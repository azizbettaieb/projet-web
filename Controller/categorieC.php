<?php
include_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/Categorie.php';

class CategorieC
{
    // Ajouter une catégorie de service
    public function ajouterCategorie($categorie)
    {
        $sql = "INSERT INTO categoryServices (nom_categorie, description) 
                VALUES (:nom, :description)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $result = $query->execute([
                'nom' => $categorie->getNom(),
                'description' => $categorie->getDescription()
            ]);
            
            error_log("Ajout de catégorie de service - Résultat: " . ($result ? 'Succès' : 'Échec'));
            
            return $result && $query->rowCount() > 0;
        } catch (Exception $e) {
            error_log('Erreur lors de l\'ajout de la catégorie de service: ' . $e->getMessage());
            return false;
        }
    }

    // Supprimer une catégorie de service par ID
    public function supprimerCategorie($id)
    {
        $sql = "DELETE FROM categoryServices WHERE id_categorie = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id', $id);
            $result = $query->execute();
            $rowCount = $query->rowCount();

            error_log("Suppression de la catégorie de service (ID: $id) - Lignes affectées: $rowCount");

            return $result && $rowCount > 0;
        } catch (Exception $e) {
            error_log('Erreur lors de la suppression de la catégorie de service (ID: ' . $id . '): ' . $e->getMessage());
            return false;
        }
    }

    // Récupérer une catégorie de service par ID
    // Récupérer toutes les catégories de service
    public function recupererToutesCategories()
    {
        $sql = "SELECT id_categorie, nom_categorie, description FROM categoryServices";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Erreur lors de la récupération des catégories de service: ' . $e->getMessage());
            return [];
        }
    }

    public function recupererCategorie($id)
    {
        $sql = "SELECT * FROM categoryServices WHERE id_categorie = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return new Categorie($row['id_categorie'], $row['nom_categorie'], $row['description']);
            }
            return null;
        } catch (Exception $e) {
            error_log('Erreur lors de la récupération de la catégorie de service: ' . $e->getMessage());
            return null;
        }
    }

    // Modifier une catégorie de service
    public function modifierCategorie($categorie, $id)
    {
        $sql = "UPDATE categoryServices SET 
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
            
            error_log("Modification de la catégorie de service (ID: $id) - Résultat: " . ($result ? 'Succès' : 'Échec'));
            
            return $result && $query->rowCount() > 0;
        } catch (Exception $e) {
            error_log('Erreur lors de la modification de la catégorie de service: ' . $e->getMessage());
            return false;
        }
    }
    

    // Récupérer toutes les catégories de service
    public function getAllCategories()
    {
        $sql = "SELECT * FROM categoryServices";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $rows = $query->fetchAll(PDO::FETCH_ASSOC);
            $categories = [];
            foreach ($rows as $row) {
                $categories[] = new Categorie($row['id_categorie'], $row['nom_categorie'], $row['description']);
            }
            return $categories;
        } catch (Exception $e) {
            error_log('Erreur lors de la récupération des catégories de service: ' . $e->getMessage());
            return [];
        }
    }
}
?>