<?php
namespace App\Backend\Utils;

class Responses
{ 
    public static function send($status, $message = "Operação concluída.", $content = null, $httpCode = 200)
    {
        http_response_code($httpCode);
        echo json_encode([
            "status" => $status,
            "message" => $message,
            "content" => $content
        ]);
    }

    public static function success($message = "Sucesso!", $content = null, $httpCode = 200)
    {
        self::send(true, $message, $content, $httpCode);
    }

    public static function error($message = "Erro na operação.", $content = null, $httpCode = 400)
    {
        self::send(false, $message, $content, $httpCode);
    }
}
