<?php
namespace App\Backend\Service;

use App\Backend\Model\OrderItemModel;
use App\Backend\Repository\OrderItemRepository;
use App\Backend\Repository\ProductRepository;
use App\Backend\Utils\Responses;
use DomainException;
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

    public function createItem(OrderItemModel $data, int $orderId): array
    {
        $productId = $data->getProductId();
        $quantity = $data->getQuantity();
        $unitPrice = $data->getUnitPrice();
        
        if (!isset($productId) || !isset($quantity) || !isset($unitPrice)) {
            return $this->buildResponse(false, 'Dados incompletos.', null);
        }

        $product = $this->productRepository->find($data['itens']['product_id']);
        if(!$product) {
            throw new DomainException("Produto não encontrado.");
        }

        try {
            $newItemId = $this->orderItemRepository->createOrderItem($data, $orderId);
            
            if ($newItemId) {
                $newItem = $this->orderItemRepository->find($newItemId['content']['id']);
                return $this->buildResponse(true, 'Item criado com sucesso', $newItem);
            } else {
                return $this->buildResponse(false, 'Falha ao criar item.', null);
            }
        } catch (Exception $e) {
            throw new DomainException("Erro ao criar item de pedido: " . $e->getMessage());
        }
    }

    /*
    no update and delete method for OrderItem
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
    */
}