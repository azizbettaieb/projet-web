<?php
include_once "../../../config.php";
include_once "../../../Controller/ServiceC.php";
include_once "../../../Controller/CategorieC.php";

$serviceC = new ServiceC();
$categorieC = new CategorieC();

// Récupération du service à modifier
if (!isset($_GET['id'])) {
    header("Location: catservices.php");
    exit();
}

$idService = $_GET['id'];
$service = $serviceC->recupererService($idService);

if (!$service) {
    header("Location: catservices.php");
    exit();
}

// Traitement du formulaire de modification
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $serviceModifie = new Service(
        $idService, 
        $_POST['service_name'], 
        $_POST['service_description'], 
        $_POST['price'], 
        isset($_POST['eco_friendly']) ? 1 : 0, 
        $_POST['id_categorie']
    );

    $serviceC->modifierService($serviceModifie, $idService);
    header("Location: catservices.php");
    exit();
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
        AgriTrace+ - Modifier Service
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
                href="https://demos.creative-tim.com/material-dashboard/pages/dashboard" target="_blank">
                <img src="../assets/img/logo-ct-dark.png" class="navbar-brand-img" width="26" height="26"
                    alt="main_logo">
                <span class="ms-1 text-sm text-dark">AgriTrace+ Admin</span>
            </a>
        </div>
        <hr class="horizontal dark mt-0 mb-2">
        <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-dark" data-bs-toggle="collapse" href="#submenuServices" role="button"
                        aria-expanded="false" aria-controls="submenuServices">
                        <span class="nav-link-text ms-1">Services</span>
                    </a>
                    <div class="collapse" id="submenuServices">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="catservices.php">
                                    Liste services
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="ajouter_service.php">
                                    Ajouter un service
                                </a>
                            </li>
                        </ul>
                    </div>
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
                        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Modifier Service</li>
                    </ol>
                </nav>
            </div>
        </nav>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Modifier un Service</h6>
                            </div>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <form method="POST" action="">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nom du Service</label>
                                        <input type="text" name="service_name" class="form-control border border-2 p-2" 
                                               value="<?php echo htmlspecialchars($service->getServiceName()); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea name="service_description" class="form-control border border-2 p-2" required><?php 
                                            echo htmlspecialchars($service->getServiceDescription()); 
                                        ?></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Prix</label>
                                        <input type="number" name="price" step="0.01" class="form-control border border-2 p-2" 
                                               value="<?php echo htmlspecialchars($service->getPrice()); ?>" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Catégorie</label>
                                        <select name="id_categorie" class="form-control border border-2 p-2" required>
                                            <?php foreach ($categorieC->recupererToutesCategories() as $categorie): ?>
                                                <option value="<?php echo $categorie['id_categorie']; ?>"
                                                    <?php echo ($categorie['id_categorie'] == $service->getIdCategorie()) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($categorie['nom_categorie']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3 d-flex align-items-end">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="eco_friendly" id="eco_friendly"
                                                   <?php echo $service->getEcoFriendly() ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="eco_friendly">
                                                Écologique
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" name="modifierService" class="btn bg-gradient-dark text-white">Modifier Service</button>
                                    <a href="catservices.php" class="btn btn-outline-dark">Annuler</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
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
                </div>
            </div>
        </footer>
    </main>
</body>
</html>
