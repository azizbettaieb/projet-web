<?php
class CategorieTransport {
    private ?int $id = null;
    private string $nom;
    private string $description;
    
    public function __construct($id = null, $nom = null, $description = null)
    {
        $this->id = $id !== null ? (int)$id : null;
        $this->nom = $nom ?? '';
        $this->description = $description ?? '';
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }
    
    public function getNom(): string
    {
        return $this->nom;
    }
    
    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }
    
    public function getDescription(): string
    {
        return $this->description;
    }
    
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }
}
?>
