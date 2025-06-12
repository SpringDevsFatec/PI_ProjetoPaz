<?php
namespace App\Backend\Service;

use App\Backend\Model\OrderItemModel;
use App\Backend\Repository\OrderItemRepository;
use App\Backend\Repository\ProductRepository;
use App\Backend\Utils\Responses;
use DomainException;
use DateTime;
use InvalidArgumentException;
use Exception;

class OrderItemService {
    
    use Responses;

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
        try {
            $this->orderItemRepository->beginTransaction();
            $response = $this->orderItemRepository->findWithProductDetails($orderId);
            $this->orderItemRepository->commitTransaction();
            
            if ($response['status'] == true) {
                return $this->buildResponse(true, 'Conteúdo encontrado.', $response['content']);
            }

            return $this->buildResponse(false, 'Nenhum conteúdo encontrado.', null);

        } catch (Exception $e) {
            $this->orderItemRepository->rollBackTransaction();
            throw $e;
        }
    }
    
    public function getItem(int $id): ?array
    {
        try {
            $this->orderItemRepository->beginTransaction();

            $response = $this->orderItemRepository->find($id);
            if ($response['status'] == true) {
                return $this->buildResponse(true, 'Conteúdo encontrado.', $response['content']);
            }

            return $this->buildResponse(false, 'Nenhum conteúdo encontrado.', null);
            
            $this->orderItemRepository->commitTransaction();

        } catch (Exception $e) {
            $this->orderItemRepository->rollBackTransaction();
            throw $e;
        }
    }

    public function createItem(array $data): OrderItemModel 
    {
        if (empty($data['product_id']) || empty($data['order_id']) || empty($data['quantity'])) 
        {
            throw new InvalidArgumentException("Dados incompletos.");
        }

        $product = $this->productRepository->find($data['product_id']);
        if(!$product) {
            throw new DomainException("Produto não encontrado.");
        }

        $orderItem = new OrderItemModel(
            productId: (int)$data['product_id'],
            orderId: (int)$data['order_id'],
            quantity: (int)$data['quantity'],
            unitPrice: (float)$data['unit_price'],
            id: null,
            createdAt: new DateTime()
        );

        $itemId = $this->orderItemRepository->save($orderItem);
        $orderItem->setId($itemId);

        return $orderItem;
    }

    public function updateItemQuantity(int $itemId, int $newQuantity): OrderItemModel 
    {
        if ($newQuantity <= 0) {
            throw new InvalidArgumentException("Quantidade deve ser maior que zero.");
        }
        $existingItem = $this->orderItemRepository->find($itemId);
        if (!$existingItem) {
            throw new DomainException("Item de pedido não encontrado.");
        }
        $orderItem = new OrderItemModel(
            productId: (int)$existingItem['product_id'],
            orderId: (int)$existingItem['order_id'],
            quantity: $newQuantity,
            unitPrice: (float)$existingItem['unit_price'],
            id: (int)$existingItem['id'],
            createdAt: new DateTime($existingItem['created_at'])
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