<?php
require_once '../../controller/recommendationC.php';
require_once '../../controller/serviceC.php';
require_once '../../model/recommendation.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id_service"])) {
    // Validate input
    $idService = filter_input(INPUT_POST, 'id_service', FILTER_VALIDATE_INT);
    if (!$idService) {
        error_log("Invalid Service ID Attempt: " . print_r($_POST, true));
        die("Invalid service ID");
    }

    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Get current user ID from session
    $idUser = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 2;
    
    // Log user ID retrieval
    error_log('Recommendation User ID: ' . $idUser . ' (Default used if not in session)');


    $serviceC = new ServiceC();
    $service = $serviceC->recupererService($idService);

    // Log service retrieval
    error_log("Service Retrieval - ID: $idService");
    error_log("Service Retrieved: " . print_r($service, true));

    if ($service) {
        // Convert object to array if needed
        if (is_object($service)) {
            $service = get_object_vars($service);
        }

        // Try multiple possible price keys
        $originalPrice = $service['price'] ?? $service['prix'] ?? $service['Prix'] ?? null;

        // Validate price
        if ($originalPrice === null || $originalPrice <= 0) {
            error_log("Invalid price for service: " . print_r($service, true));
            error_log("Service Details: " . json_encode($service, JSON_PRETTY_PRINT));
            error_log("Available Keys: " . implode(", ", array_keys($service)));
            echo "Invalid service price.";
            exit;
        }

        // Validate service
        if (empty($service)) {
            error_log("No service found for ID: $idService");
            echo "Service not found.";
            exit;
        }

        // Try multiple possible service ID keys
        $serviceId = $service['id_service'] ?? $service['idService'] ?? $service['serviceId'] ?? $idService;
        
        // Validate service ID
        if (!$serviceId) {
            error_log("Unable to determine service ID");
            echo "Unable to process service recommendation.";
            exit;
        }

        // Ensure service ID is correctly extracted
        $serviceId = $service['id_service'] ?? $service['idService'] ?? $service['serviceId'] ?? $idService;
        
        // Validate service ID
        if (!$serviceId) {
            error_log("Cannot determine service ID");
            error_log("Service Data: " . json_encode($service, JSON_PRETTY_PRINT));
            die("Unable to process recommendation: Invalid service ID");
        }

        // Calculate discounted price
        $discountedPrice = round($originalPrice * 0.9, 2); // 10% discount

        // Log detailed recommendation information
        error_log("Recommendation Attempt Details:");
        error_log("Service ID: $serviceId");
        error_log("User ID: $idUser");
        error_log("Original Price: $originalPrice");
        error_log("Discounted Price: $discountedPrice");

        try {
            // Create recommendation object
            $recommendation = new Recommendation(null, $serviceId, $idUser);
            $recommendation->setDiscountedPrice($discountedPrice);

            // Attempt to add recommendation
            $recommendationC = new RecommendationC();
            $result = $recommendationC->ajouterRecommandation($recommendation);

            if ($result) {
                // Log successful recommendation
                error_log("Recommendation Successfully Added");
                error_log("Service ID: $serviceId");
                error_log("User ID: $idUser");
                error_log("Discounted Price: $discountedPrice");

                // Redirect to listeservices.php with success message
                header("Location: listeservices.php?recommendation_success=1&service_id=$serviceId");
                exit();
            } else {
                // Log detailed recommendation failure
                error_log("Recommendation Add Failed");
                error_log("Service ID: $serviceId");
                error_log("User ID: $idUser");
                error_log("Discounted Price: $discountedPrice");
                error_log("Service Details: " . print_r($service, true));

                // Redirect to listeservices.php with error message
                header("Location: listeservices.php?recommendation_error=add_failed&service_id=$serviceId");
                exit();
            }
        } catch (Exception $e) {
            // Catch any unexpected errors
            header("Location: listeservices.php?recommendation_error=exception&service_id=$serviceId");
            exit();
        }
    } else {
        // Redirect to listeservices.php with service not found error
        header("Location: listeservices.php?recommendation_error=service_not_found");
        exit();
    }
}

// Récupérer les recommandations personnalisées
$recommendationC = new RecommendationC();
$serviceC = new ServiceC();
$userId = $sess ['user']; // Replace with actual user ID
$recommendedServices = $recommendationC->getServicesWithDiscount($userId);
$priceRange = $recommendationC->getMinMaxPrices($userId);

// Debug logging
error_log("Recommended Services: " . print_r($recommendedServices, true));
error_log("Price Range: " . print_r($priceRange, true));
?>

<!doctype html>
<html lang="en">
<head>
    <title>Recommendations Personnalisées</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- CSS -->
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/colors/blue.css">
</head>
<body>
    <div id="wrapper">
        <header id="header-container" class="fullwidth">
            <div id="header">
                <div class="container">
                    <h1>Recommandations Personnalisées</h1>
                    
                    <!-- Plage de prix -->
                    <div>
                        <h2>Plage de Prix</h2>
                        <p>Min: <?= number_format($priceRange['min_price'], 2) ?> € | 
                           Max: <?= number_format($priceRange['max_price'], 2) ?> €</p>
                    </div>

                    <!-- Tableau des services recommandés -->
                    <?php if (empty($recommendedServices)): ?>
                        <div class="alert alert-info">
                            <p>Aucune recommandation personnalisée n'est disponible pour le moment.</p>
                            <p>Explorez nos services pour obtenir des recommandations plus précises.</p>
                        </div>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Nom du Service</th>
                                    <th>Description</th>
                                    <th>Prix Original</th>
                                    <th>Prix Recommandé</th>
                                    <th>Catégorie</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recommendedServices as $service): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($service['service_name']) ?></td>
                                        <td><?= htmlspecialchars($service['service_description']) ?></td>
                                        <td><?= number_format($service['price'], 2) ?> €</td>
                                        <td>
                                            <?= $service['discounted_price'] ? 
                                                number_format($service['discounted_price'], 2) . ' €' : 
                                                'Pas de réduction' 
                                            ?>
                                        </td>
                                        <td><?= htmlspecialchars($service['nom_categorie']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </header>
    </div>
</body>
</html>
