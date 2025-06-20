<?php
namespace App\Backend\Service;

use App\Backend\Model\SaleModel;
use App\Backend\Model\OrderModel;
use App\Backend\Repository\SaleRepository;
use App\Backend\Repository\OrderRepository;
use App\Backend\Repository\UserRepository;
use App\Backend\Utils\Responses;
use App\Backend\Utils\PatternText;
use App\Backend\Libs\AuthMiddleware;
use App\Backend\Utils\CreateCodes;
use App\Backend\Utils\ImageUploader;
use Exception;
use DomainException;
use DateTimeInterface;
use DateTime;
use GuzzleHttp\Promise\Create;
use InvalidArgumentException;

class SaleService {
    
    use Responses;

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


    public function getSalesByPeriod(DateTimeInterface $startDate, DateTimeInterface $endDate, ?int $sellerId = null): array 
    {
        if ($startDate > $endDate) {
            throw new InvalidArgumentException("Data final deve ser maior ou igual à data inicial");
        }
        try {
            $this->orderRepository->beginTransaction();
            $response = $this->saleRepository->findByDateRange($startDate, $endDate, $sellerId);
            $this->saleRepository->commitTransaction();
            
            if ($response['status'] == true) {
                $content = array_map([$this, 'ResolveSale'], $response['content']);
                return $this->buildResponse(true, 'Conteúdo encontrado.', $content);
            }

            return $this->buildResponse(false, 'Nenhum conteúdo encontrado.', null);

        } catch (Exception $e) {
            $this->saleRepository->rollBackTransaction();
            throw $e;
        }
}

    public function getSalesBySeller(int $sellerId, ?string $status = null): array 
    {
        try {
            $this->orderRepository->beginTransaction();
            $response = $this->saleRepository->findBySeller($sellerId, $status);
            $this->saleRepository->commitTransaction();
            
            if ($response['status'] == true) {
                $content = array_map([$this, 'ResolveSale'], $response['content']);
                return $this->buildResponse(true, 'Conteúdo encontrado.', $content);
            }

            return $this->buildResponse(false, 'Nenhum conteúdo encontrado.', null);

        } catch (Exception $e) {
            $this->saleRepository->rollBackTransaction();
            throw $e;
        }
    }


    public function getByStatus(string $status): array 
    {
        try {
            $this->orderRepository->beginTransaction();
            $response = $this->saleRepository->findByStatus($status);
            $this->saleRepository->commitTransaction();
            
            if ($response['status'] == true) {
                $content = array_map([$this, 'ResolveSale'], $response['content']);
                return $this->buildResponse(true, 'Conteúdo encontrado.', $content);
            }


            return $this->buildResponse(false, 'Nenhum conteúdo encontrado.', null);

        } catch (Exception $e) {
            $this->saleRepository->rollBackTransaction();
            throw $e;
        }
    }

    public function getAll(): array 
    {
        try {
            $this->orderRepository->beginTransaction();
            $response = $this->saleRepository->findAll();
            $this->saleRepository->commitTransaction();
            
            if ($response['status'] == true) {
                $content = array_map([$this, 'ResolveSale'], $response['content']);
                return $this->buildResponse(true, 'Conteúdo encontrado.', $content);
            }


            return $this->buildResponse(false, 'Nenhum conteúdo encontrado.', null);

        } catch (Exception $e) {
            $this->saleRepository->rollBackTransaction();
            throw $e;
        }
    }

    public function getSale(int $id): ?array 
    {
        try {
            $this->orderRepository->beginTransaction();
            $response = $this->saleRepository->find($id);
            $this->saleRepository->commitTransaction();
            
            if ($response['status'] == true) {
                $content = $this->ResolveSale($response['content']);
                return $this->buildResponse(true, 'Conteúdo encontrado.', $content);
            }

            return $this->buildResponse(false, 'Nenhum conteúdo encontrado.', null);

        } catch (Exception $e) {
            $this->saleRepository->rollBackTransaction();
            throw $e;
        }
    }

