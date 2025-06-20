<?php
namespace App\Backend\Service;

use App\Backend\Model\OrderModel;
use App\Backend\Model\OrderItemModel;
use App\Backend\Service\OrderItemService;
use App\Backend\Repository\OrderRepository;
use App\Backend\Repository\SaleRepository;
use App\Backend\Repository\ProductRepository;
use App\Backend\Utils\PatternText;
use App\Backend\Utils\Responses;
use App\Backend\Utils\CreateCodes;
use DomainException;
use Exception;

class OrderService {

    use Responses;
    
    private OrderRepository $orderRepository;
    private SaleRepository $saleRepository;
    private ProductRepository $productRepository;
    private OrderItemService $orderItemService;

    public function __construct(
        OrderRepository $orderRepository,
        SaleRepository $saleRepository,
        ProductRepository $productRepository,
        OrderItemService $orderItemService
    ) {
        $this->orderRepository = $orderRepository;
        $this->saleRepository = $saleRepository;
        $this->productRepository = $productRepository;
        $this->orderItemService = $orderItemService;
    }

    public function getWithItems(int $id): array
    {
        try {
            $this->orderRepository->beginTransaction();
            $response = $this->orderRepository->findWithItems($id);
            $this->orderRepository->commitTransaction();
            
            if ($response['status'] == true) {
                return $this->buildResponse(true, 'Conteúdo encontrado.', $response['content']);
            }

            return $this->buildResponse(false, 'Nenhum conteúdo encontrado.', null);

        } catch (Exception $e) {
            $this->orderRepository->rollBackTransaction();
            throw $e;
        }
    }

    public function getByPaymentMethod(string $paymentMethod): array
    {
        try {
            $this->orderRepository->beginTransaction();
            $response = $this->orderRepository->findByPaymentMethod($paymentMethod);
            $this->orderRepository->commitTransaction();
            
            if ($response['status'] == true) {
                return $this->buildResponse(true, 'Conteúdo encontrado.', $response['content']);
            }

            return $this->buildResponse(false, 'Nenhum conteúdo encontrado.', null);

        } catch (Exception $e) {
            $this->orderRepository->rollBackTransaction();
            throw $e;
        }
    }

    public function getAll(): array
    {
        try {
            $this->orderRepository->beginTransaction();
            $response = $this->orderRepository->findAll();
            $this->orderRepository->commitTransaction();
            
            if ($response['status'] == true) {
                return $this->buildResponse(true, 'Conteúdo encontrado.', $response['content']);
            }

            return $this->buildResponse(false, 'Nenhum conteúdo encontrado.', null);

        } catch (Exception $e) {
            $this->orderRepository->rollBackTransaction();
            throw $e;
        }
    }

    public function getOrder(int $id): ?array
    {
        try {
            $this->orderRepository->beginTransaction();
            $response = $this->orderRepository->find($id);
            $this->orderRepository->commitTransaction();
            
            if ($response['status'] == true) {
                return $this->buildResponse(true, 'Conteúdo encontrado.', $response['content']);
            }

            return $this->buildResponse(false, 'Nenhum conteúdo encontrado.', null);

        } catch (Exception $e) {
            $this->orderRepository->rollBackTransaction();
            throw $e;
        }
    }

    public function createOrder(int $saleId, $data)
    {
        PatternText::validateOrderData($data);
        PatternText::processText($data);

        $sale = $this->saleRepository->find($saleId);
        if (!$sale) {
            throw new DomainException("Venda não encontrada.");
        }

        $code = CreateCodes::createCodes('OR');

        $order = new OrderModel();
        $order->setSaleId($saleId);
        $order->setCode($code['content']);
        $order->setPaymentMethod($data['payment_method']);

        try {
            $this->orderRepository->beginTransaction();

            $responseOrder = $this->orderRepository->createOrder($order);
            if (!$responseOrder['status']) {
                return $this->buildResponse(false, 'Erro ao criar Pedido', null);
            }

            $orderId = $responseOrder['content']->getId();
            if (!$orderId) {
                return $this->buildResponse(false, 'Id não retornado do Pedido!', null);
            }

            $order->setId($orderId);

            if (empty($data['itens'])) {
                return $this->buildResponse(false, 'Nenhum item adicionado ao pedido.', null);
            }

            $itensCriados = [];

            foreach ($data['itens'] as $itemData) {
                $item = new OrderItemModel();
                $item->setProductId($itemData['product_id']);
                $item->setOrderId($order);
                $item->setQuantity($itemData['quantity']);
                $item->setUnitPrice($itemData['unit_price']);

                $itemResponse = $this->orderItemService->createItem($item, $orderId);
                if (!$itemResponse['status']) {
                    throw new DomainException("Erro ao criar item: {$itemData['product_id']}");
                }

                $order->addItem($itemResponse['content']);

                $itensCriados[] = [
                    'id' => $itemResponse['content']->getId(),
                    'product_id' => $itemData['product_id'],
                    'order_id' => $order->getId(),
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                ];
            }
            $this->orderRepository->updateTotalAmount($order);

            $this->orderRepository->commitTransaction();

            return $this->buildResponse(true, 'Pedido criado com sucesso', [
                'id' => $order->getId(),
                'sale_id' => $order->getSaleId(),
                'code' => $order->getCode(),
                'payment_method' => $order->getPaymentMethod(),
                'total_amount_order' => $order->getTotalAmountOrder(),
                'itens' => $itensCriados,
            ]);

        } catch (Exception $e) {
            $this->orderRepository->rollBackTransaction();
            throw $e;
        }
    }

    public function cancelOrder(int $id)
    {
        $response = $this->orderRepository->find($id);
        if ($response['status'] === false) {
            throw new DomainException("Pedido não encontrado");
        }
        
        $order = new OrderModel();
        $order->setId($id);
        $order->setStatus('cancelled');
        $this->orderRepository->beginTransaction();

        try {
            $result = $this->orderRepository->updateStatus($order);

            if ($result['status'] === true) {
                $this->orderRepository->commitTransaction();
                return $this->buildResponse(true, 'Status atualizado com sucesso', null);
            } else {
                $this->orderRepository->rollBackTransaction();
                return $this->buildResponse(false, 'Erro ao atualizar.', null);
            }

        } catch (Exception $e) {
            $this->orderRepository->rollBackTransaction();
            throw $e;
        }
    }

    /*
    public function addItemToOrder(int $orderId, int $productId, int $quantity): OrderModel
    {
        $orderData = $this->orderRepository->find($orderId);
        if (!$orderData) {
            throw new DomainException("Pedido não encontrado");
        }
        
        if ($orderData['status'] !== 'open') {
            throw new DomainException("Só é possível adicionar itens a pedidos abertos");
        }
        
        $product = $this->productRepository->find($productId);
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

    
    private function hydrateOrder(array $orderData): OrderModel
    {
        $order = new OrderModel(
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
    */
}