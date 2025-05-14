<?php
namespace App\Backend\Service;

use App\Backend\Model\Sale;
use App\Backend\Model\Order;
use App\Backend\Repository\SaleRepository;
use App\Backend\Repository\OrderRepository;
use App\Backend\Repository\UserRepository;

use DomainException;
use DateTimeInterface;
use DateTime;
use InvalidArgumentException;

class SaleService {
    
    private SaleRepository $saleRepository;
    private OrderRepository $orderRepository;
    private UserRepository $userRepository;

    public function __construct(
        SaleRepository $saleRepository,
        OrderRepository $orderRepository,
        UserRepository $userRepository
    ) {
        $this->saleRepository = $saleRepository;
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
    }

    public function getSaleDetails(int $saleId): array
    {
        $saleData = $this->saleRepository->findWithOrders($saleId);
        if (!$saleData) {
            throw new DomainException("Venda não encontrada");
        }

        $sale = $this->hydrateSale($saleData);
        return $sale->toDetailedArray();
    }

    public function getByDate(DateTimeInterface $date): array 
    {
        return $this->saleRepository->findByDate($date);
    }

    public function getSalesBySeller(int $sellerId, ?string $status = null): array 
    {
        return $this->saleRepository->findBySeller($sellerId, $status);
    }

    public function getByStatus(string $status): array 
    {
        return $this->saleRepository->findByStatus($status);
    }

    public function getAll(): array 
    {
        return $this->saleRepository->findAll();
    }

    public function getSale(int $id): ?array 
    {
        return $this->saleRepository->find($id);
    }

    public function createSale(int $sellerId): Sale 
    {
        $seller = $this->userRepository->find($sellerId);
        if(!$seller) {
            throw new DomainException("Vendedor não encontrado.");
        }
        
        if (!$this->saleRepository->findOpenBySeller($sellerId)) 
        {
            throw new InvalidArgumentException("Já existe uma venda aberta para este vendedor");
        }

        $sale = new Sale(
            sellerId: $sellerId,
            date: new DateTime(),
            status: 'open'
        );

        $saleId = $this->saleRepository->create($sale);
        $sale->setId($saleId);

        return $sale;
    }

    public function addOrderToSale(int $saleId, int $orderId): Sale
    {
        $saleData = $this->saleRepository->findWithOrders($saleId);
        if (!$saleData) {
            throw new DomainException("Venda não encontrada");
        }

        $orderData = $this->orderRepository->find($orderId);
        if (!$orderData) {
            throw new DomainException("Pedido não encontrado");
        }

        $sale = $this->hydrateSale($saleData);
        
        $order = $this->hydrateOrder($orderData);
        
        if ($sale->getStatus() !== 'open') {
            throw new DomainException("Só é possível adicionar pedidos a vendas abertas");
        }

        if ($order->getStatus() !== 'paid') {
            throw new DomainException("Só é possível adicionar pedidos pagos");
        }
        
        $sale->addOrder($order);
        $this->saleRepository->update($sale);
        
        return $sale;
    }

    private function hydrateOrder(array $orderData): Order
    {
        return new Order(
            saleId: $orderData['sale_id'],
            status: $orderData['status'],
            paymentMethod: $orderData['payment_method'],
            totalAmount: $orderData['total_amount'],
            id: $orderData['id'],
            createdAt: new DateTime($orderData['created_at']),
            updatedAt: new DateTime($orderData['updated_at'])
        );
    }

    public function completeSale(int $saleId): Sale 
    {
        $saleData = $this->saleRepository->findWithOrders($saleId);
        if (!$saleData) {
            throw new DomainException("Venda não encontrada");
        }
        
        $sale = $this->hydrateSale($saleData);
        
        try {
            $sale->complete();
            $this->saleRepository->completeSale($saleId, $sale->getTotal());

            return $sale;

        } catch (DomainException $e) {
            throw new DomainException("Não foi possível concluir a venda: " . $e->getMessage());
        }
    }

    public function cancelSale(int $saleId) : Sale 
    {
        $saleData = $this->saleRepository->findWithOrders($saleId);
        if (!$saleData) {
            throw new DomainException("Venda não encontrada");
        }

        $sale = $this->hydrateSale($saleData);
        $sale->cancel();

        $this->saleRepository->update($sale);

        return $sale;
    }

    public function deleteSale(int $id): void
    {
        $sale = $this->saleRepository->find($id);
        if (!$sale) {
            throw new DomainException("Pedido não encontrado");
        }
        
        if ($sale['status'] === 'paid') {
            throw new DomainException("Pedidos pagos não podem ser removidos");
        }
        
        if (!$this->saleRepository->delete($id)) {
            throw new DomainException("Falha ao remover pedido");
        } 
    }

    private function hydrateSale(array $saleData): Sale
    {
        $sale = new Sale(
            sellerId: $saleData['seller_id'],
            date: new DateTime($saleData['date']),
            status: $saleData['status'],
            id: $saleData['id'],
            createdAt: new DateTime($saleData['created_at']),
            updatedAt: new DateTime($saleData['updated_at'])
        );
        
        if (!empty($saleData['orders'])) {
            foreach ($saleData['orders'] as $orderData) {
                $order = new Order(
                    paymentMethod: $orderData['payment_method'],
                    totalAmount: $orderData['total_amount'],
                    status: $orderData['status_id'],
                    id: $orderData['id'],
                    saleId: $orderData['sale_id'],
                    createdAt: new DateTime($orderData['created_at']),
                    updatedAt: new DateTime($orderData['updated_at'])
                );
                $sale->addOrder($order);
            }
        }
        
        return $sale;
    }
}