<?php 

namespace App\Backend\Model;

class Sale {
    private $id;
    private $sellerId;
    private $total;
    private $status;
    // 0 = pending, 1 = completed, 2 = cancelled
    private $dateCreate;

    public function getId() {
        return $this->id;
    }
    public function getSellerId() {
        return $this->sellerId;
    }
    public function getTotal() {
        return $this->total;
    }
    public function getStatus() {
        return $this->status;
    }
    public function getStatusText() {
        switch ($this->status) {
            case 0:
                return "Pending";
            case 1:
                return "Completed";
            case 2:
                return "Cancelled";
            default:
                return "Unknown";
        }
    }
    public function getDateCreate() {
        return $this->dateCreate;
    }
    public function setId($id) {
        $this->id = $id;
    }
    public function setSellerId($sellerId) {
        $this->sellerId = $sellerId;
    }
    public function setTotal($total) {
        $this->total = $total;
    }
    public function setStatus($status) {
        $this->status = $status;
    }
    public function setDateCreate($dateCreate) {
        $this->dateCreate = $dateCreate;
    }
}