<?php 

namespace App\Backend\Model;

use DateTime;
use DateTimeInterface;
use InvalidArgumentException;
use DomainException;

class Sale {
    private ?int $id;
    //private int $sellerId;
    private float $total = 0.0;
    private string $status = 'open';
    private ?DateTimeInterface $createdAt;
    private ?DateTimeInterface $updatedAt;

    private array $orders = [];

    const STATUSES = [
        'open' => 'Aberta',
        'completed' => 'Concluída',
        'cancelled' => 'Cancelada'
    ];

    public function __construct(
        ?int $id,
        //int $sellerId,
        float $total = 0.0,
        string $status = 'open',
        ?DateTimeInterface $createdAt = null,
        ?DateTimeInterface $updatedAt = null
    ) {
        $this->id = $id;
        //$this->sellerId = $sellerId;
        $this->total = $total;
        $this->status = $status;
        $this->createdAt = $createdAt ?? new DateTime();
        $this->updatedAt = $updatedAt ?? new DateTime();
    }

    public function getId(): ?int {
        return $this->id;
    }
    /*
    public function getSellerId(): int {
        return $this->sellerId;
    }
    */
    public function getTotal(): float {
        return $this->total;
    }
    public function getStatus(): string {
        return $this->status;
    }
    public function getCreatedAt(): ?DateTimeInterface {
        return $this->createdAt;
    }
    public function getUpdatedAt(): ?DateTimeInterface {
        return $this->updatedAt;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }
    /*
    public function setSellerId(int $sellerId): void {
        $this->sellerId = $sellerId;
    }
    */
    public function setTotal(float $total): void {
        $this->total = $total;
    }

    public function setStatus(string $status): void {
        if (!array_key_exists($status, self::STATUSES)) {
            throw new InvalidArgumentException("Status de pedido inválido: " . $status);
        }
        $this->status = $status;
        $this->updatedAt = new DateTime();
    }

    public function open(): void {
        if ($this->status === 'calcelled') {
            throw new DomainException("Venda cancelada não pode ser aberta novamente");
        }
        $this->setStatus('open');
    }

    public function complete(): void {
        if ($this->status === 'calcelled') {
            throw new DomainException("Venda cancelada não pode ser concluída");
        }
        $this->setStatus('completed');
    }

    public function cancel(): void {
        if ($this->status === 'completed') {
            throw new DomainException("Venda concluída não pode ser cancelada");
        }
        $this->setStatus('cancelled');
    }

    public function addOrder(Order $order): void 
    {
        if ($this->status !== 'open') {
            throw new DomainException("Só é possível adicionar pedidos a vendas abertas");
        }
        $this->orders[] = $order;
        $this->calculateTotal();
    }

    public function removeItem(Order $orderId): void 
    {
        if ($this->status !== 'open') {
            throw new DomainException("Só é possível remover itens de pedidos abertos");
        }

        $this->orders = array_filter($this->orders, fn($order) => $order->getId() !== $orderId);
        $this->calculateTotal();
    }

    public function calculateTotal(): void
    {
        $this->total = array_reduce(
            $this->orders,
            fn(float $total, Order $order) => $total + $order->getTotalAmount(),
            0.0
        );
        $this->updatedAt = new DateTime();
    }

    public function getOrders(): array {
        return $this->orders;
    }

    public function updateFromArray(array $data): void {

        if (isset($data['status'])) {
            $this->setStatus($data['status']);
        }
    }
    public function toArray(): array {
        return [
            'id' => $this->id,
            //'seller_id' => $this->sellerId,
            'total' => $this->total,
            'status' => $this->status,
            'status_label' => self::STATUSES[$this->status] ?? null,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s')
        ];
    }
}