<?php
include_once "../../Controller/RecommendationC.php";
include_once "../../Controller/ServiceC.php";

// Simuler un ID utilisateur (à remplacer par la session réelle)
$userId = 1;

$recommendationC = new RecommendationC();
$serviceC = new ServiceC();

// Récupérer les recommandations personnalisées
$recommendedServices = $recommendationC->getServicesWithDiscount($userId);
$priceRange = $recommendationC->getMinMaxPrices($userId);
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
                                    <th>Score de Recommandation</th>
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
                                        <td><?= $service['recommendation_score'] ?? 'N/A' ?></td>
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
