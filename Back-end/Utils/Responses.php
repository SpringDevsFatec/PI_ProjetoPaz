<?php
namespace App\Backend\Utils;

trait Responses {
    
    public function handleResponse($result, $successMessage = "Operação concluída com sucesso.", $content = null, $http_response_code = 200) {
        http_response_code($http_response_code);

        echo json_encode([
            "status" => $result,
            "message" => $successMessage,
            "content" => $content
        ]);
    }

    

}
