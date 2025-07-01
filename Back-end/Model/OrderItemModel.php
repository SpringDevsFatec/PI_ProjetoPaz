<?php

namespace App\Backend\Model;

class OrderItemModel {

    // Atributos
    private $id;
    private $product_id;
    private $order_id;
    private $quantity;
    private $unit_price;

    // Getters e Setters
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getProductId() {
        return $this->product_id;
    }

    public function setProductId($product_id) {
        $this->product_id = $product_id;
    }

    public function getOrderId() {
        return $this->order_id;
    }

    public function setOrderId($order_id) {
        $this->order_id = $order_id;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    public function getUnitPrice() {
        return $this->unit_price;
    }

    public function setUnitPrice($unit_price) {
        $this->unit_price = $unit_price;
    }

    // Para serialização em JSON
    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'order_id' => $this->order_id,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price
        ];
    }
}
