<?php

namespace App\Backend\Model;

use DateTime;
use DateTimeInterface;
use InvalidArgumentException;
use DomainException;

class ProductModel {
    private  $id;
    private  $supplierId;
    private  $name;
    private  $costPrice;
    private  $salePrice;
    private  $category;
    private  $description;
    private  $isFavorite;
    private  $isDonation;
    private  $img_product;
    private  $status;
    private  $createdAt;

    // Constructor 
  
    public function __construct() {
        $this->status = 1;
    }

    //getters
    public function getId(){ 
        return $this->id; 
    }
    public function getSupplierId(){ 
        return $this->supplierId; 
    }

    public function getName(){ 
        return $this->name; 
    }

    public function getCostPrice(){ 
        return $this->costPrice ; 
    }
    public function getSalePrice(){
        return $this->salePrice; 
    }

    public function getCategory(){
        return $this->category; 
    }

    public function getDescription(){
        return $this->description; 
    }

    public function getImgProduct(){
        return $this->img_product; 
    }

    public function getStatus(){
        return $this->status; 
    }

    public function getFavorite(){
        return $this->isFavorite; 
    }
    public function getDonation(){
        return $this->isDonation; 
    }

    public function getCreatedAt(){
        return $this->createdAt; 
    }

    //setters

    public function setId($id){
        $this->id = $id;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function setCostPrice($costPrice){
        $this->costPrice = $costPrice;
    }

    public function setSalePrice($salePrice){
        $this->salePrice = $salePrice;
    }

    public function setCategory($category){
        $this->category = $category;
    }

    public function setDescription($description){
        $this->description = $description;
    }

    public function setIsFavorite($isFavorite){
        $this->isFavorite = $isFavorite;
    }

    public function setIsDonation($isDonation){
        $this->isDonation = $isDonation;
    }

    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }


    public function calculateProfitMargin() : float
    {
        if ($this->getCostPrice() <= 0) {
            return 0.0;
        }
        return round((($this->salePrice - $this->getCostPrice()) / $this->getCostPrice()) * 100, 2);
    }

    public function updateFromArray(array $data): void
    {
        if (isset($data['name'])) $this->setName($data['name']);
        
        if (isset($data['cost_price'])) $this->setCostPrice((float)$data['cost_price']);
        
        if (isset($data['sale_price'])) $this->setSalePrice((float)$data['sale_price']);
        
        if (isset($data['category'])) $this->setCategory($data['category']);
        
        if (isset($data['description'])) $this->setDescription($data['description']);
        
        if (isset($data['is_favorite'])) $this->setIsFavorite((bool)$data['is_favorite']);
        
        if (isset($data['is_donation'])) $this->setIsDonation((bool)$data['is_donation']);
    }
    public function toArray(): array {
        return [
            'id' => $this->id,
            'supplier_id' => $this->supplierId,
            'name' => $this->name,
            'cost_price' => $this->getCostPrice(),
            'sale_price' => $this->salePrice,
            'category' => $this->category,
            'description' => $this->description,
            'is_favorite' => $this->isFavorite,
            'is_donation' => $this->isDonation,
            'profit_margin' => $this->calculateProfitMargin(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }
}