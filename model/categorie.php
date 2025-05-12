
<?php
class Categorie{
    private string $description;
    private int $id;
    private string $nom;
    
    public function __construct($id = null, $nom = null, $description = null)
    {
        $this->id = (int)$id;
        $this->nom = $nom ?? '';
        $this->description = $description ?? '';
    }
    public function getNom()
    {
        return $this->nom;
    }
    public function setNom($nom)
    {
        $this->nom = $nom;
        return $this;
    }
    public function getId()
    {
         return $this->id;
    }
    public function setId($id)
    {
         $this->id = (int)$id;
         return $this;
    }
    public function getDescription()
    {
         return $this->description;
    }
    public function setDescription($description)
    {
         $this->description = $description;
         return $this;
    }
}    
?>