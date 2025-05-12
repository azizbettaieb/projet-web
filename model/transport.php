<?php

class Transport
{
    private $id_transport;
    private $nom_transport;
    private $capacite;
    private $frequence;
    private $zone_deservie;
    private $cout_moyen;
    private $ecologique;
    private $id_categorie;
    private $start_position;        
    private $destination_position;   

    
    public function __construct($id_transport = null, $nom_transport, $capacite, $frequence, $zone_deservie, $cout_moyen, $ecologique, $id_categorie, $start_position, $destination_position)
    {
        $this->id_transport = $id_transport;
        $this->nom_transport = $nom_transport;
        $this->capacite = $capacite;
        $this->frequence = $frequence;
        $this->zone_deservie = $zone_deservie;
        $this->cout_moyen = $cout_moyen;
        $this->ecologique = $ecologique;
        $this->id_categorie = $id_categorie;
        $this->start_position = $start_position; 
        $this->destination_position = $destination_position; 
    }


    public function getIdTransport()
    {
        return $this->id_transport;
    }

    public function getNomTransport()
    {
        return $this->nom_transport;
    }

    public function getCapacite()
    {
        return $this->capacite;
    }

    public function getFrequence()
    {
        return $this->frequence;
    }

    public function getZoneDeservie()
    {
        return $this->zone_deservie;
    }

    public function getCoutMoyen()
    {
        return $this->cout_moyen;
    }

    public function getEcologique()
    {
        return $this->ecologique;
    }

    public function getIdCategorie()
    {
        return $this->id_categorie;
    }

    public function getStartPosition() // ✅
    {
        return $this->start_position;
    }

    public function getDestinationPosition() // ✅
    {
        return $this->destination_position;
    }

    // Setters
    public function setNomTransport($nom_transport)
    {
        $this->nom_transport = $nom_transport;
    }

    public function setCapacite($capacite)
    {
        $this->capacite = $capacite;
    }

    public function setFrequence($frequence)
    {
        $this->frequence = $frequence;
    }

    public function setZoneDeservie($zone_deservie)
    {
        $this->zone_deservie = $zone_deservie;
    }

    public function setCoutMoyen($cout_moyen)
    {
        $this->cout_moyen = $cout_moyen;
    }

    public function setEcologique($ecologique)
    {
        $this->ecologique = $ecologique;
    }

    public function setIdCategorie($id_categorie)
    {
        $this->id_categorie = $id_categorie;
    }

    public function setStartPosition($start_position) // ✅
    {
        $this->start_position = $start_position;
    }

    public function setDestinationPosition($destination_position) // ✅
    {
        $this->destination_position = $destination_position;
    }
}
?>