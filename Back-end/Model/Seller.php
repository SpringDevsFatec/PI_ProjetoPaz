<?php

namespace App\Backend\Model;

class Seller {
    private $id;
    private $userId;
    private $saleId;
    private $dateCreate;

    public function getId() {
        return $this->id;
    }
    public function getUserId() {
        return $this->userId;
    }
    public function getSaleId() {
        return $this->saleId;
    }
    public function getDateCreate() {
        return $this->dateCreate;
    }
    public function setId($id) {
        $this->id = $id;
    }
    public function setUserId($userId) {
        $this->userId = $userId;
    }
    public function setSaleId($saleId) {
        $this->saleId = $saleId;
    }
    public function setDateCreate($dateCreate) {
        $this->dateCreate = $dateCreate;
    }
}