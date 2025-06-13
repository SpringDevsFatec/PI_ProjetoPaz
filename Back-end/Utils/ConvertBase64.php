<?php
namespace App\Backend\Utils;

use Exception;
use App\Backend\Utils\Responses;

class ConvertBase64 {
    // Use the Responses trait to handle responses
use Responses;
    public static function processBase64($data, $diretorio) {
        $basePath = __DIR__ . '/../../SrcServer/' . $diretorio;

        if (!empty($data)) {
             // Decodifica o base64
            $fileData = base64_decode($data, true);
        
            if ($fileData === false) {
                // If the base64 data is invalid, return an error response
                $response = self::buildResponse(true, "Invalid base64 data", null);
            }
            var_dump($fileData);die;

            $response = self::buildResponse(true, "imagem decodificada", $fileData);
        }else {
            // If no data is provided, return an error response
           $response = self::buildResponse(false, "No data provided for processing.", null);
        }
        
        return $response;
    }

    private static function saveBase64File($base64Data, $uploadDir, $extension) {
        // Decodifica o base64
        $fileData = base64_decode($base64Data, true);
        var_dump($fileData);die;

        if ($fileData === false) {
            throw new Exception("Invalid base64 data");
        }

        // Cria o diretório, se não existir
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            throw new Exception("Failed to create directory: $uploadDir");
        }

        // Gera o nome do arquivo e salva
        $fileName = uniqid() . '.' . $extension;
        $filePath = $uploadDir . $fileName;

        if (file_put_contents($filePath, $fileData) === false) {
            throw new Exception("Failed to save file: $filePath");
        }

        // Retorna o caminho relativo
        return str_replace(__DIR__ . '/../../', '', $filePath);
    }
}
