<?php
// Désactiver l'affichage des erreurs en production
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Démarrer la session uniquement si elle n'est pas déjà active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Effacer l'ID utilisateur de la session
$_SESSION['user_id'] = null;

// Inclure les fichiers nécessaires
require_once '../../config.php';
require_once '../../controller/serviceC.php';
require_once '../../controller/recommendationC.php';
require_once '../../controller/CategorieC.php';


// Gestion de l'utilisateur
if (!isset($_SESSION['user_id'])) {
    // Utilisateur par défaut ou redirection vers la connexion
    $_SESSION['user_id'] = 4; // ID utilisateur par défaut
}

// Récupérer l'ID utilisateur de la session
$userId = intval($_SESSION['user_id']);



$recommendationC = new RecommendationC();

$serviceC = new ServiceC();
$categorieC = new CategorieC();

// Récupérer les catégories
$categories = method_exists($categorieC, 'recupererToutesCategories') 
    ? $categorieC->recupererToutesCategories() 
    : [];

// Gestion des recommandations
$recommendationError = isset($_GET['recommendation_error']) ? $_GET['recommendation_error'] : null;
$recommendationSuccess = isset($_GET['recommendation_success']) ? true : false;
$serviceId = isset($_GET['service_id']) ? intval($_GET['service_id']) : null;

// Messages de recommandation
$recommendationMessage = null;
$recommendationMessageType = null;

if ($recommendationSuccess) {
    // Message de succès
    if ($serviceId) {
        $serviceC = new ServiceC();
        $service = $serviceC->recupererService($serviceId);
        $serviceName = $service['nom_service'] ?? $service['service_name'] ?? 'ce service';
        
        $recommendationMessage = "Recommandation pour $serviceName ajoutée avec succès !";
        $recommendationMessageType = 'success';
        
        // Log successful recommendation
        error_log("Recommendation Success");
        error_log("Service ID: $serviceId");
        error_log("Service Name: $serviceName");
        error_log("User ID: $userId");
    } else {
        $recommendationMessage = "Recommandation ajoutée avec succès !";
        $recommendationMessageType = 'success';
    }
} elseif ($recommendationError) {
    // Messages d'erreur
    switch ($recommendationError) {
        case 'add_failed':
            $recommendationMessage = "Impossible d'ajouter la recommandation. Veuillez réessayer.";
            $recommendationMessageType = 'danger';
            break;
        case 'exception':
            $recommendationMessage = "Une erreur inattendue s'est produite lors de la recommandation.";
            $recommendationMessageType = 'danger';
            break;
        default:
            $recommendationMessage = "Erreur de recommandation inconnue.";
            $recommendationMessageType = 'warning';
    }
    
    // Log detailed recommendation error information
    error_log("Recommendation Error: $recommendationError");
    error_log("Service ID: $serviceId");
    error_log("User ID: $userId");

    // Optional: Fetch service details for more context
    if ($serviceId) {
        $serviceC = new ServiceC();
        $service = $serviceC->recupererService($serviceId);
        error_log("Service Details: " . print_r($service, true));
    }
}

// Color palette for service categories
$colorPalettes = [
    'default' => [
        'bg' => 'bg-soft-primary',
        'border' => 'border-primary',
        'text' => 'text-primary'
    ]
];

// Vérifier s'il y a un filtre
if (isset($_GET['categorie']) && $_GET['categorie'] !== "") {
    // Get services by category with user-based discount
    $listServices = $recommendationC->getServicesWithDiscountByCategory($userId, $_GET['categorie']);
} else {
    // Get all services with user-based discount
    $listServices = $recommendationC->getServicesWithDiscount($userId);
}

// Debug: Check the retrieved services
error_log('Retrieved Services Count: ' . count($listServices));
error_log('Retrieved Services: ' . print_r($listServices, true));

// Fallback if no services found
if (empty($listServices)) {
    // Attempt to retrieve services directly from ServiceC
    $listServices = $serviceC->afficherServices();
    error_log('Fallback Services Count: ' . count($listServices));
}


