<?php 

namespace App\Backend\Model;

use InvalidArgumentException;
use DomainException;

class SaleModel {
    private $id;
    private $sellerId;
    private $total;
    private $status;
    private $date;
    private $createdAt;
    private $orders = [];

    const STATUSES = [
        'open' => 'Aberta',
        'completed' => 'Concluída',
        'canceled' => 'Cancelada'
    ];

    public function getId() {
        return $this->id;
    }
    
    public function getSellerId() {
        return $this->sellerId;
    }
    
    public function getDate() {
        return $this->date;
    }

    public function getDateFormatted(string $format = 'Y-m-d') {
        return $this->date->format($format);
    }

    public function getTotal() {
        return round($this->total, 2);
    }
    public function getStatus() {
        return $this->status;
    }
    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function getOrders() {
        return $this->orders;
    }

    public function setId(?int $id) {
        $this->id = $id;
    }

    public function setStatus(string $status) {
        if (!array_key_exists($status, self::STATUSES)) {
            throw new InvalidArgumentException("Status de venda inválido: " . $status);
        }
        $this->status = $status;
    }

    public function addOrder(OrderModel $order): void 
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

    public function removeOrder(OrderModel $orderId): void 
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
            fn(float $total, OrderModel $order) => $total + $order->getTotalAmount(),
            0.0
        );
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
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s')
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