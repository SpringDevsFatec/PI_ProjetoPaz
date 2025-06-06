<?php 

namespace App\Backend\Model;

use InvalidArgumentException;
use DomainException;

class OrderModel {

    private $id;
    private $saleId;
    private $status;
    private $paymentMethod;
    private $totalAmount;
    private $createdAt;

    private $items = [];

    const PAYMENT_METHODS = [
        'cash' => 'Dinheiro',
        'card' => 'Cartão',
        'pix' => 'PIX'
    ];

    const STATUSES = [
        'open' => 'Aberto',
        'pending' => 'Pendente',
        'paid' => 'Pago',
        'canceled' => 'Cancelado'
    ];

    public function getId() {
        return $this->id;
    }

    public function getSaleId() {
        return $this->saleId;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getPaymentMethod() {
        return $this->paymentMethod;
    }

    public function getTotalAmount() {
        return $this->totalAmount;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function setId(int $id) {
        $this->id = $id;
    }

    public function setSaleId(?int $saleId) {
        $this->saleId = $saleId;
    }

    public function setStatus(string $status) {
        if (!array_key_exists($status, self::STATUSES)) {
            throw new InvalidArgumentException("Status de pedido inválido: " . $status);
        }
        $this->status = $status;
    }

    public function setPaymentMethod(string $paymentMethod) {
        if (!array_key_exists($paymentMethod, self::PAYMENT_METHODS)) {
            throw new InvalidArgumentException("Método de pagamento inválido: " . $paymentMethod);
        }
        $this->paymentMethod = $paymentMethod;
    }

    public function markAsPaid() {
        if ($this->status === 'canceled') {
            throw new DomainException("Pedido cancelado não pode ser marcado como pago");
        }
        $this->setStatus('paid');
    }

    public function cancel() {
        if ($this->status === 'paid') {
            throw new DomainException("Pedido pago deve ser reembolsado, não cancelado");
        }
        $this->setStatus('canceled');
    }

    public function assignToSale(int $saleId) {
        if ($this->saleId !== null) {
            throw new DomainException("Pedido já vinculado a uma venda");
        }
        $this->setSaleId($saleId);
    }

    public function addItem(OrderItem $item) 
    {
        if ($this->status !== 'open') {
            throw new DomainException("Só é possível adicionar itens a pedidos abertos");
        }
        $this->items[] = $item;
        $this->calculateTotal();
    }

    public function removeItem(OrderItem $itemId) 
    {
        if ($this->status !== 'open') {
            throw new DomainException("Só é possível remover itens de pedidos abertos");
        }

        $this->items = array_filter($this->items, fn($item) => $item->getId() !== $itemId);
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->totalAmount = array_reduce(
            $this->items,
            fn(float $total, OrderItem $item) => $total + $item->getSubTotal(),
            0.0
        );
    }

    public function getItems() {
        return $this->items;
    }

    public function updateFromArray(array $data) {
        if (isset($data['payment_method'])) {
            $this->setPaymentMethod($data['payment_method']);
        }

        if (isset($data['status'])) {
            $this->setStatus($data['status']);
        }
    }
    public function toArray() {
        return [
            'id' => $this->id,
            'sale_id' => $this->saleId,
            'status' => $this->status,
            'status_label' => self::STATUSES[$this->status] ?? null,
            'payment_method' => $this->paymentMethod,
            'payment_method_label' => self::PAYMENT_METHODS[$this->paymentMethod] ?? null,
            'total_amount' => $this->totalAmount,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s')
        ];
    }
}