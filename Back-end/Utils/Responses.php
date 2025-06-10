<?php
namespace App\Backend\Utils;

trait Responses {

    /**
     * Handles the response for the API.
     *
     * @param array $result The result of the operation.
     * @param string $message The message to return.
     * @param mixed $content The content to return.
     * @param int $statusCode The HTTP status code.
     */
    
    public function handleResponse($result, $successMessage = "Operação concluída com sucesso.", $content = null, $http_response_code = 200) {
        http_response_code($http_response_code);

        echo json_encode([
            "status" => $result,
            "message" => $successMessage,
            "content" => $content
        ]);
    }

    

}
