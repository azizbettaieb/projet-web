<?php
include_once "../../../config.php";
include_once "../../../Controller/ServiceC.php";

// Vérifier si un ID de service est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: catservices.php?error=1");
    exit();
}

$id = $_GET['id'];
$serviceC = new ServiceC();

try {
    // Supprimer le service
    $serviceC->supprimerService($id);
    
    // Rediriger avec un message de succès
    header("Location: catservices.php?success=1");
    exit();
} catch (Exception $e) {
    // En cas d'erreur, rediriger avec un message d'erreur
    header("Location: catservices.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>
