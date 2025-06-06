<?php
namespace App\Backend\Service;

use App\Backend\Model\SaleModel;
use App\Backend\Model\OrderModel;
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

    public function getSalesByPeriod(DateTimeInterface $startDate, DateTimeInterface $endDate, ?int $sellerId = null): array 
    {
        if ($startDate > $endDate) {
            throw new InvalidArgumentException("Data final deve ser maior ou igual à data inicial");
        }

        return $this->saleRepository->findByDateRange($startDate, $endDate, $sellerId);
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

    public function createSale(int $sellerId): SaleModel 
    {
        $seller = $this->userRepository->getContentId($sellerId);
        if(!$seller) {
            throw new DomainException("Vendedor não encontrado.");
        }
        
        if (!$this->saleRepository->findOpenBySeller($sellerId)) 
        {
            throw new InvalidArgumentException("Já existe uma venda aberta para este vendedor");
        }

        $sale = new SaleModel(
            sellerId: $sellerId,
            date: new DateTime(),
            status: 'open'
        );

        $saleId = $this->saleRepository->create($sale);
        $sale->setId($saleId);

        return $sale;
    }

    public function addOrderToSale(int $saleId, int $orderId): SaleModel
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

    private function hydrateOrder(array $orderData): OrderModel
    {
        return new OrderModel(
            saleId: $orderData['sale_id'],
            status: $orderData['status'],
            paymentMethod: $orderData['payment_method'],
            totalAmount: $orderData['total_amount'],
            id: $orderData['id'],
            createdAt: new DateTime($orderData['created_at'])
        );
    }

    public function completeSale(int $saleId): SaleModel 
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

    public function cancelSale(int $saleId) : SaleModel 
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

    private function hydrateSale(array $saleData): SaleModel
    {
        $sale = new SaleModel(
            sellerId: $saleData['seller_id'],
            date: new DateTime($saleData['date']),
            status: $saleData['status'],
            id: $saleData['id'],
            createdAt: new DateTime($saleData['created_at'])
        );
        
        if (!empty($saleData['orders'])) {
            foreach ($saleData['orders'] as $orderData) {
                $order = new OrderModel(
                    paymentMethod: $orderData['payment_method'],
                    totalAmount: $orderData['total_amount'],
                    status: $orderData['status'],
                    id: $orderData['id'],
                    saleId: $orderData['sale_id'],
                    createdAt: new DateTime($orderData['created_at'])
                );
                $sale->addOrder($order);
            }
        }
        
        return $sale;
    }
}