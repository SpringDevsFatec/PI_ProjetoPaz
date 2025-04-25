<?php 

namespace App\Backend\Model;

class Order {
    private $id;
    private $saleId;
    private $paymentMethod;
    private $dateCreate;

    public function getId() {
        return $this->id;
    }
    public function getSaleId() {
        return $this->saleId;
    }
    public function getPaymentMethod() {
        return $this->paymentMethod;
    }
    public function getDateCreate() {
        return $this->dateCreate;
    }
    public function setId($id) {
        $this->id = $id;
    }
    public function setSaleId($saleId) {
        $this->saleId = $saleId;
    }
    public function setPaymentMethod($paymentMethod) {
        $this->paymentMethod = $paymentMethod;
    }
    public function setDateCreate($dateCreate) {
        $this->dateCreate = $dateCreate;
    }
}