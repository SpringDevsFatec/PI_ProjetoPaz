<?php 

namespace App\Backend\Model;

use DateTime;
use DateTimeInterface;

class OrderItem {
    private ?int $id;
    private int $productId;
    private int $orderId;
    private int $quantity;
    private float $unitPrice;
    private ?DateTimeInterface $createdAt;
    private ?DateTimeInterface $updateAt;

    public function __construct(
        int $productId,
        int $orderId,
        int $quantity,
        float $unitPrice,
        ?int $id = null,
        ?DateTimeInterface $createdAt = null,
        ?DateTimeInterface $updateAt = null
    ) {
        $this->id = $id;
        $this->productId = $productId;
        $this->orderId = $orderId;
        $this->setQuantity($quantity);
        $this->unitPrice = $unitPrice;
        $this->createdAt = $createdAt ?? new DateTime();
        $this->updateAt = $updateAt ?? new DateTime();
    }

    public function getId(): ?int {
        return $this->id;
    }
    public function getProductId(): int {
        return $this->productId;
    }
    public function getOrderId(): int {
        return $this->orderId;
    }
    public function getQuantity(): int {
        return $this->quantity;
    }
    public function getUnitPrice(): float {
        return $this->unitPrice;
    }
    public function getCreatedAt(): ?DateTimeInterface {
        return $this->createdAt;
    }
    public function getUpdateAt(): ?DateTimeInterface {
        return $this->updateAt;
    }
    public function setId(int $id): void {
        $this->id = $id;
    }
    public function setQuantity(int $quantity): void {
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
        $this->updateAt = new DateTime();
    }
    public function toArray(): array {
        return [
            'id' => $this->id,
            'product_id' => $this->productId,
            'order_id' => $this->orderId,
            'quantity' => $this->quantity,
            'unit_price' => $this->unitPrice,
            'subtotal' => $this->getSubTotal(),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updateAt?->format('Y-m-d H:i:s')
        ];
    }
}