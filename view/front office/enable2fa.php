<?php
session_start();
require_once '../../config.php';
require_once '../../GoogleAuthenticator.php';
require_once '../../Controller/UserController.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$pdo = config::getConnexion();

// Create controller
$controller = new UserController($pdo);

// Generate the QR code URL for Google Authenticator
$qrCodeUrl = $controller->enable2FA($userId);

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
                    
                    </div>
                </div>

                <div class="col-xl-9 col-lg-8 content-left-offset">
     
                               
                                    <h2>Enable Google Authenticator</h2>
                                    <br></br>
    <p>  Scan the QR code below using your Google Authenticator app:</p>
    <br></br>
    <img src="<?php echo $qrCodeUrl; ?>" alt="QR Code">
    <br></br>
    <p>   Once scanned, next time you log in you'll be asked for a 6-digit code.</p>
    <br></br>
    <a href="profile.php">Back to Profile</a>
                                      
                                    </div>
                                </a>
                    

                      
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