// Search Function for Services
function searchServices($services, $searchTerm = '') {
    // Extensive logging
    error_log('DEBUG: Search Function Start');
    error_log('DEBUG: Search Term: ' . $searchTerm);
    error_log('DEBUG: Total Services Input: ' . count($services));
    
    // If no search term, return all services
    if (empty($searchTerm)) {
        error_log('DEBUG: No search term, returning all services');
        return $services;
    }
    
    // Convert search term to lowercase for case-insensitive search
    $searchTerm = mb_strtolower(trim($searchTerm));
    
    // Validate services input
    if (!is_array($services)) {
        error_log('DEBUG: Services is not an array');
        return [];
    }
    
    // Perform search
    $filteredServices = [];
    foreach ($services as $index => $service) {
        // Ensure service is an array
        if (!is_array($service)) {
            error_log("DEBUG: Non-array service at index $index");
            continue;
        }
        
        // Try multiple possible keys for service name
        $serviceNameKeys = ['nom_service', 'service_name', 'name', 'title'];
        $matchFound = false;
        
        foreach ($serviceNameKeys as $key) {
            if (!isset($service[$key])) {
                continue;
            }
            
            // Safely get and process service name
            $nomService = mb_strtolower(trim($service[$key]));
            
            // Log detailed matching information
            error_log("DEBUG: Checking $key: $nomService");
            
            if (strpos($nomService, $searchTerm) !== false) {
                error_log("DEBUG: Match found in $key");
                $filteredServices[] = $service;
                $matchFound = true;
                break;
            }
        }
        
        // If no match found, try description
        if (!$matchFound && isset($service['description'])) {
            $description = mb_strtolower(trim($service['description']));
            
            if (strpos($description, $searchTerm) !== false) {
                error_log('DEBUG: Match found in description');
                $filteredServices[] = $service;
            }
        }
    }
    
    // Log search results
    error_log('DEBUG: Filtered Services Count: ' . count($filteredServices));
    
    return $filteredServices;
}

// Sorting Function for Services
function sortServices($services, $sortBy = '', $sortOrder = 'asc') {
    if (empty($services) || $sortBy !== 'price') {
        return $services;
    }
    
    // Possible price keys
    $priceKeys = ['prix', 'price', 'original_price'];
    
    // Find a valid price key
    $priceKey = null;
    foreach ($priceKeys as $key) {
        if (isset($services[0][$key])) {
            $priceKey = $key;
            break;
        }
    }
    
    // Fallback if no price key found
    if (!$priceKey) {
        return $services;
    }
    
    // Sort the services by price
    usort($services, function($a, $b) use ($priceKey, $sortOrder) {
        $valA = floatval($a[$priceKey] ?? 0);
        $valB = floatval($b[$priceKey] ?? 0);
        
        return ($sortOrder === 'asc') ? ($valA <=> $valB) : ($valB <=> $valA);
    });
    
    return $services;
}

// Ecological Filtering Function
function filterServicesByEcology($services, $type = 'all') {
    if ($type === 'all') {
        return $services;
    }
    
    return array_filter($services, function($service) use ($type) {
        $isEcoFriendly = boolval($service['eco_friendly'] ?? false);
        return ($type === 'eco') ? $isEcoFriendly : !$isEcoFriendly;
    });
}

// Category Filtering Function
function filterServicesByCategory($services, $categoryId = null) {
    if (empty($categoryId)) {
        return $services;
    }
    
    return array_filter($services, function($service) use ($categoryId) {
        // Check if the service's category matches the selected category
        // Assuming category is stored in 'nom_categorie' or 'id_categorie'
        return 
            (isset($service['id_categorie']) && $service['id_categorie'] == $categoryId) ||
            (isset($service['nom_categorie']) && $service['nom_categorie'] == $categoryId);
    });
}

// Dynamic Pagination Setup
$itemsPerPage = 4;
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$currentEcoFilter = isset($_GET['eco']) ? $_GET['eco'] : 'all';
$currentCategoryFilter = isset($_GET['category']) ? trim($_GET['category']) : null;

// Search Filtering
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

// Detailed logging of input
error_log('DEBUG: Search GET Parameter: ' . print_r($_GET, true));

// Ensure services are loaded before searching
if (empty($listServices)) {
    // Fallback to retrieving all services if list is empty
    $listServices = $serviceC->afficherServices();
    error_log('DEBUG: Services retrieved via afficherServices(): ' . count($listServices));
}

// Log initial services
error_log('DEBUG: Initial Services Count: ' . count($listServices));
if (!empty($listServices)) {
    $firstService = reset($listServices);
    error_log('DEBUG: First Service Keys: ' . implode(', ', array_keys($firstService)));
}

