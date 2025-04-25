<?php

namespace App\Backend\Model;

class Supplier {
    private $id;
    private $name;
    private $address;
    private $dateCreate;

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getAddress() {
        return $this->address;
    }

    public function getDateCreate() {
        return $this->dateCreate;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setAddress($address) {
        $this->address = $address;
    }
    
    public function setDateCreate($dateCreate) {
        $this->dateCreate = $dateCreate;
    }
}