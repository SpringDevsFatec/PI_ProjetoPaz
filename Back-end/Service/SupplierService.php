<?php

namespace App\Backend\Service;

use App\Backend\Model\SupplierModel;
use App\Backend\Repository\SupplierRepository;
use App\Backend\Utils\Responses;
use Exception;

class SupplierService {
    use Responses;

    private $supplierRepository;

    public function __construct(SupplierRepository $supplierRepository) {
        $this->supplierRepository = $supplierRepository;
    }

    public function getSupplierById(int $id): array {
        try {
            $supplier = $this->supplierRepository->find($id);

            if ($supplier) {
                return $this->buildResponse(true, 'Fornecedor encontrado com sucesso.', $supplier);
            } else {
                return $this->buildResponse(false, 'Fornecedor não encontrado.', null);
            }
        } catch (Exception $e) {
            return $this->buildResponse(false, 'Erro interno ao buscar fornecedor: ' . $e->getMessage(), null);
        }
    }

    public function getAllSuppliers(): array {
        try {
            $suppliers = $this->supplierRepository->findAll();

            if (!empty($suppliers)) {
                return $this->buildResponse(true, 'Fornecedores encontrados com sucesso.', $suppliers);
            } else {
                return $this->buildResponse(false, 'Nenhum fornecedor encontrado.', []);
            }
        } catch (Exception $e) {
            return $this->buildResponse(false, 'Erro interno ao buscar todos os fornecedores: ' . $e->getMessage(), null);
        }
    }

    public function createSupplier(SupplierModel $data): array {
        $name = $data->getName();
        $location = $data->getLocation();

        if (!isset($name) || !isset($location)) {
            return $this->buildResponse(false, 'Nome e local são campos obrigatórios para criar um fornecedor.', null);
        }

        try {
            $newSupplierId = $this->supplierRepository->create($data);

            if ($newSupplierId) {
                $newSupplier = $this->supplierRepository->find($newSupplierId);
                return $this->buildResponse(true, 'Fornecedor criado com sucesso.', $newSupplier);
            } else {
                return $this->buildResponse(false, 'Falha ao criar o fornecedor.', null);
            }
        } catch (Exception $e) {
            return $this->buildResponse(false, 'Erro interno ao criar fornecedor: ' . $e->getMessage(), null);
        }
    }

    public function updateSupplier(SupplierModel $data): array {
        $id = $data->getId();

        try {
            $existingSupplier = $this->supplierRepository->find($id);
            if (!$existingSupplier) {
                return $this->buildResponse(false, 'Fornecedor não encontrado para atualização.', null);
            }

            $updated = $this->supplierRepository->update($data);

            if ($updated) {
                return $this->buildResponse(true, 'Fornecedor atualizado com sucesso.', $data);
            } else {
                return $this->buildResponse(false, 'Falha ao atualizar o fornecedor.', null);
            }
        } catch (Exception $e) {
            return $this->buildResponse(false, 'Erro interno ao atualizar fornecedor: ' . $e->getMessage(), null);
        }
    }

    public function deleteSupplier(int $id): array {
        try {
            $existingSupplier = $this->supplierRepository->find($id);
            if (!$existingSupplier) {
                return $this->buildResponse(false, 'Fornecedor não encontrado para exclusão.', null);
            }

            $deleted = $this->supplierRepository->delete($id);

            if ($deleted) {
                return $this->buildResponse(true, 'Fornecedor excluído com sucesso.', null);
            } else {
                return $this->buildResponse(false, 'Falha ao excluir o fornecedor.', null);
            }
        } catch (Exception $e) {
            return $this->buildResponse(false, 'Erro interno ao excluir fornecedor: ' . $e->getMessage(), null);
        }
    }
}
