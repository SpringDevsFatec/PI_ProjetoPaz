<?php
namespace App\Backend\Utils;

use Exception;

class ConvertBase64 {

    public static function processBase64($data, $diretorio) {
        $basePath = __DIR__ . '/../../SrcServer/' . $diretorio;

        if (isset($data->imageSrcBase64)) {
            $data->imgurl = self::saveBase64File($data->imageSrcBase64, $basePath . '/Imgs/', 'jpg');
        }

        if (isset($data->pdfBase64)) {
            $data->pdfurl = self::saveBase64File($data->pdfBase64, $basePath . '/PDF/', 'pdf');
        }

        return $data;
    }

    private static function saveBase64File($base64Data, $uploadDir, $extension) {
        // Decodifica o base64
        $fileData = base64_decode($base64Data, true);
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
