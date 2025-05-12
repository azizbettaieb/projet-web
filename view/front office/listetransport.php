<?php
include_once "../../Controller/transportC.php";
include_once "../../Controller/CategoryTransportC.php";

$transportC = new TransportC();
$categoryTransportC = new CategoryTransportC();
$categories = $categoryTransportC->afficherCategories();

// Color palette for transport categories
$colorPalettes = [
    'default' => [
        'bg' => 'bg-soft-primary',
        'border' => 'border-primary',
        'text' => 'text-primary'
    ]
];

// Sorting Function for Transports
function sortTransports($transports, $sortBy = '', $sortOrder = 'asc') {
    if (empty($sortBy)) {
        return $transports;
    }
    
    // Allowed sorting columns
    $allowedColumns = ['cout_moyen', 'capacite', 'frequence'];
    
    // Validate sort column
    if (!in_array($sortBy, $allowedColumns)) {
        return $transports;
    }
    
    // Sort the transports
    usort($transports, function($a, $b) use ($sortBy, $sortOrder) {
        // Convert to numeric for numeric columns
        $valA = is_numeric($a[$sortBy]) ? floatval($a[$sortBy]) : $a[$sortBy];
        $valB = is_numeric($b[$sortBy]) ? floatval($b[$sortBy]) : $b[$sortBy];
        
        // Compare based on sort order
        if ($sortOrder === 'asc') {
            return $valA <=> $valB;
        } else {
            return $valB <=> $valA;
        }
    });
    
    return $transports;
}

// Search Function for Transports
function searchTransports($transports, $searchTerm = '') {
    if (empty($searchTerm)) {
        return $transports;
    }
    
    // Convert search term to lowercase for case-insensitive search
    $searchTerm = mb_strtolower($searchTerm);
    
    return array_filter($transports, function($transport) use ($searchTerm) {
        // Search across multiple fields
        $searchFields = [
            'nom_transport',     // Transport name
            'zone_deservie',     // Served zone
            'capacite',          // Capacity
            'frequence',         // Frequency
            'cout_moyen'         // Average cost
        ];
        
        foreach ($searchFields as $field) {
            // Convert field value to lowercase for case-insensitive search
            $fieldValue = mb_strtolower($transport[$field]);
            
            // Check if search term is in the field value
            if (strpos($fieldValue, $searchTerm) !== false) {
                return true;
            }
        }
        
        return false;
    });
}

// Vérifier s'il y a un filtre de catégorie
if (isset($_GET['categorie']) && $_GET['categorie'] !== "") {
    $listTransports = $transportC->getTransportsByCategorie($_GET['categorie']);
} else {
    $listTransports = $transportC->afficherTransports();
}

// Search Filtering
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$listTransports = searchTransports($listTransports, $searchTerm);

// Sorting Filtering
$sortBy = isset($_GET['sort']) ? trim($_GET['sort']) : '';
$sortOrder = isset($_GET['order']) && in_array(strtolower($_GET['order']), ['asc', 'desc']) ? strtolower($_GET['order']) : 'asc';
$listTransports = sortTransports($listTransports, $sortBy, $sortOrder);

// Ecological Filtering Function
function filterTransportsByEcology($transports, $type = 'all') {
    switch ($type) {
        case 'eco':
            return array_filter($transports, function($transport) {
                return $transport['ecologique'] == 1;
            });
        case 'non-eco':
            return array_filter($transports, function($transport) {
                return $transport['ecologique'] == 0;
            });
        default:
            return $transports;
    }
}

// Ecological Level Filtering Function
function filterTransportsByEcologicalLevel($transports, $level = 'all') {
    switch ($level) {
        case 'eco':
            return array_filter($transports, function($transport) {
                return $transport['ecologique'] == 1;
            });
        case 'non-eco':
            return array_filter($transports, function($transport) {
                return $transport['ecologique'] == 0;
            });
        default:
            return $transports;
    }
}

// Dynamic Pagination Setup
$itemsPerPage = 4;
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$currentEcoFilter = isset($_GET['eco']) ? $_GET['eco'] : 'all';
$currentEcoLevelFilter = isset($_GET['eco_level']) ? $_GET['eco_level'] : 'all';

// First, filter transports based on ecological status
$filteredTransports = filterTransportsByEcology($listTransports, $currentEcoFilter);

// Then, filter by ecological level
$filteredTransports = filterTransportsByEcologicalLevel($filteredTransports, $currentEcoLevelFilter);

// Then calculate pagination based on filtered transports
$totalItems = count($filteredTransports);
$totalPages = max(1, ceil($totalItems / $itemsPerPage));

// Ensure current page is within valid range
$currentPage = min($currentPage, $totalPages);

