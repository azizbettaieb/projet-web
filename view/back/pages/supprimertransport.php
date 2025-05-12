    <?php
include_once "../../../Controller/transportC.php";  // Assure-toi d'inclure le bon fichier contrôleur pour les transports

$transportC = new TransportC();  // Utilise la classe TransportC pour gérer les transports

if (isset($_GET['id'])) {  // Vérifie si l'ID du transport est passé en paramètre GET
    $idTransport = $_GET['id'];  // Récupère l'ID du transport à supprimer
    $transportC->supprimerTransport($idTransport);  // Appelle la méthode pour supprimer le transport
    header("Location: cattrans.php");  // Redirige vers la page de gestion des transports
    exit();  // Arrête l'exécution du script
}
?>