// Perform search
if (!empty($searchTerm)) {
    error_log('DEBUG: Performing search with term: ' . $searchTerm);
    $listServices = searchServices($listServices, $searchTerm);
}

// Log final search results
error_log('DEBUG: Search Term: ' . $searchTerm);
error_log('DEBUG: Services After Search: ' . count($listServices));

// Ensure we have services to display
if (empty($listServices)) {
    error_log('DEBUG: No services found after search');
    // Fallback to all services if search returns nothing
    $listServices = $serviceC->afficherServices();
    error_log('DEBUG: Fallback Services Count: ' . count($listServices));
}

// Sorting Filtering
$sortBy = isset($_GET['sort']) ? trim($_GET['sort']) : '';
$sortOrder = 'asc';

// Translate old keys to new keys
$keyTranslations = [
    'prix' => 'price'
];

// Handle new sort format with explicit order
if (strpos($sortBy, '_') !== false) {
    $parts = explode('_', $sortBy);
    $sortBy = $parts[0];
    $sortOrder = $parts[1];
} elseif (isset($keyTranslations[$sortBy])) {
    $sortBy = $keyTranslations[$sortBy];
}

// Only allow price sorting
if ($sortBy !== 'price') {
    $sortBy = '';
}

// Apply sorting if needed
if (!empty($sortBy)) {
    $listServices = sortServices($listServices, $sortBy, $sortOrder);
}

// Normalize service data to ensure all required keys exist
$listServices = array_map(function($service) {
    // Convert object to array if it's an object
    if (is_object($service)) {
        $service = get_object_vars($service);
    }
    
    // Default values for all potentially missing keys
    $defaultService = [
        'id_service' => $service['id_service'] ?? $service['idService'] ?? null,
        'nom_service' => trim($service['nom_service'] ?? $service['nomService'] ?? $service['service_name']),
        'prix' => round(floatval($service['prix'] ?? $service['price'] ?? 0.00), 2),
        'description' => trim($service['description'] ?? $service['serviceDescription'] ?? 'Aucune description'),
        'eco_friendly' => boolval($service['eco_friendly'] ?? $service['ecoFriendly'] ?? false),
        'nom_categorie' => trim($service['nom_categorie'] ?? $service['nomCategorie'] ?? 'Non catégorisé'),
        'discounted_price' => null
    ];
    return $defaultService;
}, $listServices);

// First, filter services based on ecological status
$filteredServices = filterServicesByEcology($listServices, $currentEcoFilter);

// Then filter services by category
$filteredServices = filterServicesByCategory($filteredServices, $currentCategoryFilter);

// Calculate pagination based on filtered services
$totalItems = count($filteredServices);
$totalPages = max(1, ceil($totalItems / $itemsPerPage));

// Ensure current page is within valid range
$currentPage = min($currentPage, $totalPages);

// Slice the array for current page
$startIndex = ($currentPage - 1) * $itemsPerPage;
$currentPageServices = array_slice($filteredServices, $startIndex, $itemsPerPage);

// Vérifier si un utilisateur est connecté
$userLoggedIn = isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;

// Get recommended services for the current user
$recommendedServices = [];
$userRecommendations = [];
$recommendedServicesMap = [];

if ($userLoggedIn) {
    $recommendedServices = $recommendationC->getServicesWithDiscount($userId);
    $userRecommendations = $recommendationC->getRecommendationsForUser($userId);

    // Create a map of recommended services
    foreach ($userRecommendations as $rec) {
        $recommendedServicesMap[$rec['id_service']] = $rec;
    }
} else {
    // Log that no user is logged in
    error_log("Aucun utilisateur connecté pour les recommandations");
}

