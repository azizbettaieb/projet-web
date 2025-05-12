<?php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/Service.php';

class ServiceC
{
    // Ajouter un service
    public function ajouterService($service) {
        $sql = "INSERT INTO Services (service_name, service_description, price, eco_friendly, id_categorie) 
                VALUES (:service_name, :service_description, :price, :eco_friendly, :id_categorie)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'service_name' => $service->getServiceName(),
                'service_description' => $service->getServiceDescription(),
                'price' => $service->getPrice(),
                'eco_friendly' => $service->getEcoFriendly(),
                'id_categorie' => $service->getIdCategorie()
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }

    // Afficher tous les services
    public function afficherServices()
    {
        $sql = "SELECT s.*, c.nom_categorie 
                FROM Services s
                LEFT JOIN categoryServices c ON s.id_categorie = c.id_categorie";
        $db = config::getConnexion();
        try {
            $liste = $db->query($sql);
            return $liste->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    // Supprimer un service
    public function supprimerService($id)
    {
        $sql = "DELETE FROM Services WHERE id_service = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id', $id);
            $query->execute();
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    // Récupérer un service par ID
    public function recupererService($id) {
        $sql = "SELECT * FROM Services WHERE id_service = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $row = $query->fetch(PDO::FETCH_ASSOC);
            
            // Log the retrieved service details
            error_log("Retrieving Service ID: $id");
            error_log("Retrieved Service: " . print_r($row, true));

            return $row ? $row : null;
        } catch (Exception $e) {
            error_log('Service Retrieval Error: ' . $e->getMessage());
            return null;
        }
    }

    // Modifier un service
    public function modifierService($service, $id) {
        $sql = "UPDATE Services SET 
                    service_name = :service_name,
                    service_description = :service_description,
                    price = :price,
                    eco_friendly = :eco_friendly,
                    id_categorie = :id_categorie
                WHERE id_service = :id";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'service_name' => $service->getServiceName(),
                'service_description' => $service->getServiceDescription(),
                'price' => $service->getPrice(),
                'eco_friendly' => $service->getEcoFriendly(),
                'id_categorie' => $service->getIdCategorie(),
                'id' => $id
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
            return false;
        }
    }

    // Récupérer tous les services avec leur catégorie
    public function getServicesWithCategorie() {
        $sql = "SELECT s.*, c.nom_categorie 
                FROM Services s
                JOIN categoryServices c ON s.id_categorie = c.id_categorie";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    // Récupérer les services par catégorie
    public function getServicesByCategorie($idCategorie) {
        $sql = "SELECT s.*, c.nom_categorie 
                FROM Services s
                JOIN categoryServices c ON s.id_categorie = c.id_categorie
                WHERE s.id_categorie = :idCategorie";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['idCategorie' => $idCategorie]);
            $services = $query->fetchAll(PDO::FETCH_ASSOC);
            
            // Log if no services found
            if (empty($services)) {
                error_log("Aucun service trouvé pour la catégorie ID: $idCategorie");
            }
            
            return $services;
        } catch (Exception $e) {
            error_log('Erreur getServicesByCategorie: ' . $e->getMessage());
            return [];
        }
    }

    // Récupérer le nombre de services par catégorie
    public function getServiceCountByCategory() {
        $sql = "SELECT c.id_categorie, c.nom_categorie, COUNT(s.id_service) as service_count
                FROM categoryServices c
                LEFT JOIN Services s ON c.id_categorie = s.id_categorie
                GROUP BY c.id_categorie, c.nom_categorie
                ORDER BY service_count DESC";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Erreur getServiceCountByCategory: ' . $e->getMessage());
            return [];
        }
    }
}
?>
