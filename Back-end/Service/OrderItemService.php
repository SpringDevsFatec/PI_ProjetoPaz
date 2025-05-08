<?php
namespace App\Backend\Service;

use App\Backend\Model\OrderItem;
use App\Backend\Repository\OrderItemRepository;
use App\Backend\Repository\ProductRepository;

use DomainException;
use DateTime;
use InvalidArgumentException;

class OrderItemService {
    
    private $orderItemRepository;
    private $productRepository;

    public function __construct(
        OrderItemRepository $orderItemRepository, 
        ProductRepository $productRepository
    ) {
        $this->orderItemRepository = $orderItemRepository;
        $this->productRepository = $productRepository;
    }

    public function getItemsWithProductDetails(int $orderId): array
    {
        return $this->orderItemRepository->findWithProductDetails($orderId);
    }
    public function getItemsByOrderId(int $orderId): array 
    {
        return $this->orderItemRepository->findByOrderId($orderId);
    }
    public function getItem(int $id): ?array
    {
        return $this->orderItemRepository->find($id);
    }

    public function createItem(array $data): OrderItem 
    {
        if (empty($data['product_id']) || empty($data['order_id']) || empty($data['quantity'])) 
        {
            throw new InvalidArgumentException("Dados incompletos.");
        }

        $product = $this->productRepository->find($data['product_id']);
        if(!$product) {
            throw new DomainException("Produto não encontrado.");
        }

        $orderItem = new OrderItem(
            productId: (int)$data['product_id'],
            orderId: (int)$data['order_id'],
            quantity: (int)$data['quantity'],
            unitPrice: (float)$data['unit_price'],
            id: null,
            createdAt: new DateTime(),
            updatedAt: new DateTime()
        );

        $itemId = $this->orderItemRepository->save($orderItem);
        $orderItem->setId($itemId);

        return $orderItem;
    }

    public function updateItemQuantity(int $itemId, int $newQuantity): OrderItem 
    {
        if ($newQuantity <= 0) {
            throw new InvalidArgumentException("Quantidade deve ser maior que zero.");
        }
        $existingItem = $this->orderItemRepository->find($itemId);
        if (!$existingItem) {
            throw new DomainException("Item de pedido não encontrado.");
        }
        $orderItem = new OrderItem(
            productId: (int)$existingItem['product_id'],
            orderId: (int)$existingItem['order_id'],
            quantity: $newQuantity,
            unitPrice: (float)$existingItem['unit_price'],
            id: (int)$existingItem['id'],
            createdAt: new DateTime($existingItem['created_at']),
            updatedAt: new DateTime()
        );

        $this->orderItemRepository->update($orderItem);
        
        return $orderItem;
    }

    public function deleteItem(int $itemId): void 
    {
        $orderItem = $this->orderItemRepository->find($itemId);
        if (!$orderItem) {
            throw new DomainException("Item de pedido não encontrado.");
        }

        if (!$this->orderItemRepository->delete($itemId)) {
            throw new DomainException("Falha ao deletar item.");
        } 
    }
}