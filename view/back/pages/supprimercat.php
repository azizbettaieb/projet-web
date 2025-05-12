<?php
include_once "../../../Controller/CategoryTransportC.php";

$categorieC = new CategoryTransportC();

if (isset($_GET['id'])) {
    $idCategorie = $_GET['id'];
    $result = $categorieC->supprimerCategorie($idCategorie);
    if ($result) {
        header("Location: cattrans.php?success=1");
    } else {
        header("Location: cattrans.php?error=1");
    }
    exit();
}
?>
