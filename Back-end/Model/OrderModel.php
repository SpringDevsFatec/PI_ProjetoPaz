<?php

namespace App\Backend\Model;

class OrderModel {

    // Atributos
    private $id;
    private $sale_id;
    private $code;
    private $payment_method;
    private $status;
    private $total_amount_order;
    private $created_at;
    private $updated_at;

    // Getters e Setters
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getSaleId() {
        return $this->sale_id;
    }

    public function setSaleId($sale_id) {
        $this->sale_id = $sale_id;
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function getPaymentMethod() {
        return $this->payment_method;
    }

    public function setPaymentMethod($payment_method) {
        $allowedMethods = ['credito', 'debito', 'dinheiro', 'pix'];
        if (in_array($payment_method, $allowedMethods)) {
            $this->payment_method = $payment_method;
        } else {
            throw new \InvalidArgumentException("Método de pagamento inválido: $payment_method");
        }
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getTotalAmountOrder() {
        return $this->total_amount_order;
    }

    public function setTotalAmountOrder($total_amount_order) {
        $this->total_amount_order = $total_amount_order;
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

    // Para serialização em JSON
    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'sale_id' => $this->sale_id,
            'code' => $this->code,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'total_amount_order' => $this->total_amount_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
