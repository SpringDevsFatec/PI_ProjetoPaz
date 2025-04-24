<?php

namespace App\Backend\Libs;

use App\Backend\Config\Token;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AuthMiddleware {
    public function openToken() {
        var_dump("chegou");
        $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (!$authorizationHeader) {
            http_response_code(401);
            echo json_encode(['status' => false, 'message' => 'Token de autorização ausente']);
            exit;
        }

        if (preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
            $token = $matches[1];
        } else {
            http_response_code(401);
            echo json_encode(['status' => false, 'message' => 'Formato do token inválido']);
            exit;
        }
var_dump($token);
        try {
            $decoded = JWT::decode($token, new Key(Token::SECRET_KEY, 'HS256'));
            return $decoded;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['status' => false, 'message' => 'Token inválido ou expirado']);
            exit;
        }
    }
}