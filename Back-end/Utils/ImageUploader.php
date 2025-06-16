<?php
// src/Backend/Utils/ImageUploader.php
namespace App\Backend\Utils;

use App\Backend\Config\AWSAuth;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use App\Backend\Utils\Responses;
use Exception;

class ImageUploader
{
    use Responses;

    public function __construct()
    {
        AWSAuth::loadFromEnv();
    }

    public static function base64ToS3Url(array $data): array
    {
        AWSAuth::loadFromEnv();

        if (empty($data['image'])) {
            return Responses::buildResponse(false, 'Chave "image" não enviada.', null);
        }

        $base64 = $data['image'];

        if (preg_match('/^data:(image\/\w+);base64,/', $base64, $matches)) {
            $mimeType = $matches[1];
            $base64 = substr($base64, strpos($base64, ',') + 1);
        } else {
            return Responses::buildResponse(false, 'String Base64 inválida ou sem prefixo de MIME.', null);
        }

        $fileData = base64_decode($base64, true);
        if ($fileData === false) {
            return Responses::buildResponse(false, 'Decodificação Base64 falhou.', null);
        }

        $ext = explode('/', $mimeType)[1] ?? 'jpeg';
        $fileName = uniqid('img_', true) . ".{$ext}";
        $filePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileName;

        if (file_put_contents($filePath, $fileData) === false) {
            return Responses::buildResponse(false, 'Falha ao escrever arquivo temporário.', null);
        }

        $url = self::uploadToS3($filePath, $fileName, $mimeType);

        @unlink($filePath);

        return self::buildResponse(true, 'Imagem salva com sucesso.', $url['content']);
    }

    private static function uploadToS3(string $filePath, string $fileName, string $mimeType): array
    {
        $s3 = new S3Client([
            'version'     => AWSAuth::$VERSION,
            'region'      => AWSAuth::$REGION,
            'credentials' => [
                'key'    => AWSAuth::$ACCESS_KEY,
                'secret' => AWSAuth::$SECRET_KEY,
            ],
            'http' => ['verify' => false]
        ]);

        try {
            $result = $s3->putObject([
                'Bucket'      => AWSAuth::$BUCKET_PRODUCT,
                'Key'         => $fileName,
                'SourceFile'  => $filePath,
                'ACL'         => 'public-read',
                'ContentType' => $mimeType,
            ]);

            return self::buildResponse(true, 'Produto Upado com sucesso', $result['ObjectURL']);
        } catch (AwsException $e) {
            throw new Exception('Erro no upload para S3: ' . $e->getAwsErrorMessage());
        }
    }
}
