<?php
include_once "../../../config.php";
include '../../../Controller/usercontroller.php';
$pdo = config::getConnexion();

$controller = new UserController($pdo);
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}

$user = $controller->getUserById($id);
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    if ($controller->updateUser($id, $name, $lastName, $email, $role)) {
        header("Location: data-user.php");
        exit;
    } else {
        $error = "Failed to update user.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>
        AgriTrace+
    </title>
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css"
        href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <!-- Nucleo Icons -->
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Material Icons -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <!-- CSS Files -->
    <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
</head>

<body class="g-sidenav-show  bg-gray-100">
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2  bg-white my-2"
        id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
                aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand px-4 py-3 m-0"
                href=" https://demos.creative-tim.com/material-dashboard/pages/dashboard " target="_blank">
                <img src="../assets/img/logo-ct-dark.png" class="navbar-brand-img" width="26" height="26"
                    alt="main_logo">
                <span class="ms-1 text-sm text-dark">AgriTrace+ Admin</span>
            </a>
        </div>
        <hr class="horizontal dark mt-0 mb-2">
        <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-dark" href="cattrans.php">
                        <span class="nav-link-text ms-1">Liste des Transports</span>
                    </a>
                </li>
				<li class="nav-item">
          <a class="nav-link text-dark" href="../pages/data-user.php">
            <span class="nav-link-text ms-1">liste utilisateur</span>
          </a>
        </li>
                <li class="nav-item">
                    <a class="nav-link active bg-gradient-dark text-white" href="ajoutertransport.php">
                        <span class="nav-link-text ms-1">Ajouter Transport</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl" id="navbarBlur"
            data-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a>
                        </li>
                        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Dashboard</li>
                    </ol>
                </nav>

            </div>
        </nav>
        <!-- End Navbar -->
        <style>
        .transport-form {
            max-width: 800px;
            margin: 0 auto;
            padding: 25px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .transport-form .form-title {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 25px;
            font-size: 24px;
            font-weight: 600;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        .transport-form .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            display: block;
        }

        .transport-form .form-control {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px 15px;
            width: 100%;
            transition: all 0.3s ease;
            background-color: white;
        }

        .transport-form .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.5);
            outline: none;
        }

        .transport-form .mb-3 {
            margin-bottom: 20px;
        }

        .transport-form .btn-success {
            background-color: #27ae60;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
            color: white;
            width: 100%;
            margin-top: 10px;
        }

        .transport-form .btn-success:hover {
            background-color: #2ecc71;
        }

        .transport-form select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232c3e50' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 15px;
            padding-right: 45px;
        }

        @media (min-width: 768px) {
            .transport-form .form-row {
                display: flex;
                flex-wrap: wrap;
                margin-right: -10px;
                margin-left: -10px;
            }

            .transport-form .form-col {
                flex: 0 0 50%;
                max-width: 50%;
                padding: 0 10px;
            }
        }
        </style>
        <div class="container-fluid py-2">


            <div class="px-4 py-3">
                <h5 class="mb-3">Ajouter une catégorie</h5>
               
                <form action="" method="POST" enctype="multipart/form-data" class="user-form" id="userForm">
    <div class="form-title">Créer un nouveau compte utilisateur</div>

    <div class="form-row">
        <div class="form-col">
            <div class="mb-3">
                <label for="name" class="form-label">Prénom</label>
                <input type="text" class="form-control" name="name" id="name" value="<?= htmlspecialchars($user->getName()) ?>" required>
            </div>
        </div>

        <div class="form-col">
            <div class="mb-3">
                <label for="last_name" class="form-label">Nom</label>
                <input type="text" class="form-control" name="last_name" id="last_name" value="<?= htmlspecialchars($user->getLastName()) ?>" required>
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="form-col">
            <div class="mb-3">
                <label for="email" class="form-label">Adresse e-mail</label>
                <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($user->getEmail()) ?>" required>
            </div>
        </div>

        <div class="form-col">
            <div class="mb-3">
                <label for="photo" class="form-label">Photo</label>
                <input type="file" class="form-control" name="photo" id="photo" accept="image/*">
            </div>
        </div>
    </div>





    <div class="form-row">
        <div class="form-col">
            <div class="mb-3">
                <label for="role" class="form-label">Rôle</label>
                <select name="role" id="role" class="form-control" required>
                    <option value="<?= htmlspecialchars($user->getRole()) ?>" disabled selected>Sélectionner un rôle</option>
                    <option value="0">Client</option>
                    <option value="1">Admin</option>
                </select>
            </div>
        </div>

        <div class="form-col">
            <div class="mb-3">
                <label for="statuscompte" class="form-label">Statut du compte</label>
                <select name="statuscompte" id="statuscompte" class="form-control" required>
                    <option value="" disabled selected>Sélectionner un statut</option>
                    <option value="1">Actif</option>
                    <option value="0">Inactif</option>
                </select>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Créer le compte</button>

    <?php if (!empty($error)) : ?>
        <div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
