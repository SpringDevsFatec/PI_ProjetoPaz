<?php 

namespace App\Backend\Model;

use DateTime;
use DateTimeInterface;
use InvalidArgumentException;
use DomainException;

class Sale {
    private ?int $id;
    private int $sellerId;
    private float $total = 0.0;
    private string $status = 'open';
    private ?DateTimeInterface $date;
    private ?DateTimeInterface $createdAt;
    private ?DateTimeInterface $updatedAt;
    private array $orders = [];

    const STATUSES = [
        'open' => 'Aberta',
        'completed' => 'Concluída',
        'canceled' => 'Cancelada'
    ];

    public function __construct(
        int $sellerId,
        DateTimeInterface $date,
        float $totalAmount = 0.0,
        string $status = 'open',
        ?int $id = null,
        ?DateTimeInterface $createdAt = null,
        ?DateTimeInterface $updatedAt = null
    ) {
        $this->id = $id;
        $this->sellerId = $sellerId;
        $this->date = $date;
        $this->total = 0.0;
        $this->setStatus($status);
        $this->createdAt = $createdAt ?? new DateTime();
        $this->updatedAt = $updatedAt ?? new DateTime();
    }

    public function getId(): ?int {
        return $this->id;
    }
    
    public function getSellerId(): int {
        return $this->sellerId;
    }
    
    public function getDate() : DateTimeInterface {
        return $this->date;
    }

    public function getDateFormatted(string $format = 'Y-m-d') : string {
        return $this->date->format($format);
    }

    public function getTotal(): float {
        return round($this->total, 2);
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

    public function getOrders(): array {
        return $this->orders;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function setStatus(string $status): void {
        if (!array_key_exists($status, self::STATUSES)) {
            throw new InvalidArgumentException("Status de venda inválido: " . $status);
        }
        $this->status = $status;
        $this->updatedAt = new DateTime();
    }

    public function addOrder(Order $order): void 
    {
        if ($this->status !== 'open') {
            throw new DomainException("Só é possível adicionar pedidos a vendas abertas");
        }
        if ($this->getStatus() !== 'paid') {
            throw new DomainException("Só é possível adicionar pedidos pagos à venda");
        }
        $this->orders[] = $order;
        $this->calculateTotal();
    }

    public function removeOrder(Order $orderId): void 
    {
        if ($this->status !== 'open') {
            throw new DomainException("Só é possível remover pedidos de vendas abertas");
        }

        $this->orders = array_filter($this->orders, 
            fn($order) => $order->getId() !== $orderId
        );
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

    /*
    public function open(): void {
        if ($this->status === 'calcelled') {
            throw new DomainException("Venda cancelada não pode ser aberta novamente");
        }
        $this->setStatus('open');
    }
    */

    public function complete(): void {
        if ($this->status === 'canceled') {
            throw new DomainException("Venda cancelada não pode ser concluída");
        }

        /*
        if (empty($this->orders)) {
            throw new DomainException("Não é possível concluir venda sem pedidos");
        }
        */

        $this->setStatus('completed');
    }

    public function cancel(): void {
        if ($this->status === 'completed') {
            throw new DomainException("Venda concluída não pode ser cancelada");
        }
        $this->setStatus('canceled');
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'seller_id' => $this->sellerId,
            'date' => $this->date->format('Y-m-d'),
            //'date_formatted' => $this->date->format('d/m/Y'),
            'total' => $this->getTotal(),
            'status' => $this->status,
            'status_label' => self::STATUSES[$this->status],
            'orders_count' => count($this->orders),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s')
        ];
    }

    public function toDetailedArray(): array {
        $data = $this->toArray();
        $data['orders'] = array_map(
            fn($order) => $order->toArray(),
            $this->orders
        );
        return $data;
    }
}