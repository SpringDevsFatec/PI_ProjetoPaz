<?php

namespace App\Backend\Service;

use App\Backend\Model\SupplierModel;
use App\Backend\Repository\SupplierRepository;
use Exception;

class SupplierService {
    private $supplierRepository;

    public function __construct(SupplierRepository $supplierRepository) {
        $this->supplierRepository = $supplierRepository;
    }

    public function getSupplierById(int $id): array {
        try {
            $supplier = $this->supplierRepository->find($id);

            if ($supplier) {
                return [
                    'status' => true,
                    'message' => 'Fornecedor encontrado com sucesso.',
                    'content' => $supplier
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Fornecedor não encontrado.',
                    'content' => null
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Erro interno ao buscar fornecedor: ' . $e->getMessage(),
                'content' => null
            ];
        }
    }

    public function getAllSuppliers(): array {
        try {
            $suppliers = $this->supplierRepository->findAll();
            if (!empty($suppliers)) {
                return [
                    'status' => true,
                    'message' => 'Fornecedores encontrados com sucesso.',
                    'content' => $suppliers
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Nenhum fornecedor encontrado.',
                    'content' => []
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Erro interno ao buscar todos os fornecedores: ' . $e->getMessage(),
                'content' => null
            ];
        }
    }

    public function createSupplier(SupplierModel $data){
        // Validate required fields
        $name = $data->getName();
        $location = $data->getLocation();

        if (!isset($name) || !isset($location)) {
            return [
                'status' => false,
                'message' => 'Nome e local são campos obrigatórios para criar um fornecedor.',
                'content' => null
            ];
        }

        // create a new supplier object


        try {
            $newSupplierId = $this->supplierRepository->create($data);

            if ($newSupplierId) {
                $newSupplier = $this->supplierRepository->find($newSupplierId);
                return [
                    'status' => true,
                    'message' => 'Fornecedor criado com sucesso.',
                    'content' => $newSupplier
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Falha ao criar o fornecedor.',
                    'content' => null
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Erro interno ao criar fornecedor: ' . $e->getMessage(),
                'content' => null
            ];
        }
    }

    public function updateSupplier(SupplierModel $data): array {
        
        $id = $data->getId();
        try {
            $existingSupplier = $this->supplierRepository->find($id);
            if (!$existingSupplier) {
                return [
                    'status' => false,
                    'message' => 'Fornecedor não encontrado para atualização.',
                    'content' => null
                ];
            }

            $updated = $this->supplierRepository->update($data);

            if ($updated) {
                return [
                    'status' => true,
                    'message' => 'Fornecedor atualizado com sucesso.',
                    'content' => $data
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Falha ao atualizar o fornecedor.',
                    'content' => null
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Erro interno ao atualizar fornecedor: ' . $e->getMessage(),
                'content' => null
            ];
        }
    }

    public function deleteSupplier(int $id): array {
        try {
            $existingSupplier = $this->supplierRepository->find($id);
            if (!$existingSupplier) {
                return [
                    'status' => false,
                    'message' => 'Fornecedor não encontrado para exclusão.',
                    'content' => null
                ];
            }

            $deleted = $this->supplierRepository->delete($id);

            if ($deleted) {
                return [
                    'status' => true,
                    'message' => 'Fornecedor excluído com sucesso.',
                    'content' => null
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Falha ao excluir o fornecedor.',
                    'content' => null
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Erro interno ao excluir fornecedor: ' . $e->getMessage(),
                'content' => null
            ];
        }
    }
}