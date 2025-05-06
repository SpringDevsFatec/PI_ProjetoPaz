<?php

namespace App\Backend\Model;

use PDO;

class UserModel{

    // Attributes
    private $id;
    private $name;
    private $email;
    private $password;
    private $dateCreate;
    private $updatedAt;

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
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    public function getDateCreate() {
        return $this->dateCreate;
    }

    public function setDateCreate($dateCreate) {
        $this->dateCreate = $dateCreate;
    }

    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
    }

}