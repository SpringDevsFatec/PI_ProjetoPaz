<?php
namespace App\Backend\Service;

use App\Backend\Model\Order;
use App\Backend\Model\OrderItem;
use App\Backend\Repository\OrderRepository;
use App\Backend\Repository\SaleRepository;
use App\Backend\Repository\ProductRepository;

use DomainException;
use DateTime;
use InvalidArgumentException;

class OrderService {
    
    private OrderRepository $orderRepository;
    private SaleRepository $saleRepository;
    private ProductRepository $productRepository;

    public function __construct(
        OrderRepository $orderRepository,
        SaleRepository $saleRepository,
        ProductRepository $productRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->saleRepository = $saleRepository;
        $this->productRepository = $productRepository;
    }

    public function getWithItems(int $id): array
    {
        return $this->orderRepository->findWithItems($id);
    }

    public function getByPaymentMethod(string $paymentMethod): array
    {
        return $this->orderRepository->findByPaymentMethod($paymentMethod);
    }

    public function getAll(): array
    {
        return $this->orderRepository->findAll();
    }

    public function getOrder(int $id): ?array
    {
        return $this->orderRepository->find($id);
    }

    public function createOrder(int $sale_id, string $paymentMethod): Order
    {
        if (empty($paymentMethod))
        {
            throw new InvalidArgumentException("Dados incompletos.");
        }

        $sale = $this->saleRepository->getById($sale_id);
        if(!$sale) {
            throw new DomainException("Venda não encontrada.");
        }

        $order = new Order(
            saleId: $sale_id,
            status: 'open',
            paymentMethod: $paymentMethod,
            totalAmount: 0.0,
            id: null,
            createdAt: new DateTime(),
            updatedAt: new DateTime()
        );

        $orderId = $this->orderRepository->save($order);
        $order->setId($orderId);

        return $order;
    }

    public function addItemToOrder(int $orderId, int $productId, int $quantity): Order
    {
        $orderData = $this->orderRepository->find($orderId);
        if (!$orderData) {
            throw new DomainException("Pedido não encontrado");
        }
        
        if ($orderData['status'] !== 'open') {
            throw new DomainException("Só é possível adicionar itens a pedidos abertos");
        }
        
        $product = $this->productRepository->getById($productId);
        if (!$product) {
            throw new DomainException("Produto não encontrado");
        }
        
        $item = new OrderItem(
            productId: $productId,
            orderId: $orderId,
            quantity: $quantity,
            unitPrice: $product['current_price']
        );
        
        $order = $this->hydrateOrder($orderData);
        $order->addItem($item);
        
        $this->orderRepository->update($order);
        
        return $order;
    }

    public function updateOrderStatus(int $orderId, string $status): Order
    {
        $orderData = $this->orderRepository->findWithItems($orderId);
        if (!$orderData) {
            throw new DomainException("Pedido não encontrado");
        }
        
        $order = $this->hydrateOrder($orderData);
        
        switch ($status) {
            case 'paid':
                $order->markAsPaid();
                break;
            case 'canceled':
                $order->cancel();
                break;
            default:
                throw new InvalidArgumentException("Status inválido");
        }
        
        $this->orderRepository->update($order);
        
        return $order;
    }

    public function deleteOrder(int $orderId): void 
    {
        $order = $this->orderRepository->find($orderId);
        if (!$order) {
            throw new DomainException("Pedido não encontrado");
        }
        
        if ($order['status'] === 'paid') {
            throw new DomainException("Pedidos pagos não podem ser removidos");
        }
        
        if (!$this->orderRepository->delete($orderId)) {
            throw new DomainException("Falha ao remover pedido");
        } 
    }

    private function hydrateOrder(array $orderData): Order
    {
        $order = new Order(
            paymentMethod: $orderData['payment_method'],
            totalAmount: $orderData['total_amount'],
            saleId: $orderData['sale_id'],
            status: $orderData['status'],
            id: $orderData['id'],
            createdAt: new DateTime($orderData['created_at']),
            updatedAt: new DateTime($orderData['updated_at'])
        );
        
        if (!empty($orderData['items'])) {
            foreach ($orderData['items'] as $itemData) {
                $item = new OrderItem(
                    productId: $itemData['product_id'],
                    orderId: $itemData['order_id'],
                    quantity: $itemData['quantity'],
                    unitPrice: $itemData['unit_price'],
                    id: $itemData['id'],
                    createdAt: new DateTime($itemData['created_at']),
                    updatedAt: new DateTime($itemData['updated_at'])
                );
                $order->addItem($item);
            }
        }
        
        return $order;
    }
}