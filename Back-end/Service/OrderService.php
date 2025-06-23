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

    public function getByPaymentMethodAndSaleId(string $paymentMethod, int $saleId): array
    {
        try {
            $this->orderRepository->beginTransaction();
            $response = $this->orderRepository->findByPaymentMethodAndSaleId($paymentMethod, $saleId);
            $this->orderRepository->commitTransaction();
            
            if ($response['status'] == true) {
                return $this->buildResponse(true, 'Conteúdo encontrado.', $response['content']);
            }

            return $this->buildResponse(false, 'Não a Nenhum Pedido com este metodo dentro dessa venda!.', null);

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

       public function getOrderBySaleId(int $saleId): array
    {
        try {
            $this->orderRepository->beginTransaction();
            $response = $this->orderRepository->findBySaleId($saleId);
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
            return $this->buildResponse(false, 'Venda não encontrada.', null);
        }

        // Sum TotalOrder by $data(json['itens']) from Front-End
        $totalAmount = $this->countTotalAmountOrder($data['itens']);
    
        // generate Trigger
        $code = CreateCodes::createCodes('OR');

        // Create Model Order
        $order = new OrderModel();
        $order->setSaleId($saleId);
        $order->setCode($code['content']);
        $order->setStatus('completed');
        $order->setPaymentMethod($data['payment_method']);
        $order->setTotalAmountOrder($totalAmount);
        
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

            $itensCreated = $this->createItensOrders($data, $order, $orderId);

            if ($itensCreated['status'] === false) {
                return $itensCriados; // response created for buildResponse into the method
            }

            $itensCriados = $itensCreated['content'];


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
            return $this->buildResponse(false, 'Pedido não encontrado', null);
        }
        
        $order = new OrderModel();
        $order->setId($id);
        $order->setStatus('cancelled');
        $this->orderRepository->beginTransaction();

        // get response from find() and add new status
      $response['content']['status'] = $order->getStatus();
        try {
            $result = $this->orderRepository->updateStatus($order);

            if ($result['status'] === true) {
                $this->orderRepository->commitTransaction();
                return $this->buildResponse(true, 'Status atualizado com sucesso', $response);
            } else {
                $this->orderRepository->rollBackTransaction();
                return $this->buildResponse(false, 'Erro ao atualizar.', null);
            }

        } catch (Exception $e) {
            $this->orderRepository->rollBackTransaction();
            throw $e;
        }
    }

    private function countTotalAmountOrder(array $orders): float
    {
        $total = 0.0;

        foreach ($orders as $order) {
            // Garante que o unit_price é numérico antes de somar
            if (isset($order['unit_price']) && is_numeric($order['unit_price'])) {
                $total += floatval($order['unit_price']);
            }
        }

        return $total;
    }

    private function createItensOrders(array $data, OrderModel $order, int $orderId): array
    {
        $itensCriados = [];

        foreach ($data['itens'] as $itemData) {
            $item = new OrderItemModel();
            $item->setProductId($itemData['product_id']);
            $item->setOrderId($order);
            $item->setQuantity($itemData['quantity']);
            $item->setUnitPrice($itemData['unit_price']);

            $itemResponse = $this->orderItemService->createItem($item, $orderId);
            if (!$itemResponse['status']) {
                return $this->buildResponse(false, "Erro ao criar item: {$itemData['product_id']}", null);
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

        return $this->buildResponse(true, 'Itens do Pedido Criados com sucesso!', $itensCriados);
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