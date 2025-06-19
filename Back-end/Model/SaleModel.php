<?php

namespace App\Backend\Model;

class SaleModel {

    // Atributos
    private $id;
    private $code;
    private $img_sale;
    private $total_amount_sale;
    private $status;
    private $method;
    private $created_at;
    private $updated_at;
    private $user_id;

    // Getters e Setters
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function getImgSale() {
        return $this->img_sale;
    }

    public function setImgSale($img_sale) {
        $this->img_sale = $img_sale;
    }

    public function getTotalAmountSale() {
        return $this->total_amount_sale;
    }

    public function setTotalAmountSale($total_amount_sale) {
        $this->total_amount_sale = $total_amount_sale;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getMethod() {
        return $this->method;
    }

    public function setMethod($method) {
        $allowedMethods = ['auto', 'manual'];
        if (in_array($method, $allowedMethods)) {
            $this->method = $method;
        } else {
            throw new \InvalidArgumentException("Método inválido: $method");
        }
    }

    public function setStatus($status) {
        $allowedStatuses = ['pending', 'completed', 'cancelled'];
        if (in_array($status, $allowedStatuses)) {
            $this->status = $status;
        } else {
            throw new \InvalidArgumentException("Status inválido: $status");
        }
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    public function getUpdatedAt() {
        return $this->updated_at;
    }

    public function setUpdatedAt($updated_at) {
        $this->updated_at = $updated_at;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    // Para serialização em JSON
    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'img_sale' => $this->img_sale,
            'total_amount_sale' => $this->total_amount_sale,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user_id' => $this->user_id,
        ];
    }
}
