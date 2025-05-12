<?php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/Transport.php';

class TransportC
{
    // Ajouter un transport
    public function ajouterTransport($transport) {
        $sql = "INSERT INTO Transport (nom_transport, capacite, frequence, zone_deservie, cout_moyen, ecologique, id_categorie, start_position, destination_position) 
                VALUES (:nom, :capacite, :frequence, :zone, :cout, :ecologique, :id_categorie, :start_position, :destination_position)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $transport->getNomTransport(),
                'capacite' => $transport->getCapacite(),
                'frequence' => $transport->getFrequence(),
                'zone' => $transport->getZoneDeservie(),
                'cout' => $transport->getCoutMoyen(),
                'ecologique' => $transport->getEcologique(),
                'id_categorie' => $transport->getIdCategorie(),
                'start_position' => $transport->getStartPosition(), // ✅ new
                'destination_position' => $transport->getDestinationPosition() // ✅ new
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }

    // Afficher tous les transports
    public function afficherTransports()
    {
        $sql = "SELECT t.*, c.nom_categorie 
                FROM Transport t 
                LEFT JOIN categoryTransport c ON t.id_categorie = c.id_categorie";
        $db = config::getConnexion();
        try {
            $liste = $db->query($sql);
            return $liste->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    // Supprimer un transport
    public function supprimerTransport($id)
    {
        $sql = "DELETE FROM Transport WHERE id_transport = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id', $id);
            $query->execute();
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    // Récupérer un transport par ID
    public function recupererTransport($id) {
        $sql = "SELECT * FROM Transport WHERE id_transport = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $row = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                return new Transport(
                    $row['id_transport'],
                    $row['nom_transport'],
                    $row['capacite'],
                    $row['frequence'],
                    $row['zone_deservie'],
                    $row['cout_moyen'],
                    $row['ecologique'],
                    $row['id_categorie'],
                    $row['start_position'],         // 
                    $row['destination_position']    // 
                );
            }
            return null;
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
            return null;
        }
    }

    // Modifier un transport
    public function modifierTransport($transport, $id) {
        $sql = "UPDATE Transport SET 
                    nom_transport = :nom,
                    capacite = :capacite,
                    frequence = :frequence,
                    zone_deservie = :zone,
                    cout_moyen = :cout,
                    ecologique = :ecologique,
                    id_categorie = :id_categorie,
                    start_position = :start_position,  -- 
                    destination_position = :destination_position  -- 
                WHERE id_transport = :id";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $transport->getNomTransport(),
                'capacite' => $transport->getCapacite(),
                'frequence' => $transport->getFrequence(),
                'zone' => $transport->getZoneDeservie(),
                'cout' => $transport->getCoutMoyen(),
                'ecologique' => $transport->getEcologique(),
                'id_categorie' => $transport->getIdCategorie(),
                'start_position' => $transport->getStartPosition(), // 
                'destination_position' => $transport->getDestinationPosition(), // 
                'id' => $id
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
            return false;
        }
    }

    public function getTransportsWithCategorie() {
        $sql = "SELECT t.*, c.nom_categorie 
                FROM Transport t
                JOIN categoryTransport c ON t.id_categorie = c.id_categorie";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function getTransportsByCategorie($idCategorie) {
        $sql = "SELECT t.*, c.nom_categorie 
                FROM Transport t
                JOIN categoryTransport c ON t.id_categorie = c.id_categorie
                WHERE t.id_categorie = :idCategorie";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['idCategorie' => $idCategorie]);
            return $query->fetchAll();
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }




    public function getTransportCountByCategory() {
        $sql = "SELECT c.nom_categorie, COUNT(t.id_transport) as transport_count
                FROM categoryTransport c
                LEFT JOIN Transport t ON c.id_categorie = t.id_categorie
                GROUP BY c.id_categorie
                ORDER BY transport_count DESC";  // to see the highest first
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
            return [];
        }
    }
    
}
?>