</form>

                <script>
                // Fonction pour valider le formulaire avant soumission
                document.getElementById("transportForm").addEventListener("submit", function(event) {
                    let isValid = true; // Variable pour contrôler la validité

                    // Récupérer les valeurs des champs
                    const nomTransport = document.getElementById("nom_transport").value.trim();
                    const capacite = document.getElementById("capacite").value.trim();
                    const frequence = document.getElementById("frequence").value.trim();
                    const zoneDeservie = document.getElementById("zone_deservie").value.trim();
                    const coutMoyen = document.getElementById("cout_moyen").value.trim();
                    const ecologique = document.getElementById("ecologique").value;
                    const idCategorie = document.getElementById("id_categorie").value;

                    // Vérification du champ "Nom du transport"
                    if (nomTransport === "") {
                        alert("Le nom du transport ne peut pas être vide.");
                        isValid = false;
                    } else if (nomTransport.length < 3) {
                        alert("Le nom du transport doit comporter au moins 3 caractères.");
                        isValid = false;
                    }

                    // Vérification du champ "Capacité"
                    if (capacite === "" || isNaN(capacite) || capacite <= 0) {
                        alert("La capacité doit être un nombre positif.");
                        isValid = false;
                    }

                    // Vérification du champ "Fréquence"
                    if (frequence === "") {
                        alert("La fréquence ne peut pas être vide.");
                        isValid = false;
                    }

                    // Vérification du champ "Zone desservie"
                    if (zoneDeservie === "") {
                        alert("La zone desservie ne peut pas être vide.");
                        isValid = false;
                    }

                    // Vérification du champ "Coût moyen"
                    if (coutMoyen === "" || isNaN(coutMoyen) || coutMoyen <= 0) {
                        alert("Le coût moyen doit être un nombre positif.");
                        isValid = false;
                    }

                    // Vérification du champ "Transport écologique"
                    if (ecologique === "") {
                        alert("Veuillez choisir si le transport est écologique.");
                        isValid = false;
                    }

                    // Vérification du champ "Catégorie"
                    if (idCategorie === "") {
                        alert("Veuillez sélectionner une catégorie.");
                        isValid = false;
                    }

                    // Si une validation échoue, on empêche l'envoi du formulaire
                    if (!isValid) {
                        event.preventDefault(); // Empêche la soumission du formulaire
                    }
                });
                </script>

            </div>

            <footer class="footer py-4  ">
                <div class="container-fluid">
                    <div class="row align-items-center justify-content-lg-between">
                        <div class="col-lg-6 mb-lg-0 mb-4">
                            <div class="copyright text-center text-sm text-muted text-lg-start">
                                © <script>
                                document.write(new Date().getFullYear())
                                </script>,
                                made with <i class="fa fa-heart"></i> by
                                <a href="https://www.creative-tim.com" class="font-weight-bold" target="_blank">Creative
                                    Tim</a>
                                for a better web.
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <ul class="nav nav-footer justify-content-center justify-content-lg-end">
                                <li class="nav-item">
                                    <a href="https://www.creative-tim.com" class="nav-link text-muted"
                                        target="_blank">Creative Tim</a>
                                </li>
                                <li class="nav-item">
                                    <a href="https://www.creative-tim.com/presentation" class="nav-link text-muted"
                                        target="_blank">About Us</a>
                                </li>
                                <li class="nav-item">
                                    <a href="https://www.creative-tim.com/blog" class="nav-link text-muted"
                                        target="_blank">Blog</a>
                                </li>
                                <li class="nav-item">
                                    <a href="https://www.creative-tim.com/license" class="nav-link pe-0 text-muted"
                                        target="_blank">License</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </main>
    <div class="fixed-plugin">
        <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
            <i class="material-symbols-rounded py-2">settings</i>
        </a>
        <div class="card shadow-lg">
            <div class="card-header pb-0 pt-3">
                <div class="float-start">
                    <h5 class="mt-3 mb-0">Material UI Configurator</h5>
                    <p>See our dashboard options.</p>
                </div>
                <div class="float-end mt-4">
                    <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
                        <i class="material-symbols-rounded">clear</i>
                    </button>
                </div>
                <!-- End Toggle Button -->
            </div>
            <hr class="horizontal dark my-1">
            <div class="card-body pt-sm-3 pt-0">
                <!-- Sidebar Backgrounds -->
                <div>
                    <h6 class="mb-0">Sidebar Colors</h6>
                </div>
                <a href="javascript:void(0)" class="switch-trigger background-color">
                    <div class="badge-colors my-2 text-start">
                        <span class="badge filter bg-gradient-primary" data-color="primary"
                            onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-dark active" data-color="dark"
                            onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-info" data-color="info"
                            onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-success" data-color="success"
                            onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-warning" data-color="warning"
                            onclick="sidebarColor(this)"></span>
                        <span class="badge filter bg-gradient-danger" data-color="danger"
                            onclick="sidebarColor(this)"></span>
                    </div>
                </a>
                <!-- Sidenav Type -->
                <div class="mt-3">
                    <h6 class="mb-0">Sidenav Type</h6>
                    <p class="text-sm">Choose between different sidenav types.</p>
                </div>
                <div class="d-flex">
                    <button class="btn bg-gradient-dark px-3 mb-2" data-class="bg-gradient-dark"
                        onclick="sidebarType(this)">Dark</button>
                    <button class="btn bg-gradient-dark px-3 mb-2 ms-2" data-class="bg-transparent"
                        onclick="sidebarType(this)">Transparent</button>
                    <button class="btn bg-gradient-dark px-3 mb-2  active ms-2" data-class="bg-white"
                        onclick="sidebarType(this)">White</button>
                </div>
                <p class="text-sm d-xl-none d-block mt-2">You can change the sidenav type just on desktop view.</p>
                <!-- Navbar Fixed -->
                <div class="mt-3 d-flex">
                    <h6 class="mb-0">Navbar Fixed</h6>
                    <div class="form-check form-switch ps-0 ms-auto my-auto">
                        <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed"
                            onclick="navbarFixed(this)">
                    </div>
                </div>
                <hr class="horizontal dark my-3">
                <div class="mt-2 d-flex">
                    <h6 class="mb-0">Light / Dark</h6>
                    <div class="form-check form-switch ps-0 ms-auto my-auto">
                        <input class="form-check-input mt-1 ms-auto" type="checkbox" id="dark-version"
                            onclick="darkMode(this)">
                    </div>
                </div>
                <hr class="horizontal dark my-sm-4">
                <a class="btn bg-gradient-info w-100"
                    href="https://www.creative-tim.com/product/material-dashboard-pro">Free Download</a>
                <a class="btn btn-outline-dark w-100"
                    href="https://www.creative-tim.com/learning-lab/bootstrap/overview/material-dashboard">View
                    documentation</a>
                <div class="w-100 text-center">
                    <a class="github-button" href="https://github.com/creativetimofficial/material-dashboard"
                        data-icon="octicon-star" data-size="large" data-show-count="true"
                        aria-label="Star creativetimofficial/material-dashboard on GitHub">Star</a>
                    <h6 class="mt-3">Thank you for sharing!</h6>
                    <a href="https://twitter.com/intent/tweet?text=Check%20Material%20UI%20Dashboard%20made%20by%20%40CreativeTim%20%23webdesign%20%23dashboard%20%23bootstrap5&amp;url=https%3A%2F%2Fwww.creative-tim.com%2Fproduct%2Fsoft-ui-dashboard"
                        class="btn btn-dark mb-0 me-2" target="_blank">
                        <i class="fab fa-twitter me-1" aria-hidden="true"></i> Tweet
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=https://www.creative-tim.com/product/material-dashboard"
                        class="btn btn-dark mb-0 me-2" target="_blank">
                        <i class="fab fa-facebook-square me-1" aria-hidden="true"></i> Share
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!--   Core JS Files   -->
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/chartjs.min.js"></script>
    <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="../assets/js/material-dashboard.min.js?v=3.2.0"></script>
</body>

</html>