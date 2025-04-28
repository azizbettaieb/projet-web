<?php
class User {
    private $id;
    private $name;
    private $lastName;
    private $email;
    private $password;
    private $role;
    private $statusCompte;
    private $photo;
    

    // Constructor
    public function __construct(
        $id = null,
        $name = null,
        $lastName = null,
        $email = null,
        $password = null,
        $role = null,
        $statusCompte = null,
        $photo = null,
  
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->statusCompte = $statusCompte;
        $this->photo = $photo;
   
    }

    // Getters and Setters
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getRole() {
        return $this->role;
    }

    public function setRole($role) {
        $this->role = $role;
    }

    public function getStatusCompte() {
        return $this->statusCompte;
    }

    public function setStatusCompte($statusCompte) {
        $this->statusCompte = $statusCompte;
    }

    public function getPhoto() {
        return $this->photo;
    }

    public function setPhoto($photo) {
        $this->photo = $photo;
    }
}
?>