<?php

namespace App\Backend\Model;

use App\Backend\Utils\PatternText;
use PDO;

class UserModel{

    // Attributes
    private $id;
    private $name;
    private $email;
    private $password;
    private $created_at;

    // // Constructor
    // public function __construct($id = null, $name = null, $email = null, $password = null, $dateCreate = null, $updatedAt = null) {
    //     $this->id = $id;
    //     $this->name = $name;
    //     $this->email = $email;
    //     $this->password = password_hash($password, PASSWORD_BCRYPT);
    //     $this->dateCreate = $dateCreate;
    //     $this->updatedAt = $updatedAt;
    // }
    // Getters and Setters
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
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
        $this->password = PatternText::cryptPassword($password);
    }
    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }
    
    // Method to convert the object to an array for JSON serialization
    public function jsonSerialize() {
        return [
            'name' => $this->name,
            'email' => $this->email,
            // Nunca envie a senha em produção! Só adicione se for necessário e seguro.
        ];
    }

}