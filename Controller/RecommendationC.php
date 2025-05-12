<?php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/Recommendation.php';
include_once __DIR__ . '/../model/Service.php';

class RecommendationC
{
    private $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
        try {
            // Use the config class to get database connection
            $this->pdo = config::getConnexion();
        } catch (Exception $e) {
            // Log or handle the connection error
            error_log("Database Connection Error: " . $e->getMessage());
            throw new Exception("Unable to connect to the database");
        }
    }

    // Ajouter une recommandation
    public function ajouterRecommandation($recommendation) {
        // Validate input
        if (!$recommendation->getIdService()) {
            error_log("Invalid Recommendation: Missing Service ID");
            return false;
        }

        if (!$recommendation->getIdUser()) {
            error_log("Invalid Recommendation: Missing User ID");
            return false;
        }

        if ($recommendation->getDiscountedPrice() === null || $recommendation->getDiscountedPrice() <= 0) {
            error_log("Invalid Recommendation: Invalid Discounted Price");
            return false;
        }

        // First, check if a recommendation already exists
        $checkSql = "SELECT COUNT(*) FROM User_Service_Recommendation 
                     WHERE id_service = :id_service AND id_user = :id_user";
        try {
            $checkQuery = $this->pdo->prepare($checkSql);
            $checkQuery->execute([
                'id_service' => $recommendation->getIdService(),
                'id_user' => $recommendation->getIdUser()
            ]);
            $exists = $checkQuery->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Database Error (Check Existing): " . $e->getMessage());
            return false;
        }

        // Detailed logging
        error_log("Recommendation Attempt:");
        error_log("Service ID: " . $recommendation->getIdService());
        error_log("User ID: " . $recommendation->getIdUser());
        error_log("Discounted Price: " . $recommendation->getDiscountedPrice());
        error_log("Existing Recommendation: " . ($exists ? 'Yes' : 'No'));

        if ($exists) {
            // Update existing recommendation
            $sql = "UPDATE User_Service_Recommendation 
                    SET discounted_price = :discounted_price 
                    WHERE id_service = :id_service AND id_user = :id_user";
        } else {
            // Insert new recommendation
            $sql = "INSERT INTO User_Service_Recommendation 
                    (id_service, id_user, discounted_price) 
                    VALUES (:id_service, :id_user, :discounted_price)";
        }

        try {
            $query = $this->pdo->prepare($sql);
            $query->execute([
                'id_service' => $recommendation->getIdService(),
                'id_user' => $recommendation->getIdUser(),
                'discounted_price' => $recommendation->getDiscountedPrice()
            ]);
            
            // Verify the operation
            $affectedRows = $query->rowCount();
            if ($affectedRows > 0) {
                // Log successful recommendation
                error_log("Recommendation Successfully " . ($exists ? 'Updated' : 'Added'));
                return true;
            } else {
                error_log("No Rows Affected: Recommendation " . ($exists ? 'Update' : 'Insert') . " Failed");
                return false;
            }
        } catch (PDOException $e) {
            // Detailed error logging
            error_log('Recommendation Database Error: ' . $e->getMessage());
            error_log('SQL: ' . $sql);
            error_log('Params: ' . print_r([
                'id_service' => $recommendation->getIdService(),
                'id_user' => $recommendation->getIdUser(),
                'discounted_price' => $recommendation->getDiscountedPrice()
            ], true));
            return false;
        }
    }

    // Récupérer les recommandations pour un utilisateur
    public function getRecommendationsForUser($userId) {
        $sql = "SELECT 
                    r.id_service, 
                    r.discounted_price, 
                    r.date_recommended, 
                    s.nom_service AS service_name, 
                    s.description AS service_description, 
                    s.prix AS original_price, 
                    c.nom_categorie,
                    s.eco_friendly
                FROM User_Service_Recommendation r
                JOIN Services s ON r.id_service = s.id_service
                JOIN categoryServices c ON s.id_categorie = c.id_categorie
                WHERE r.id_user = :userId";
        try {
            $query = $this->pdo->prepare($sql);
            $query->execute(['userId' => $userId]);
            $recommendations = $query->fetchAll(PDO::FETCH_ASSOC);
            
            // Add additional processing if needed
            foreach ($recommendations as &$rec) {
                // Format date if exists
                if (!empty($rec['date_recommended'])) {
                    $rec['date_recommended'] = date('d/m/Y H:i', strtotime($rec['date_recommended']));
                }
                
                // Calculate discount percentage
                if (!empty($rec['original_price']) && !empty($rec['discounted_price'])) {
                    $rec['discount_percentage'] = round((1 - ($rec['discounted_price'] / $rec['original_price'])) * 100, 2);
                }
            }
            unset($rec);
            
            return $recommendations;
        } catch (Exception $e) {
            error_log('Recommendation Fetch Error: ' . $e->getMessage());
            return [];
        }
    }

    // Check if a specific service has a recommendation for the user
    public function checkServiceRecommendation($userId, $serviceId) {
        try {
            $query = "SELECT * FROM user_service_recommendation 
                      WHERE id_user = :userId AND id_service = :serviceId";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':serviceId', $serviceId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        } catch (Exception $e) {
            error_log('Service Recommendation Check Error: ' . $e->getMessage());
            return false;
        }
    }

    // Récupérer les services recommandés avec prix personnalisé
    public function getServicesWithDiscount($userId) {
        try {
            // Log the incoming user ID
            error_log("Fetching services for userId: $userId");

            $query = "SELECT 
                     s.id_service, 
                     s.nom_service, 
                     s.prix, 
                     s.description, 
                     s.eco_friendly, 
                     c.nom_categorie,
                     COALESCE(r.discounted_price, s.prix) as discounted_price
                     FROM services s
                     JOIN categoryservices c ON s.id_categorie = c.id_categorie
                     LEFT JOIN (
                         SELECT id_service, discounted_price 
                         FROM user_service_recommendation 
                         WHERE id_user = :userId
                     ) r ON s.id_service = r.id_service";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Log the number of results
            error_log("Services retrieved: " . count($results));
            
            // Log the first few services for debugging
            if (!empty($results)) {
                error_log("First service details: " . print_r($results[0], true));
            }

            return $results;
        } catch (PDOException $e) {
            // Log the error with more details
            error_log("Error in getServicesWithDiscount: " . $e->getMessage());
            error_log("Query: $query");
            error_log("User ID: $userId");
            return []; // Return empty array on error
        }
    }

    public function getServicesWithDiscountByCategory($userId, $category) {
        try {
            // Log input parameters
            error_log("Fetching services for userId: $userId, category: $category");

            $query = "SELECT 
                     s.id_service, 
                     s.nom_service, 
                     s.prix, 
                     s.description, 
                     s.eco_friendly, 
                     c.nom_categorie,
                     COALESCE(r.discounted_price, s.prix) as discounted_price
                     FROM services s
                     JOIN categoryservices c ON s.id_categorie = c.id_categorie
                     LEFT JOIN (
                         SELECT id_service, discounted_price 
                         FROM user_service_recommendation 
                         WHERE id_user = :userId
                     ) r ON s.id_service = r.id_service
                     WHERE c.nom_categorie = :category";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':category', $category, PDO::PARAM_STR);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Log the number of results
            error_log("Services retrieved for category $category: " . count($results));
            
            // Log the first few services for debugging
            if (!empty($results)) {
                error_log("First service details: " . print_r($results[0], true));
            }

            return $results;
        } catch (PDOException $e) {
            // Log the error with more details
            error_log("Error in getServicesWithDiscountByCategory: " . $e->getMessage());
            error_log("Query: $query");
            error_log("User ID: $userId, Category: $category");
            return []; // Return empty array on error
        }
    }
    
    // Obtenir les prix minimum et maximum
    public function getMinMaxPrices($userId) {
        $sql = "SELECT 
                    MIN(COALESCE(usr.discounted_price, s.price)) AS min_price, 
                    MAX(COALESCE(usr.discounted_price, s.price)) AS max_price
                FROM Services s
                LEFT JOIN User_Service_Recommendation usr 
                    ON usr.id_service = s.id_service AND usr.id_user = :userId";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['userId' => $userId]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }
}
?>
