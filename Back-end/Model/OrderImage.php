<?php

namespace App\Backend\Model;

class OrderImage {
    private $id;
    private $orderId;
    private $imagePath;

    public function getId() {
        return $this->id;
    }

    public function getOrderId() {
        return $this->orderId;
    }

    public function getImagePath() {
        return $this->imagePath;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setOrderId($orderId) {
        $this->orderId = $orderId;
    }

    public function setImagePath($imagePath) {
        $this->imagePath = $imagePath;
    }
}