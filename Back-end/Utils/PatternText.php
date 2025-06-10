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

// ...existing code...
}