// Slice the array for current page
$startIndex = ($currentPage - 1) * $itemsPerPage;
$currentPageTransports = array_slice($filteredTransports, $startIndex, $itemsPerPage);
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Basic Page Needs
    ================================================== -->
    <title>Liste des Transports</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- CSS
    ================================================== -->
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/colors/blue.css">
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
                    </div>
                </div>
            </div>
            <!-- Header / End -->
        </header>
        <div class="clearfix"></div>
        <!-- Header Container / End -->

        <!-- Spacer -->
        <div class="margin-top-90"></div>

        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-4">
                    <div class="sidebar-container">
                        <!-- Category Dropdown -->
                        <form method="GET" action="listetransport.php">
                            <div class="sidebar-widget">
                                <h3>Recherche</h3>
                                <div class="input-group mb-3">
                                    <input type="text" name="search" class="form-control" placeholder="Rechercher des transports" 
                                           value="<?= htmlspecialchars($searchTerm) ?>">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="icon-feather-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="sidebar-widget">
                                <h3>Catégories</h3>
                                <select name="categorie" class="form-control" onchange="this.form.submit()">
                                    <option value="">Toutes les catégories</option>
                                    <?php foreach($categories as $categorie): ?>
                                        <option value="<?= $categorie['id_categorie'] ?>" 
                                            <?= (isset($_GET['categorie']) && $_GET['categorie'] == $categorie['id_categorie']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($categorie['nom_categorie']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="sidebar-widget">
                                <h3>Statut Écologique</h3>
                                <select name="eco_level" class="form-control" onchange="this.form.submit()">
                                    <option value="all" <?= ($currentEcoLevelFilter == 'all') ? 'selected' : '' ?>>Tous les transports</option>
                                    <option value="eco" <?= ($currentEcoLevelFilter == 'eco') ? 'selected' : '' ?>>Transports Écologiques</option>
                                    <option value="non-eco" <?= ($currentEcoLevelFilter == 'non-eco') ? 'selected' : '' ?>>Transports Non-Écologiques</option>
                                </select>
                            </div>

                            <div class="sidebar-widget">
                                <h3>Trier par</h3>
                                <select name="sort" class="form-control" onchange="this.form.submit()">
                                    <option value="">Trier par</option>
                                    <option value="cout_moyen" <?= ($sortBy == 'cout_moyen' && $sortOrder == 'asc') ? 'selected' : '' ?>>Coût (Croissant)</option>
                                    <option value="cout_moyen" <?= ($sortBy == 'cout_moyen' && $sortOrder == 'desc') ? 'selected' : '' ?>>Coût (Décroissant)</option>
                                    <option value="capacite" <?= ($sortBy == 'capacite' && $sortOrder == 'asc') ? 'selected' : '' ?>>Capacité (Croissant)</option>
                                    <option value="capacite" <?= ($sortBy == 'capacite' && $sortOrder == 'desc') ? 'selected' : '' ?>>Capacité (Décroissant)</option>
                                    <option value="frequence" <?= ($sortBy == 'frequence' && $sortOrder == 'asc') ? 'selected' : '' ?>>Fréquence (Croissant)</option>
                                    <option value="frequence" <?= ($sortBy == 'frequence' && $sortOrder == 'desc') ? 'selected' : '' ?>>Fréquence (Décroissant)</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-xl-9 col-lg-8 content-left-offset">
                    <h3 class="page-title">Liste des Transports</h3>

                    <div class="freelancers-container freelancers-list-layout compact-list margin-top-35">
                        <?php foreach($currentPageTransports as $transport): 
                            $colorPalette = $colorPalettes[$transport['nom_categorie']] ?? $colorPalettes['default'];
                        ?>
                            <div class="col-md-6 mb-4">
                                <a href="single-transport-page.php?id=<?= $transport['id_transport'] ?>" class="task-listing h-100">
                                    <div class="task-listing-details h-100 d-flex flex-column">
                                        <div class="task-listing-description flex-grow-1">
                                            <h3 class="task-listing-title">
                                                <?= htmlspecialchars($transport['nom_transport']) ?></h3>
                                            <ul class="task-icons">
                                                <li><i class="icon-material-outline-directions-transit"></i> Catégorie:
                                                    <?= htmlspecialchars($transport['nom_categorie']) ?></li>
                                                <li><i class="icon-material-outline-people"></i> Capacité:
                                                    <?= htmlspecialchars($transport['capacite']) ?> personnes</li>
                                                <li><i class="icon-material-outline-access-time"></i> Fréquence:
                                                    <?= htmlspecialchars($transport['frequence']) ?> min</li>
                                                <li><i class="icon-material-outline-location-on"></i> Zone:
                                                    <?= htmlspecialchars($transport['zone_deservie']) ?></li>
                                            </ul>
                                        </div>

                                        <div class="task-listing-bid mt-auto">
                                            <div class="task-listing-bid-inner">
                                                <div class="task-offers">
                                                    <strong><?= htmlspecialchars($transport['cout_moyen']) ?> €</strong>
                                                    <span>Coût moyen</span>
                                                </div>
                                                <div class="task-offers">
                                                    <strong><?= $transport['ecologique'] ? 'Eco' : 'Non-eco' ?></strong>
                                                    <span>Type</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="button button-sliding-icon ripple-effect">
                                                        Choisir <i class="icon-material-outline-arrow-right-alt"></i>
                                                    </span>
                                                    <a href="https://www.google.com/maps/dir/?api=1&origin=<?= urlencode($transport['start_position']) ?>&destination=<?= urlencode($transport['destination_position']) ?>" 
                                                       target="_blank" class="btn btn-outline-primary btn-sm ml-2">
                                                        <i class="icon-feather-map-pin"></i> Itinéraire
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
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
                            echo '<li class="pagination-arrow"><a href="?page=' . ($currentPage - 1) . '&eco=' . $currentEcoFilter . '&eco_level=' . $currentEcoLevelFilter . '&search=' . urlencode($searchTerm) . '&sort=' . urlencode($sortBy) . '&order=' . urlencode($sortOrder) . '" class="ripple-effect"><i class="icon-material-outline-keyboard-arrow-left"></i></a></li>';
                        }
                        
                        // First page and ellipsis
                        if ($startPage > 1) {
                            echo '<li><a href="?page=1&eco=' . $currentEcoFilter . '&eco_level=' . $currentEcoLevelFilter . '&search=' . urlencode($searchTerm) . '&sort=' . urlencode($sortBy) . '&order=' . urlencode($sortOrder) . '" class="ripple-effect">1</a></li>';
                            if ($startPage > 2) {
                                echo '<li class="disabled"><span>...</span></li>';
                            }
                        }
                        
                        // Page numbers
                        for ($i = $startPage; $i <= $endPage; $i++) {
                            $activeClass = ($i == $currentPage) ? 'current-page' : '';
                            echo '<li><a href="?page=' . $i . '&eco=' . $currentEcoFilter . '&eco_level=' . $currentEcoLevelFilter . '&search=' . urlencode($searchTerm) . '&sort=' . urlencode($sortBy) . '&order=' . urlencode($sortOrder) . '" class="ripple-effect ' . $activeClass . '">' . $i . '</a></li>';
                        }
                        
                        // Last page and ellipsis
                        if ($endPage < $totalPages) {
                            if ($endPage < $totalPages - 1) {
                                echo '<li class="disabled"><span>...</span></li>';
                            }
                            echo '<li><a href="?page=' . $totalPages . '&eco=' . $currentEcoFilter . '&eco_level=' . $currentEcoLevelFilter . '&search=' . urlencode($searchTerm) . '&sort=' . urlencode($sortBy) . '&order=' . urlencode($sortOrder) . '" class="ripple-effect">' . $totalPages . '</a></li>';
                        }
                        
                        // Next link
                        if ($currentPage < $totalPages) {
                            echo '<li class="pagination-arrow"><a href="?page=' . ($currentPage + 1) . '&eco=' . $currentEcoFilter . '&eco_level=' . $currentEcoLevelFilter . '&search=' . urlencode($searchTerm) . '&sort=' . urlencode($sortBy) . '&order=' . urlencode($sortOrder) . '" class="ripple-effect"><i class="icon-material-outline-keyboard-arrow-right"></i></a></li>';
                        }
                        
                        echo '</ul></nav></div>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <script>
            function cycleEcologicalFilter() {
                // Reload page with next ecological filter state
                const currentUrl = new URL(window.location.href);
                const currentFilter = currentUrl.searchParams.get('eco') || 'all';
                const filterStates = ['all', 'eco', 'non-eco'];
                
                // Find current index and get next state
                const currentIndex = filterStates.indexOf(currentFilter);
                const nextIndex = (currentIndex + 1) % filterStates.length;
                const nextFilter = filterStates[nextIndex];

                // Update URL with new filter
                currentUrl.searchParams.set('eco', nextFilter);
                currentUrl.searchParams.delete('page'); // Reset to first page
                window.location.href = currentUrl.toString();
            }
        </script>

        <!-- Scripts
        ================================================== -->
        <script src="js/jquery-3.3.1.min.js"></script>
        <script src="js/jquery-migrate-3.0.0.min.js"></script>
        <script src="js/mmenu.min.js"></script>
        <script src="js/tippy.all.min.js"></script>
        <script src="js/simplebar.min.js"></script>
        <script src="js/bootstrap-slider.min.js"></script>
        <script src="js/bootstrap-select.min.js"></script>
        <script src="js/snackbar.js"></script>
        <script src="js/clipboard.min.js"></script>
        <script src="js/counterup.min.js"></script>
        <script src="js/magnific-popup.min.js"></script>
        <script src="js/slick.min.js"></script>
        <script src="js/custom.js"></script>
    </body>
</html>
