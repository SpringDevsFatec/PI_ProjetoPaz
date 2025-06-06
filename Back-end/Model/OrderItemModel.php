<?php 

namespace App\Backend\Model;

class OrderItemModel {
    private $id;
    private $productId;
    private $orderId;
    private $quantity;
    private $unitPrice;
    private $createdAt;

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
    public function getCreatedAt() {
        return $this->createdAt;
    }

    // setters

    public function setId(int $id) {
        $this->id = $id;
    }
    public function setQuantity(int $quantity) {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException("Quantidade deve ser maior que zero.");
        }
        $this->quantity = $quantity;
    }
    public function getSubTotal() {
        return $this->quantity * $this->unitPrice;
    }
    public function getSubTotalPriceFormatted() {
        return number_format($this->getSubTotal(), 2, '.', '');
    }
    public function updateFromArray(array $data): void {
        if (isset($data['quantity'])) {
            $this->setQuantity($data['quantity']);
        }
    }
    public function toArray(): array {
        return [
            'id' => $this->id,
            'product_id' => $this->productId,
            'order_id' => $this->orderId,
            'quantity' => $this->quantity,
            'unit_price' => $this->unitPrice,
            'subtotal' => $this->getSubTotal(),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s')
        ];
    }
}