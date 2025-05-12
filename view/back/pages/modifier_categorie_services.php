<?php
include_once "../../../config.php";
include_once "../../../model/Categorie.php";

$categorieC = new CategorieC();
$errors = [];
$formData = [];

// Check if an ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: catservices.php");
    exit();
}

$categorieId = intval($_GET['id']);

// Fetch the current category details
$categorie = $categorieC->recupererCategorie($categorieId);

if (!$categorie) {
    header("Location: catservices.php?error=categorie_not_found");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['modifierCategorie'])) {
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

    // If no errors, proceed with updating category
    if (empty($errors)) {
        try {
            // Update the category
            $categorieModif = new Categorie($categorieId, $nom, $description);
            $result = $categorieC->modifierCategorie($categorieModif, $categorieId);

            if ($result) {
                // Redirect to categories page with success message
                header("Location: catservices.php?success=1");
                exit();
            } else {
                $errors[] = "Erreur lors de la modification de la catégorie.";
            }
        } catch (Exception $e) {
            $errors[] = "Erreur lors de la modification de la catégorie : " . $e->getMessage();
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
    <title>Modifier Catégorie de Services</title>
    
    <!-- Fonts and icons -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    
    <!-- CSS Files -->
    <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.1.0" rel="stylesheet" />
</head>

<body class="bg-gray-100">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg">
                    <div class="card-header bg-gradient-primary text-white text-center py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0 flex-grow-1">Modifier Catégorie de Services</h4>
                            <a href="catservices.php" class="btn btn-outline-light btn-sm">
                                <i class="material-icons opacity-10 me-1">arrow_back</i>Retour
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="modifier_categorie_services.php?id=<?php echo $categorieId; ?>" method="POST" id="categorieForm">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="nom" class="form-label">Nom de la Catégorie de Service</label>
                                        <div class="input-group input-group-outline">
                                            <input type="text" class="form-control" name="nom" id="nom" 
                                                placeholder="Ex: Services Agricoles" required
                                                value="<?php echo htmlspecialchars($categorie['nom_categorie'] ?? $formData['nom'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="description" class="form-label">Description de la Catégorie</label>
                                        <div class="input-group input-group-outline">
                                            <textarea class="form-control" name="description" id="description" 
                                                placeholder="Description détaillée de la catégorie de service" required rows="3"><?php 
                                                echo htmlspecialchars($categorie['description'] ?? $formData['description'] ?? ''); 
                                            ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                <button type="submit" name="modifierCategorie" class="btn bg-gradient-primary text-white me-3">
                                    <i class="material-icons opacity-10 me-2">edit</i>Modifier Catégorie
                                </button>
                                <a href="catservices.php" class="btn btn-outline-secondary">
                                    <i class="material-icons opacity-10 me-2">cancel</i>Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('categorieForm');
        const fields = {
            'nom': {
                element: document.getElementById('nom'),
                validate: function(value) {
                    return value.trim().length >= 3;
                },
                errorMessage: 'Le nom de la catégorie doit avoir au moins 3 caractères.'
            },
            'description': {
                element: document.getElementById('description'),
                validate: function(value) {
                    return value.trim().length >= 10;
                },
                errorMessage: 'La description doit avoir au moins 10 caractères.'
            }
        };

        function showError(field, message) {
            const errorDiv = field.element.parentNode;
            errorDiv.classList.add('is-invalid');
            
            // Remove any existing error message
            const existingError = errorDiv.querySelector('.invalid-feedback');
            if (existingError) {
                existingError.remove();
            }

            // Create and append error message
            const errorElement = document.createElement('div');
            errorElement.classList.add('invalid-feedback');
            errorElement.textContent = message;
            errorDiv.appendChild(errorElement);
        }

        function clearError(field) {
            const errorDiv = field.element.parentNode;
            errorDiv.classList.remove('is-invalid');
            
            const existingError = errorDiv.querySelector('.invalid-feedback');
            if (existingError) {
                existingError.remove();
            }
        }

        form.addEventListener('submit', function(event) {
            let isValid = true;

            Object.values(fields).forEach(field => {
                clearError(field);
                const value = field.element.value;

                if (!field.validate(value)) {
                    showError(field, field.errorMessage);
                    isValid = false;
                }
            });

            if (!isValid) {
                event.preventDefault();
            }
        });

        // Real-time validation
        Object.values(fields).forEach(field => {
            field.element.addEventListener('input', function() {
                clearError(field);
                const value = this.value;

                if (!field.validate(value)) {
                    showError(field, field.errorMessage);
                }
            });
        });
    });
    </script>
</body>
</html>
