<?php
namespace App\Backend\Utils;

trait Responses {

    /**
     * Sends the HTTP response (used in Controllers).
     *
     * @param bool $status The operation status.
     * @param string $message The message to return.
     * @param mixed $content The content to return.
     * @param int $http_response_code The HTTP status code.
     */


    public function handleResponse( $status,  $message , $content , $http_response_code ){
        http_response_code($http_response_code);

        echo json_encode([
            "status" => $status,
            "message" => $message,
            "content" => $content
        ]);
    }

       /**
     * Handles the response for the API.(used in Services)
     *
     * @param bool $status The operation status.
     * @param string $message The message to return.
     * @param mixed $content The content to return.
     * @return array
     */

    public static function buildResponse( $status,  $message, $content) {
        return [
            "status" => $status,
            "message" => $message,
            "content" => $content
        ];
    }

    /**
     * Handles the response for the API.(used in Repositories)
     *
     * @param bool $status The operation status.
     * @param mixed $content The content to return.
     * @return array
     */
    public function buildRepositoryResponse( $status, $content) {
        return [
            "status" => $status,
            "content" => $content
        ];
    }
}