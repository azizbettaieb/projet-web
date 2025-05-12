<?php
include_once "../../../config.php";
include_once "../../../model/Categorie.php"; // For services categories
include_once "../../../Controller/CategorieC.php"; // For services categories controller

$categorieC = new CategorieC();
$errors = [];
$formData = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouterCategorie'])) {
    // Validate and sanitize inputs
    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Store form data for repopulation
    $formData = [
        'nom' => $nom,
        'description' => $description
    ];

    // Validation
    if (empty($nom) || strlen($nom) < 3) {
        $errors[] = "Le nom de la catégorie doit avoir au moins 3 caractères.";
    }

    if (empty($description) || strlen($description) < 10) {
        $errors[] = "La description doit avoir au moins 10 caractères.";
    }

    // If no errors, proceed with adding category
    if (empty($errors)) {
        try {
            // Créer l'objet Categorie
            $categorie = new Categorie(null, $nom, $description);

            // Ajouter la catégorie à la base de données
            $result = $categorieC->ajouterCategorie($categorie);

            // Redirection vers la page des catégories
            if ($result) {
                header("Location: catservices.php?success=1");
            } else {
                $errors[] = "Erreur lors de l'ajout de la catégorie. Veuillez réessayer.";
            }
            exit();
        } catch (Exception $e) {
            $errors[] = "Erreur lors de l'ajout de la catégorie : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>Ajouter une Catégorie de Service</title>
    
    <!-- Fonts and icons -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <!-- Nucleo Icons -->
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <!-- CSS Files -->
    <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
</head>
<body class="g-sidenav-show bg-gray-100">
    <!-- Sidebar -->
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2" id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand m-0" href="catservices.php">
                <span class="ms-1 text-sm text-dark">Gestion des Services</span>
            </a>
        </div>
        <hr class="horizontal dark mt-0 mb-2">
        <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-dark" href="catservices.php">
                        <span class="nav-link-text ms-1">Liste des Services</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active bg-gradient-dark text-white" href="ajouter_categorie_services.php">
                        <span class="nav-link-text ms-1">Ajouter Catégorie</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Ajouter une Catégorie de Service</h6>
                            </div>
                        </div>
                        <div class="card-body px-4 pb-2">
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php foreach ($errors as $error): ?>
                                        <p class="text-white mb-0"><?php echo htmlspecialchars($error); ?></p>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="" class="needs-validation" novalidate>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nom" class="form-label">Nom de la Catégorie</label>
                                        <input type="text" 
                                               class="form-control border border-2 p-2" 
                                               id="nom" 
                                               name="nom" 
                                               value="<?php echo htmlspecialchars($formData['nom'] ?? ''); ?>" 
                                               required 
                                               minlength="3">
                                        <div class="invalid-feedback">
                                            Le nom doit avoir au moins 3 caractères.
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea 
                                            class="form-control border border-2 p-2" 
                                            id="description" 
                                            name="description" 
                                            rows="4" 
                                            required 
                                            minlength="10"><?php echo htmlspecialchars($formData['description'] ?? ''); ?></textarea>
                                        <div class="invalid-feedback">
                                            La description doit avoir au moins 10 caractères.
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" 
                                                name="ajouterCategorie" 
                                                class="btn btn-primary">
                                            Ajouter la Catégorie
                                        </button>
                                        <a href="catservices.php" class="btn btn-secondary ms-2">
                                            Annuler
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script>
        // Form validation script
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
    <script src="../assets/js/material-dashboard.min.js?v=3.2.0"></script>

    
    <!-- Scripts -->
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script>
        // Form validation script
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
    <script src="../assets/js/material-dashboard.min.js?v=3.2.0"></script>
</body>
</html>        }
    }
}

?>
