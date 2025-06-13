<?php 

namespace App\Backend\Model;

use DateTime;
use DateTimeInterface;
use InvalidArgumentException;
use DomainException;

class Order {

    private ?int $id;
    private ?int $saleId;
    private string $status = 'open';
    private string $paymentMethod;
    private float $totalAmount = 0.0;
    private ?DateTimeInterface $createdAt;
    private ?DateTimeInterface $updatedAt;

    private array $items = [];

    const PAYMENT_METHODS = [
        'cash' => 'Dinheiro',
        'card' => 'Cartão',
        'pix' => 'PIX'
    ];

    const STATUSES = [
        'open' => 'Aberto',
        'pending' => 'Pendente',
        'paid' => 'Pago',
        'cancelled' => 'Cancelado'
    ];

    public function __construct(
        string $paymentMethod,
        float $totalAmount = 0.0,
        ?int $saleId = null,
        string $status = 'open',
        ?int $id = null,
        ?DateTimeInterface $createdAt = null,
        ?DateTimeInterface $updatedAt = null
    ) {
        $this->id = $id;
        $this->setSaleId($saleId);
        $this->setStatus($status);
        $this->setPaymentMethod($paymentMethod);
        $this->totalAmount = 0.0;
        $this->createdAt = $createdAt ?? new DateTime();
        $this->updatedAt = $updatedAt ?? new DateTime();  
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getSaleId(): ?int {
        return $this->saleId;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function getPaymentMethod(): string {
        return $this->paymentMethod;
    }

    public function getTotalAmount(): float {
        return $this->totalAmount;
    }

    public function getCreatedAt(): ?DateTimeInterface {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface {
        return $this->updatedAt;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setSaleId(?int $saleId): void {
        $this->saleId = $saleId;
    }

    public function setStatus(string $status): void {
        if (!array_key_exists($status, self::STATUSES)) {
            throw new InvalidArgumentException("Status de pedido inválido: " . $status);
        }
        $this->status = $status;
        $this->updatedAt = new DateTime();
    }

    public function setPaymentMethod(string $paymentMethod): void {
        if (!array_key_exists($paymentMethod, self::PAYMENT_METHODS)) {
            throw new InvalidArgumentException("Método de pagamento inválido: " . $paymentMethod);
        }
        $this->paymentMethod = $paymentMethod;
        $this->updatedAt = new DateTime();
    }

    public function markAsPaid(): void {
        if ($this->status === 'canceled') {
            throw new DomainException("Pedido cancelado não pode ser marcado como pago");
        }
        $this->setStatus('paid');
    }

    public function cancel(): void {
        if ($this->status === 'paid') {
            throw new DomainException("Pedido pago deve ser reembolsado, não cancelado");
        }
        $this->setStatus('canceled');
    }

    public function assignToSale(int $saleId): void {
        if ($this->saleId !== null) {
            throw new DomainException("Pedido já vinculado a uma venda");
        }
        $this->setSaleId($saleId);
    }

    public function addItem(OrderItem $item): void 
    {
        if ($this->status !== 'open') {
            throw new DomainException("Só é possível adicionar itens a pedidos abertos");
        }
        $this->items[] = $item;
        $this->calculateTotal();
    }

    public function removeItem(OrderItem $itemId): void 
    {
        if ($this->status !== 'open') {
            throw new DomainException("Só é possível remover itens de pedidos abertos");
        }

        $this->items = array_filter($this->items, fn($item) => $item->getId() !== $itemId);
        $this->calculateTotal();
    }

    public function calculateTotal(): void
    {
        $this->totalAmount = array_reduce(
            $this->items,
            fn(float $total, OrderItem $item) => $total + $item->getSubTotal(),
            0.0
        );
        $this->updatedAt = new DateTime();
    }

    public function getItems(): array 
    {
        return $this->items;
    }

    public function updateFromArray(array $data): void {
        if (isset($data['payment_method'])) {
            $this->setPaymentMethod($data['payment_method']);
        }

        if (isset($data['status'])) {
            $this->setStatus($data['status']);
        }
    }
    public function toArray(): array {
        return [
            'id' => $this->id,
            'sale_id' => $this->saleId,
            'status' => $this->status,
            'status_label' => self::STATUSES[$this->status] ?? null,
            'payment_method' => $this->paymentMethod,
            'payment_method_label' => self::PAYMENT_METHODS[$this->paymentMethod] ?? null,
            'total_amount' => $this->totalAmount,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s')
        ];
    }
}