// Calculate discounted prices for services
foreach ($currentPageServices as &$service) {
    // Ensure prix is a valid numeric value
    $prix = floatval($service['prix'] ?? 0);
    
    // Check if this service is recommended
    if (isset($recommendedServicesMap[$service['id_service']])) {
        $recommendation = $recommendedServicesMap[$service['id_service']];
        
        // Use the discounted price from recommendations
        $service['discounted_price'] = $recommendation['discounted_price'] ?? $prix;
        $service['original_price'] = $prix;
        $service['is_recommended'] = true;
        $service['recommendation_details'] = $recommendation;
    } else {
        // Calculate a default discount (optional)
        $discountedPrice = round($prix * 0.95, 2); // 5% default discount
        
        $service['discounted_price'] = $discountedPrice;
        $service['original_price'] = $prix;
        $service['is_recommended'] = false;
        $service['recommendation_details'] = null;
    }
    
    $service['prix'] = $prix;
}
unset($service); // Break the reference
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Basic Page Needs
    ================================================== -->
    <title>Liste des Services</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- CSS
    ================================================== -->
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/colors/blue.css">
    <style>
        .notification {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-weight: 500;
        }
        
        .success-notification {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error-notification {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .recommendation-info {
            margin: 10px 0;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .task-listing-price {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .original-price {
            color: #6c757d;
            margin-right: 15px;
        }
        
        .discounted-price {
            color: #28a745;
            font-weight: bold;
            font-size: 1.1em;
        }
        
        .recommend-btn {
            margin-top: 10px;
        }
        
        .recommendation-badge {
            background-color: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8em;
            margin-left: 10px;
            vertical-align: middle;
        }
        
        .recommendation-details {
            display: block;
            margin-top: 5px;
            color: #6c757d;
            font-size: 0.7em;
            font-style: italic;
        }
        .login-recommendation {
            color: #6c757d;
            font-style: italic;
            display: block;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <!-- Wrapper -->
    <div id="wrapper">
        <!-- Header Container
        ================================================== -->
        <header id="header-container" class="fullwidth">
            <!-- Header -->
            <div id="header">
                <div class="container">
                    <!-- Left Side Content -->
                    <div class="left-side">
                        <!-- Logo -->
                        <div id="logo">
                            <a href="index.html"><img src="images/logomentoriel.png" alt=""></a>
                        </div>

                        <!-- Main Navigation -->
                        <nav id="navigation">
                            <ul id="responsive">
                                <li><a href="#" class="current">Accueil</a>
                                    <ul>
                                    </ul>
                                </li>

                                <li><a href="#">Parcourir</a>
                                    <ul class="dropdown-nav">
                                        <li><a href="liste-formations.html">Formations</a>
                                            <ul class="dropdown-nav">
                                                <li><a href="liste-formations.html">Parcourir Formations</a></li>
                                                <li><a href="#">Ajouter une formation</a></li>
                                                <li><a href="#">Gérer vos formations</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="liste-entreprises.html">Entreprises</a>
                                            <ul class="dropdown-nav">
                                                <li><a href="#">Parcourir les Entreprises</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="liste-seances.html">Séances</a>
                                            <ul class="dropdown-nav">
                                                <li><a href="liste-seances.html">Parcourir les séance</a></li>
                                                <li><a href="#">Ajouter une séance</a></li>
                                                <li><a href="#">Gérer vos séances</a></li>
                                            </ul>
                                        </li>

                                        <li><a href="liste-services.html">Services</a>
                                            <ul class="dropdown-nav">
                                                <li><a href="liste-services.html">Parcourir les services</a></li>
                                                <li><a href="#">Ajouter un service</a></li>
                                                <li><a href="#">Gérer vos services</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="liste-entrepreneurs.html">Entrepreneurs</a>
                                            <ul class="dropdown-nav">
                                                <li><a href="liste-entrepreneurs.html">Liste des entrepreneurs</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>

                                <li><a href="listeservices.php">Services</a></li>
                                <li><a href="listetransport.php">Transports</a></li>
                                <li><a href="dashboard-settings.html">Paramètres</a>
                                    <ul class="dropdown-nav">
                                    </ul>
                                </li>
                                <li><a href="dashboard.html">Tableau de Bord</a>
                                    <ul class="dropdown-nav"></ul>
                            </ul>
                        </nav>
                        <div class="clearfix"></div>
                    </div>
                    <!-- Left Side Content / End -->


                    <!-- Right Side Content / End -->
                    <div class="right-side">
                        <!--  User Notifications -->
                        <div class="header-widget hide-on-mobile">

                            <!-- Notifications -->
                            <div class="header-notifications">

                                <!-- Trigger -->
                                <div class="header-notifications-trigger">
                                    <a href="#"><i class="icon-feather-bell"></i><span>4</span></a>
                                </div>

                                <!-- Dropdown -->
                                <div class="header-notifications-dropdown">

                                    <div class="header-notifications-headline">
                                        <h4>Notifications</h4>
                                        <button class="mark-as-read ripple-effect-dark" title="Mark all as read"
                                            data-tippy-placement="left">
                                            <i class="icon-feather-check-square"></i>
                                        </button>
                                    </div>

                                    <div class="header-notifications-content">
                                        <div class="header-notifications-scroll" data-simplebar>
                                            <ul>
                                                <!-- Notification Placeholder -->
                                                <li class="notifications-not-read">
                                                    <a href="#">
                                                        <span class="notification-icon"><i
                                                                class="icon-material-outline-group"></i></span>
                                                        <span class="notification-text">
                                                            Aucune notification
                                                        </span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <!-- Messages -->
                            <div class="header-notifications">
                                <div class="header-notifications-trigger">
                                    <a href="#"><i class="icon-feather-mail"></i><span>0</span></a>
                                </div>

                                <!-- Dropdown -->
                                <div class="header-notifications-dropdown">

                                    <div class="header-notifications-headline">
                                        <h4>Messages</h4>
                                        <button class="mark-as-read ripple-effect-dark" title="Mark all as read"
                                            data-tippy-placement="left">
                                            <i class="icon-feather-check-square"></i>
                                        </button>
                                    </div>

                                    <div class="header-notifications-content">
                                        <div class="header-notifications-scroll" data-simplebar>
                                            <ul>
                                                <!-- Message Placeholder -->
                                                <li class="notifications-not-read">
                                                    <a href="#">
                                                        <span class="notification-text">
                                                            Aucun message
                                                        </span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!--  User Notifications / End -->

                        <!-- User Menu -->
                        <div class="header-widget">
                            <div class="user-menu-container">
                                <div class="user-menu">
                                    <div class="user-menu-dropdown-trigger" onclick="toggleUserMenu()">
                                        <a href="#"><div class="user-avatar status-online"><img src="images/user-avatar-small-01.png" alt=""></div></a>
                                    </div>

                                    <!-- Dropdown -->
                                    <div class="user-menu-dropdown" id="userMenuDropdown">
                                        <div class="user-menu-dropdown-inner">
                                            <ul>
                                                <li><a href="dashboard-settings.php"><i class="icon-material-outline-settings"></i> Paramètres</a></li>
                                                <li><a href="dashboard.php"><i class="icon-material-outline-dashboard"></i> Tableau de Bord</a></li>
                                                <li><a href="logout.php"><i class="icon-material-outline-power-settings-new"></i> Déconnexion</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                        function toggleUserMenu() {
                            var dropdown = document.getElementById('userMenuDropdown');
                            dropdown.classList.toggle('visible');
                        }

                        // Close dropdown when clicking outside
                        document.addEventListener('click', function(event) {
                            var container = document.querySelector('.user-menu-container');
                            var dropdown = document.getElementById('userMenuDropdown');
                            
                            if (!container.contains(event.target)) {
                                dropdown.classList.remove('visible');
                            }
                        });
                        </script>

                        <style>
                        .user-menu-dropdown {
                            display: none;
                            opacity: 0;
                            visibility: hidden;
                            transform: translateY(-10px);
                            transition: all 0.3s ease;
                            position: absolute;
                            right: 0;
                            top: 100%;
                            background: white;
                            border: 1px solid #e0e0e0;
                            border-radius: 4px;
                            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                            z-index: 1000;
                            min-width: 200px;
                        }

                        .user-menu-dropdown.visible {
                            display: block;
                            opacity: 1;
                            visibility: visible;
                            transform: translateY(0);
                        }

                        .user-menu-dropdown ul {
                            list-style: none;
                            padding: 0;
                            margin: 0;
                        }

                        .user-menu-dropdown ul li a {
                            display: block;
                            padding: 10px 15px;
                            color: #333;
                            text-decoration: none;
                            transition: background-color 0.2s;
                        }

                        .user-menu-dropdown ul li a:hover {
                            background-color: #f5f5f5;
                        }
                        </style>
                        <!-- User Menu / End -->
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <!-- Header / End -->
        </header>
        <div class="clearfix"></div>
        <!-- Header Container / End -->

        <!-- Spacer -->
        <div class="margin-top-90"></div>

        <!-- Page Content
        ================================================== -->
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-4">
                    <div class="sidebar-container">
                        <!-- Search -->
                        <form method="GET" action="listeservices.php">
                            <div class="sidebar-widget">
                                <h3>Recherche</h3>
                                <div class="input-group mb-3">
                                    <input type="text" name="search" class="form-control" placeholder="Rechercher des services" 
                                           value="<?= htmlspecialchars($searchTerm) ?>">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="icon-feather-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Category Dropdown -->
                            <div class="sidebar-widget">
                                <h3>Catégories de Services</h3>
                                <select name="category" class="form-control" onchange="this.form.submit()">
                                    <option value="">Toutes les catégories</option>
                                    <?php 
                                    // Fetch unique categories from services
                                    $categories = array_unique(array_column($listServices, 'nom_categorie'));
                                    foreach ($categories as $category): 
                                        $selected = ($currentCategoryFilter === $category) ? 'selected' : '';
                                    ?>
                                        <option value="<?= htmlspecialchars($category) ?>" <?= $selected ?>>
                                            <?= htmlspecialchars($category) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Ecological Filter -->
                            <div class="sidebar-widget">
                                <h3>Statut Écologique</h3>
                                <select name="eco" class="form-control" onchange="this.form.submit()">
                                    <option value="all" <?= ($currentEcoFilter == 'all') ? 'selected' : '' ?>>Tous les services</option>
                                    <option value="eco" <?= ($currentEcoFilter == 'eco') ? 'selected' : '' ?>>Services Écologiques</option>
                                    <option value="non-eco" <?= ($currentEcoFilter == 'non-eco') ? 'selected' : '' ?>>Services Non-Écologiques</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-xl-9 col-lg-8 content-left-offset">
                    <h3 class="page-title">Liste des Services</h3>

                    <?php 
                    // Display recommendation messages
                    if ($recommendationMessage) {
                        $alertClass = 'alert-' . ($recommendationMessageType ?? 'info');
                        echo "<div class='alert $alertClass alert-dismissible fade show' role='alert'>";
                        echo htmlspecialchars($recommendationMessage);
                        echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                        echo '<span aria-hidden="true">&times;</span>';
                        echo '</button>';
                        echo '</div>';
                    }
                    ?>

                    <div class="freelancers-container freelancers-list-layout compact-list margin-top-35">
                        <?php foreach($currentPageServices as $service): 
                            // Normalize service array to ensure all expected keys exist
                            $service = array_merge([
                                'id_service' => '',
                                'nom_service' => $service['nom_service'] ?? $service['service_name'] ,
                                'prix' => $service['prix'] ?? $service['price'] ?? 0,
                                'description' => $service['description'] ?? $service['serviceDescription'] ?? 'Aucune description',
                                'eco_friendly' => $service['eco_friendly'] ?? $service['ecoFriendly'] ?? false,
                                'nom_categorie' => $service['nom_categorie'] ?? $service['nomCategorie'] ?? 'Non catégorisé',
                                'recommendation_score' => 0,
                                'discounted_price' => null
                            ], $service);

                            $colorPalette = $colorPalettes[$service['nom_categorie']] ?? $colorPalettes['default'];
                        ?>
                            <div class="task-listing">
                                <div class="task-listing-details">
                                    <div class="task-listing-description">
                                        <h3 class="task-listing-title"><?= htmlspecialchars(trim($service['nom_service'])) ?></h3>
                                        <div class="task-listing-price">
                                            <?php if (!$userLoggedIn): ?>
                                                <span class="login-recommendation">
                                                    <small>Connectez-vous pour voir les recommandations personnalisées</small>
                                                </span>
                                            <?php elseif ($service['is_recommended']): ?>
                                                <span class="original-price">Prix original: <?= number_format($service['original_price'], 2) ?> €</span>
                                                <span class="discounted-price">Prix recommandé: <?= number_format($service['discounted_price'], 2) ?> €</span>
                                                <span class="recommendation-badge">Recommandé</span>
                                                <?php if (!empty($service['recommendation_details'])): ?>
                                                    <div class="recommendation-details">
                                                        <small>
                                                            <?php 
                                                            $details = $service['recommendation_details'];
                                                            $dateRecommended = htmlspecialchars($details['date_recommended'] ?? 'Date inconnue');
                                                            $discountPercentage = !empty($details['discount_percentage']) ? $details['discount_percentage'] : null;
                                                            
                                                            echo "Recommandé le: $dateRecommended";
                                                            
                                                            if ($discountPercentage !== null) {
                                                                echo " | Réduction: $discountPercentage%";
                                                            }
                                                            ?>
                                                        </small>
                                                    </div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="original-price">Prix: <?= number_format($service['original_price'], 2) ?> €</span>
                                            <?php endif; ?>
                                        </div>
                                        <ul class="task-icons">
                                            <li><i class="icon-material-outline-business"></i> Catégorie: 
                                                <?= htmlspecialchars($service['nom_categorie']) ?></li>
                                            <li><i class="icon-material-outline-local-offer"></i> Type: 
                                                <?= $service['eco_friendly'] ? 'Eco' : 'Non-eco' ?></li>
                                        </ul>
                                        
                                        <div class="task-listing-footer">
                                            <div class="task-listing-description">
                                                <p><?= htmlspecialchars($service['description']) ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="task-listing-bid">
                                        <div class="task-listing-bid-inner">
                                            <div class="task-offers">
                                                <strong><?= $service['eco_friendly'] ? 'Eco' : 'Non-eco' ?></strong>
                                                <span>Type</span>
                                            </div>
                                            
                                            <!-- Recommend Button -->
                                            <form method="POST" action="recommend_service.php" class="recommend-btn">
                                                <input type="hidden" name="id_service" value="<?= $service['id_service'] ?>">
                                                <button type="submit" class="button button-sliding-icon ripple-effect">
                                                    Recommander <i class="icon-material-outline-thumb-up"></i>
                                                </button>
                                            </form>
                                            
                                            <a href="single-service-page.php?id=<?= $service['id_service'] ?>" class="button button-sliding-icon ripple-effect">
                                                Voir détails <i class="icon-material-outline-arrow-right-alt"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php
                    // Dynamic Pagination Links
                    if ($totalPages > 1) {
                        echo '<div class="pagination-container margin-top-40 margin-bottom-60"><nav class="pagination"><ul>';
                        
                        // Determine page range
                        $maxPagesToShow = 5;
                        $halfMax = floor($maxPagesToShow / 2);
                        $startPage = max(1, $currentPage - $halfMax);
                        $endPage = min($totalPages, $startPage + $maxPagesToShow - 1);
                        
                        // Adjust start page if we're near the end
                        if ($endPage - $startPage + 1 < $maxPagesToShow) {
                            $startPage = max(1, $endPage - $maxPagesToShow + 1);
                        }
                        
                        // Previous link
                        if ($currentPage > 1) {
                            echo '<li class="pagination-arrow"><a href="?page=' . ($currentPage - 1) . '&eco=' . $currentEcoFilter . '&search=' . urlencode($searchTerm) . '&sort=' . urlencode($sortBy) . '&order=' . urlencode($sortOrder) . '" class="ripple-effect"><i class="icon-material-outline-keyboard-arrow-left"></i></a></li>';
                        }

                        // First page and ellipsis
                        if ($startPage > 1) {
                            echo '<li><a href="?page=1&eco=' . $currentEcoFilter . '&search=' . urlencode($searchTerm) . '&sort=' . urlencode($sortBy) . '&order=' . urlencode($sortOrder) . '" class="ripple-effect">1</a></li>';
                            if ($startPage > 2) {
                                echo '<li class="disabled"><span>...</span></li>';
                            }
                        }

                        // Page numbers
                        for ($i = $startPage; $i <= $endPage; $i++) {
                            $activeClass = ($i == $currentPage) ? 'current-page' : '';
                            echo '<li><a href="?page=' . $i . '&eco=' . $currentEcoFilter . '&search=' . urlencode($searchTerm) . '&sort=' . urlencode($sortBy) . '&order=' . urlencode($sortOrder) . '" class="ripple-effect ' . $activeClass . '">' . $i . '</a></li>';
                        }

                        // Last page and ellipsis
                        if ($endPage < $totalPages) {
                            if ($endPage < $totalPages - 1) {
                                echo '<li class="disabled"><span>...</span></li>';
                            }
                            echo '<li><a href="?page=' . $totalPages . '&eco=' . $currentEcoFilter . '&search=' . urlencode($searchTerm) . '&sort=' . urlencode($sortBy) . '&order=' . urlencode($sortOrder) . '" class="ripple-effect">' . $totalPages . '</a></li>';
                        }

                        // Next link
                        if ($currentPage < $totalPages) {
                            echo '<li class="pagination-arrow"><a href="?page=' . ($currentPage + 1) . '&eco=' . $currentEcoFilter . '&search=' . urlencode($searchTerm) . '&sort=' . urlencode($sortBy) . '&order=' . urlencode($sortOrder) . '" class="ripple-effect"><i class="icon-material-outline-keyboard-arrow-right"></i></a></li>';
                        }

                        echo '</ul></nav></div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>