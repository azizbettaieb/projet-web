<?php
include_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/CategorieTransport.php';

class CategoryTransportC
{
    // Ajouter une catégorie de transport
    public function ajouterCategorie($categorie)
    {
        $sql = "INSERT INTO categoryTransport (nom_categorie, description) 
                VALUES (:nom, :description)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $result = $query->execute([
                'nom' => $categorie->getNom(),
                'description' => $categorie->getDescription()
            ]);
            
            error_log("Ajout de catégorie transport - Résultat: " . ($result ? 'Succès' : 'Échec'));
            
            return $result && $query->rowCount() > 0;
        } catch (Exception $e) {
            error_log('Erreur lors de l\'ajout de la catégorie transport: ' . $e->getMessage());
            return false;
        }
    }

    // Afficher toutes les catégories de transport
    public function afficherCategories()
    {
        $sql = "SELECT * FROM categoryTransport";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Erreur lors de l\'affichage des catégories transport: ' . $e->getMessage());
            return [];
        }
    }

    // Supprimer une catégorie de transport par ID
    public function supprimerCategorie($id)
    {
        $sql = "DELETE FROM categoryTransport WHERE id_categorie = :id";
        $db = config::getConnexion();
        try {
            // First, check if the category exists
            $checkSql = "SELECT COUNT(*) FROM categoryTransport WHERE id_categorie = :id";
            $checkQuery = $db->prepare($checkSql);
            $checkQuery->bindValue(':id', $id);
            $checkQuery->execute();
            $categoryExists = $checkQuery->fetchColumn();

            if (!$categoryExists) {
                error_log("Tentative de suppression d'une catégorie transport inexistante (ID: $id)");
                return false;
            }

            // Proceed with deletion
            $query = $db->prepare($sql);
            $query->bindValue(':id', $id);
            $result = $query->execute();
            $rowCount = $query->rowCount();

            error_log("Suppression de la catégorie transport (ID: $id) - Résultat: $result, Lignes affectées: $rowCount");

            return $result && $rowCount > 0;
        } catch (Exception $e) {
            error_log('Erreur lors de la suppression de la catégorie transport (ID: ' . $id . '): ' . $e->getMessage());
            return false;
        }
    }

    // Récupérer une catégorie de transport par ID
    public function recupererCategorie($id)
    {
        $sql = "SELECT * FROM categoryTransport WHERE id_categorie = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return new CategorieTransport($row['id_categorie'], $row['nom_categorie'], $row['description']);
            }
            return null;
        } catch (Exception $e) {
            error_log('Erreur lors de la récupération de la catégorie transport: ' . $e->getMessage());
            return null;
        }
    }

    // Modifier une catégorie de transport
    public function modifierCategorie($categorie, $id)
    {
        $sql = "UPDATE categoryTransport SET 
                nom_categorie = :nom, 
                description = :description 
                WHERE id_categorie = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $result = $query->execute([
                'nom' => $categorie->getNom(),
                'description' => $categorie->getDescription(),
                'id' => $id
            ]);
            
            $rowCount = $query->rowCount();
            error_log("Modification de la catégorie de transport (ID: $id) - Résultat: " . ($result ? 'Succès' : 'Échec') . ", Lignes affectées: $rowCount");
            
            return $result && $rowCount > 0;
        } catch (Exception $e) {
            error_log('Erreur lors de la modification de la catégorie de transport: ' . $e->getMessage());
            return false;
        }
    }

    // Récupérer toutes les catégories de transport
    public function getAllCategories()
    {
        $sql = "SELECT * FROM categoryTransport";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $rows = $query->fetchAll(PDO::FETCH_ASSOC);
            $categories = [];
            foreach ($rows as $row) {
                $categories[] = new CategorieTransport($row['id_categorie'], $row['nom_categorie'], $row['description']);
            }
            return $categories;
        } catch (Exception $e) {
            error_log('Erreur lors de la récupération des catégories transport: ' . $e->getMessage());
            return [];
        }
    }
}
?>
