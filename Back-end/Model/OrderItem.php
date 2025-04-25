<?php 

namespace App\Backend\Model;

class OrderItem {
    private $id;
    private $productId;
    private $orderId;
    private $quantity;
    private $unitPrice;

    public function getId() {
        return $this->id;
    }
    public function getProductId() {
        return $this->productId;
    }
    public function getOrderId() {
        return $this->orderId;
    }
    public function getQuantity() {
        return $this->quantity;
    }
    public function getUnitPrice() {
        return $this->unitPrice;
    }
    public function setId($id) {
        $this->id = $id;
    }
    public function setProductId($productId) {
        $this->productId = $productId;
    }
    public function setOrderId($orderId) {
        $this->orderId = $orderId;
    }
    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }
    public function setUnitPrice($unitPrice) {
        $this->unitPrice = $unitPrice;
    }
    public function getSubTotalPrice() {
        return $this->quantity * $this->unitPrice;
    }
    public function getSubTotalPriceFormatted() {
        return number_format($this->getSubTotalPrice(), 2, '.', '');
    }
}