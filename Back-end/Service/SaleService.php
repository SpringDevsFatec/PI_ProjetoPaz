<?php
namespace App\Backend\Service;

use App\Backend\Model\Sale;
use App\Backend\Repository\SaleRepository;
use App\Backend\Model\Order;

use DomainException;
use DateTimeInterface;
use DateTime;
use InvalidArgumentException;

class SaleService {
    
    private SaleRepository $saleRepository;

    public function __construct(
        SaleRepository $saleRepository    
    ) {
        $this->saleRepository = $saleRepository;
    }

    public function getWithOrders(int $id): array 
    {
        return $this->saleRepository->findWithOrders($id);
    }

    public function getByDate(DateTimeInterface $createdAt): array 
    {
        return $this->saleRepository->findByDate($createdAt);
    }

    /*
    public function getBySeller(int $sellerId): array 
    {
        return $this->saleRepository->findBySeller($sellerId);
    }
    */

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

    public function createSale($data): Sale 
    {
        /*
        $seller = $this->sellerRepository->find($seller_id);
        if(!$seller) {
            throw new DomainException("Vendedor não encontrado.");
        }
        */

        $sale = new Sale(
            //sellerId: $seller_id,
            total: 0.0,
            status: 'open',
            id: null,
            createdAt: new DateTime(),
            updatedAt: new DateTime()
        );

        $saleId = $this->saleRepository->openSale($sale);
        $sale->setId($saleId);

        return $sale;
    }

    public function addOrderToSale(int $saleId, string $status, string $paymentMethod, float $totalAmount): Sale
    {
        $saleData = $this->saleRepository->find($saleId);
        if (!$saleData) {
            throw new DomainException("Venda não encontrada");
        }
        
        if ($saleData['status'] !== 'open') {
            throw new DomainException("Só é possível adicionar pedidos a venda aberta");
        }
        
        $order = new Order(
            saleId: $saleId,
            status: $status,
            paymentMethod: $paymentMethod,
            totalAmount: $totalAmount
        );
        
        $sale = $this->hydrateSale($saleData);
        $sale->addOrder($order);
        
        $this->saleRepository->update($sale);
        
        return $sale;
    }

    public function updateSaleStatus(int $saleId, string $status): Sale 
    {
        $saleData = $this->saleRepository->findWithOrders($saleId);
        if (!$saleData) {
            throw new DomainException("Venda não encontrada");
        }
        
        $sale = $this->hydrateSale($saleData);
        
        switch ($status) {
            case 'open':
                $sale->open();
                break;
            case 'completee':
                $sale->complete();
                break;
            case 'cancelled':
                $sale->cancel();
                break;
            default:
                throw new InvalidArgumentException("Status inválido");
        }
        
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
            total: $saleData['total'],
            status: $saleData['status'],
            id: $saleData['id'],
            createdAt: new DateTime($saleData['created_at']),
            updatedAt: new DateTime($saleData['updated_at'])
        );
        
        if (!empty($saleData['orders'])) {
            foreach ($saleData['orders'] as $orderData) {
                $order = new Order(
                    saleId: $orderData['sale_id'],
                    status: $orderData['order_id'],
                    paymentMethod: $orderData['payment_method'],
                    totalAmount: $orderData['total_amount'],
                    id: $orderData['id'],
                    createdAt: new DateTime($orderData['created_at']),
                    updatedAt: new DateTime($orderData['updated_at'])
                );
                $sale->addOrder($order);
            }
        }
        
        return $sale;
    }
}