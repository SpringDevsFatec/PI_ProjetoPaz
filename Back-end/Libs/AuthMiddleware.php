<?php

namespace App\Backend\Libs;

use App\Backend\Config\Token;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AuthMiddleware {

    private Token $tokenConfig;

    public function __construct()
    {
        $this->tokenConfig = new Token(); // Carrega .env e configurações
    }

    // Gera um novo token JWT
    public function createToken(array $payload): string {
        $issuedAt = time();

        //confirma regra de token dev ilimitado
        if ($payload['name'] == "Dev") {
            $expire = $issuedAt + 3600 * 24 * 30; // 30 dias de validade
        } else {
            $expire = $issuedAt + 3600; // 1 hora de validade (ajuste conforme necessário)
        }

        $payload['iat'] = $issuedAt;
        $payload['exp'] = $expire;

        return JWT::encode($payload, $this->tokenConfig->SECRET_KEY, 'HS256');
    }

    // Valida um token diretamente
    public function validateToken(string $token) {
        try {   
            $decoded = JWT::decode($token, new Key($this->tokenConfig->SECRET_KEY, 'HS256'));
            return $decoded;
        } catch (Exception $e) {
            return null;
        }
    }

    // Valida token vindo do header Authorization
    public function openToken() {
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

        $decoded = $this->validateToken($token);

        if (!$decoded) {
            http_response_code(401);
            echo json_encode(['status' => false, 'message' => 'Token inválido ou expirado']);
            exit;
        }
        return $decoded;
    }

    public function ValidaToken() {
        return $this->openToken();
    }
}
