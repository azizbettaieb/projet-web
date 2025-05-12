<?php
include_once "../../../config.php";
include_once "../../../Controller/CategorieC.php";
include_once "../../../Controller/CategorieC.php";

$categorieC = new CategorieC();

// Check if an ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: catservices.php?error=categorie_id_missing");
    exit();
}

$categorieId = intval($_GET['id']);

// Attempt to delete the category
$result = $categorieC->supprimerCategorie($categorieId);

if ($result) {
    // Successful deletion
    header("Location: catservices.php?success=categorie_deleted");
} else {
    // Deletion failed
    header("Location: catservices.php?error=categorie_delete_failed");
}

exit();
?>
