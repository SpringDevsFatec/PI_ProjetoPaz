<?php
namespace App\Backend\Utils;

use Exception;

class PatternText {
    public static function processText($data) {
        if (isset($data->name)) {
            $data->name = ucfirst($data->name);
        }
        if (isset($data->email)) {
            $data->email = mb_strtolower($data->email);
        }
        if (isset($data->location)) {
            $data->location = ucfirst($data->location);
        }
        if (isset($data->nameproduct)) {
            $data->nameproduct = ucfirst($data->nameproduct);
        }
        if (isset($data->nameSupplier)) {
            $data->nameSupplier = ucfirst($data->nameSupplier);
        }
        if (isset($data->description)) {
            $data->description = ucfirst($data->description);
        }
        // ... another Pattners for other attributes
        return $data;
    }

    public static function cryptPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function validateRequiredFields($dataPadronizado) {
        if (empty($dataPadronizado->nome) || empty($dataPadronizado->email) || empty($dataPadronizado->password)) {
            return [
                'status' => false,
                'message' => 'Dados incompletos para atualização.',
                'content' => null
            ];
        }
        return [
            'status' => true,
            'message' => 'Dados completos.',
            'content' => $dataPadronizado
        ];
    }

    // Handles the response for the API
    public static function handleResponse($result, $successMessage = "Operação concluída com sucesso.", $content = null, $http_response_header = null) {
        if (!empty($result)) {
            http_response_code($http_response_header);
            echo json_encode(["status" => $result,"message" => $successMessage,"content" => $content]);
        } else {
            http_response_code($http_response_header);
            echo json_encode(['status' => false, "message" => $successMessage, "content" => $content]);
        }
    }

    public static function validateProductData(array $data): void
    {
        $requiredFields = ['nameproduct', 'cost_price', 'sale_price', 'category', 'donation', 'is_favorite','namesupplier','location' ];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
               self::handleResponse(false, "Campo obrigatório faltando: {$field}", null, 400);
               die;
            }
        }

        if ($data['cost_price'] < 0) {
             self::handleResponse(false, "Preço de custo não pode ser negativo", null, 400);
               die;
        }

        if ($data['sale_price'] <= 0) {
            self::handleResponse(false, "Preço de venda deve ser maior que zero", null, 400);
               die;
        }

        if ($data['donation'] <> 1 && $data['sale_price'] < $data['cost_price']) {
           self::handleResponse(false, "Preço de venda não pode ser menor que o custo", null, 400);
               die;
        }
    }

// ...existing code...
}

