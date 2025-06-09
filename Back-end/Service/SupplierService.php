<?php

namespace App\Backend\Service;

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

    public function createSupplier(object $data): array {
        if (!isset($data->name) || !isset($data->place)) {
            return [
                'status' => false,
                'message' => 'Nome e local são campos obrigatórios para criar um fornecedor.',
                'content' => null
            ];
        }

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

    public function updateSupplier(object $data): array {
        if (!isset($data->id)) {
            return [
                'status' => false,
                'message' => 'ID do fornecedor é obrigatório para atualização.',
                'content' => null
            ];
        }

        try {
            $existingSupplier = $this->supplierRepository->find($data->id);
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
                    'content' => null
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