    public function createSale($data): array
    {
        $dataPadronizado = PatternText::processText($data);

        $decodedToken = (new AuthMiddleware())->openToken();
        $userId = $decodedToken->id;

        $userExists = $this->userRepository->getContentId($userId);
        if ($userExists['status'] === false) {
            return $this->buildResponse(false, 'User não encontrado.', $userExists['content']);
        }
        
        if (!$this->saleRepository->findOpenBySeller($userId)) 
        {
            return $this->buildResponse(false, 'Já existe uma venda aberta para este vendedor', $userExists['content']);
        }

        $user = $userExists['content'];

        //create code for sale
        $code = CreateCodes::createCodes('SA');

        $sale = new SaleModel();
        $sale->setUserId($userId);
        $sale->setCode($code['content']);
        $sale->setMethod($dataPadronizado['method']);
        $sale->setStatus('pending');
        $sale->setTotalAmountSale(0.00);

        try {
        $this->saleRepository->beginTransaction();

        $response = $this->saleRepository->createSale($sale);    
        if ($response['status'] === true) {
            $sale->setId($response['content']);
            $this->saleRepository->commitTransaction();
            return $this->buildResponse(true, 'Venda criada com sucesso.', [
                'id' => $sale->getId(),
                'code' => $sale->getCode(),
                'method' => $sale->getMethod(),
                'imgSale' => $sale->getImgSale(),
                'status' => $sale->getStatus(),
                'totalAmountSale' => $sale->getTotalAmountSale(),
                'createdAt' => $sale->getCreatedAt(),
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email']
                ]
            ]
        
        
        );
        }    

        return $this->buildResponse(false, $response['content'], null);
        } catch (Exception $e) {
            $this->saleRepository->rollBackTransaction();
            throw $e;
        }
    }

    public function completeSale(int $saleId, array $data ): array
    {
        // verify if SaleExists
        $saleData = $this->saleRepository->find($saleId);
        if ($saleData == false) {
        return $this->buildResponse(false, 'Sale não encontrada', null);
        }
        $sale = $saleData['content'];
        
        // Genarate link Image
        $reponseImg = ImageUploader::base64ToS3Url($data, 'Sale');
        if ($reponseImg['status'] === false) {
            return $this->buildResponse(false, 'Erro ao processar imagem: ' . $reponseImg['message'], null);
        }

        //get total Amount 
        try {
          $ordersbyId = $this->orderRepository->findBySaleId($saleId);
          $totalsaleamount = $this->CalculateTotalAmountbySale($ordersbyId);
        } catch (\Throwable $th) {
            return $this->buildResponse(false, 'Erro na busca de Orders '. $th, null);
        }

        $saleupdate = new SaleModel();
        $saleupdate->setId($saleId);
        $saleupdate->setImgSale($reponseImg['content']);
        $saleupdate->setStatus('completed');
        $saleupdate->setTotalAmountSale($totalsaleamount);
        
        try {
            
            $response = $this->saleRepository->completeSale($saleupdate);

            if ($response['status'] == true) {
               return $this->buildResponse(true,'Venda Concluida com Sucesso!', [    
                'id' => $response['content']->getId(), 
                'code' => $sale['code'],
                'method' => $sale['method'],
                'imgSale' => $response['content']->getImgSale(),
                'status' => $response['content']->getStatus(),
                'totalAmountSale' => $response['content']->getTotalAmountSale(),
                'created_at' => $sale['created_at'],
                'updated_at' => $sale['updated_at'],
                'user' => [
                    'id' => $sale['user_id'],
                    'name' => $sale['user_name'],
                    'email' => $sale['user_email']
                ]
            ]
            );
            
        }

            return $this->buildResponse(false, $response['content'], null);
        } catch (DomainException $e) {
            throw new DomainException("Não foi possível concluir a venda: " . $e->getMessage());
        }
    }

    public function cancelSale(int $saleId) : array 
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

    private function ResolveSale(array $sale): array
{
    return [
        'id' => (int) $sale['id'],
        'code' => $sale['code'],
        'method' => $sale['method'] ?? null,
        'status' => $sale['status'],
        'total_amount_sale' => $sale['total_amount_sale'],
        'created_at' => $sale['created_at'],
        'user' => [
            'id' => (int) $sale['user_id'],
            'name' => $sale['user_name'],
            'email' => $sale['user_email'],
            'created_at' => $sale['user_created_at'],
        ]
    ];
}

    private function CalculateTotalAmountbySale(array $resultadoRepositoryOrder): float
    {
        // Verifica se o status é true e se existe o índice 'content'
        if (!isset($resultadoRepositoryOrder['status']) || $resultadoRepositoryOrder['status'] !== true) {
            return 0.0;
        }

        if (!isset($resultadoRepositoryOrder['content']) || !is_array($resultadoRepositoryOrder['content'])) {
            return 0.0;
        }

        $somaTotal = 0.0;

        foreach ($resultadoRepositoryOrder['content'] as $pedido) {
            // Verifica se existe a chave 'total_amount_order'
            if (isset($pedido['total_amount_order'])) {
                $somaTotal += (float) $pedido['total_amount_order'];
            }
        }

        return $somaTotal;
    }




    
}