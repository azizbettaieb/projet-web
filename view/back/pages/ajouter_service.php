<?php
include_once "../../../config.php";
include_once "../../../Controller/ServiceC.php";
include_once "../../../Controller/CategorieC.php";

$serviceC = new ServiceC();
$categorieC = new CategorieC();

$errors = [];
$formData = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouterService'])) {
    // Récupération et validation des données
    $formData = [
        'service_name' => trim($_POST['service_name'] ?? ''),
        'service_description' => trim($_POST['service_description'] ?? ''),
        'price' => floatval($_POST['price'] ?? 0),
        'eco_friendly' => intval($_POST['eco_friendly'] ?? 0),
        'id_categorie' => intval($_POST['id_categorie'] ?? 0)
    ];

    // Validation des champs
    $validationRules = [
        'service_name' => [
            'required' => true,
            'min_length' => 3,
            'error_message' => "Le nom du service doit avoir au moins 3 caractères."
        ],
        'service_description' => [
            'required' => true,
            'min_length' => 10,
            'error_message' => "La description doit avoir au moins 10 caractères."
        ],
        'price' => [
            'min_value' => 0,
            'error_message' => "Le prix doit être un nombre positif."
        ],
        'id_categorie' => [
            'min_value' => 1,
            'error_message' => "Veuillez sélectionner une catégorie valide."
        ]
    ];

    // Validation dynamique
    foreach ($validationRules as $field => $rules) {
        $value = $formData[$field];
        
        if (isset($rules['required']) && $rules['required'] && empty($value)) {
            $errors[] = "Le champ $field est requis.";
        }
        
        if (isset($rules['min_length']) && strlen($value) < $rules['min_length']) {
            $errors[] = $rules['error_message'];
        }
        
        if (isset($rules['min_value']) && $value <= $rules['min_value']) {
            $errors[] = $rules['error_message'];
        }
    }

    // Si pas d'erreurs, ajouter le service
    if (empty($errors)) {
        $service = new Service(
            null, 
            $formData['service_name'], 
            $formData['service_description'], 
            $formData['price'], 
            $formData['eco_friendly'], 
            $formData['id_categorie']
        );

        try {
            $result = $serviceC->ajouterService($service);
            if ($result) {
                // Redirection avec message de succès
                header("Location: catservices.php?success=1");
                exit();
            } else {
                $errors[] = "Erreur lors de l'ajout du service. Veuillez réessayer.";
            }
        } catch (Exception $e) {
            $errors[] = "Une erreur inattendue s'est produite : " . $e->getMessage();
        }
    }
}

// Récupérer les catégories pour le dropdown
$categories = $categorieC->recupererToutesCategories();

// Debugging: Log categories if none found
if (empty($categories)) {
    error_log('Aucune catégorie de service trouvée');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>Ajouter un Service</title>
    
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
    <style>
        .service-form {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
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
                    <a class="nav-link active bg-gradient-dark text-white" href="ajouter_service.php">
                        <span class="nav-link-text ms-1">Ajouter Service</span>
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
                                <h6 class="text-white text-capitalize ps-3">Ajouter un Service</h6>
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
                                        <label for="service_name" class="form-label">Nom du Service</label>
                                        <input type="text" 
                                               class="form-control border border-2 p-2" 
                                               id="service_name" 
                                               name="service_name" 
                                               value="<?php echo htmlspecialchars($formData['service_name'] ?? ''); ?>" 
                                               required 
                                               minlength="3">
                                        <div class="invalid-feedback">
                                            Le nom du service doit avoir au moins 3 caractères.
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="id_categorie" class="form-label">Catégorie de Service</label>
                                        <select 
                                            class="form-control border border-2 p-2" 
                                            id="id_categorie" 
                                            name="id_categorie" 
                                            required>
                                            <option value="">Sélectionnez une catégorie</option>
                                            <?php foreach ($categories as $categorie): ?>
                                                <option value="<?php echo $categorie['id_categorie']; ?>">
                                                    <?php echo htmlspecialchars($categorie['nom_categorie']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Veuillez sélectionner une catégorie.
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="service_description" class="form-label">Description du Service</label>
                                        <textarea 
                                            class="form-control border border-2 p-2" 
                                            id="service_description" 
                                            name="service_description" 
                                            rows="4" 
                                            required 
                                            minlength="10"><?php echo htmlspecialchars($formData['service_description'] ?? ''); ?></textarea>
                                        <div class="invalid-feedback">
                                            La description doit avoir au moins 10 caractères.
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="price" class="form-label">Prix (en DT)</label>
                                        <input type="number" 
                                               class="form-control border border-2 p-2" 
                                               id="price" 
                                               name="price" 
                                               value="<?php echo htmlspecialchars($formData['price'] ?? ''); ?>" 
                                               step="0.01" 
                                               min="0" 
                                               required>
                                        <div class="invalid-feedback">
                                            Le prix doit être un nombre positif.
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="eco_friendly" class="form-label">Éco-Responsable</label>
                                        <select 
                                            class="form-control border border-2 p-2" 
                                            id="eco_friendly" 
                                            name="eco_friendly" 
                                            required>
                                            <option value="0">Non</option>
                                            <option value="1">Oui</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" 
                                                name="ajouterService" 
                                                class="btn btn-primary">
                                            Ajouter le Service
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

                </div>
            </div>
        </div>
    </main>
</body>
</html>
