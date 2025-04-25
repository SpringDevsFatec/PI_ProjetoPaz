<?php

namespace App\Backend\Model;

class Product {
    private $id;
    private $supplierId;
    private $name;
    private $costPrice;
    private $salePrice;
    private $description;
    private $isFavorite;
    private $category;
    private $isDonation;
    private $dateCreate;

    public function getId() {
        return $this->id;
    }
    public function getSupplierId() {
        return $this->supplierId;
    }
    public function getName() {
        return $this->name;
    }
    public function getCostPrice() {
        return $this->costPrice;
    }
    public function getSalePrice() {
        return $this->salePrice;
    }
    public function getDescription() {
        return $this->description;
    }
    public function getIsFavorite() {
        return $this->isFavorite;
    }
    public function getCategory() {
        return $this->category;
    }
    public function getIsDonation() {
        return $this->isDonation;
    }
    public function getDateCreate() {
        return $this->dateCreate;
    }
    public function setId($id) {
        $this->id = $id;
    }
    public function setSupplierId($supplierId) {
        $this->supplierId = $supplierId;
    }
    public function setName($name) {
        $this->name = $name;
    }
    public function setCostPrice($costPrice) {
        $this->costPrice = $costPrice;
    }
    public function setSalePrice($salePrice) {
        $this->salePrice = $salePrice;
    }
    public function setDescription($description) {
        $this->description = $description;
    }
    public function setIsFavorite($isFavorite) {
        $this->isFavorite = $isFavorite;
    }
    public function setCategory($category) {
        $this->category = $category;
    }
    public function setIsDonation($isDonation) {
        $this->isDonation = $isDonation;
    }
    public function setDateCreate($dateCreate) {
        $this->dateCreate = $dateCreate;
    }
}