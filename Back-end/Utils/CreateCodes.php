<?php

namespace App\Backend\Utils;

use App\Backend\Repository\SaleRepository;
use App\Backend\Repository\OrderRepository;
use App\Backend\Utils\Responses;

class CreateCodes
{
    use Responses;

    public static function createCodes(string $sufixo): array
    {
        var_dump("ta chegando :" . $sufixo);
        // validate sufixo
        if (!in_array($sufixo, ['SA', 'OR'])) {
            return self::buildResponse(false, 'Sufixo inválido. Use SA ou OR.', null);
        }

        $tentativas = 0;
        $limiteTentativas = 10;

        while ($tentativas < $limiteTentativas) {
            $code = self::generateCode($sufixo);

            if (!self::codeExists($code, $sufixo)) {
                return self::buildResponse(true, 'Code criado com sucesso.', $code);
            }

            $tentativas++;
        }

        return self::buildResponse(false, 'Problemas ao criar o code. Tentativas excedidas.', null);
    }

    private static function generateCode(string $sufixo): string
    {
        $random = str_pad((string)random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        return  $sufixo . $random ;
    }

    private static function codeExists(string $code, string $sufixo): bool
    {
        if ($sufixo === 'SA') {
            $repository = new SaleRepository();
            $result = $repository->findByCode($code);
        } elseif ($sufixo === 'OR') {
            $repository = new OrderRepository();
            $result = $repository->findByCode($code);
        } else {
            return true; // segurança: se sufixo inválido, força existir
        }

        return isset($result['status']) && $result['status'] === true;
    }
}
