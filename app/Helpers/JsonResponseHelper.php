<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class JsonResponseHelper
{
    /**
     * Retorna uma resposta JSON de erro.
     *
     * @param string $message Mensagem de erro.
     * @param array $details Detalhes adicionais sobre o erro.
     * @param int $status Código de status HTTP.
     * @return JsonResponse
     */
    public static function jsonErrorResponse(string $message, array $details = [], int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'details' => $details,
        ], $status);
    }

    /**
     * Retorna uma resposta JSON de sucesso.
     *
     * @param array $data Dados da resposta.
     * @param string $message Mensagem de sucesso.
     * @param int $status Código de status HTTP.
     * @return JsonResponse
     */
    public static function jsonSuccessResponse(array $data, string $message = 'Operação realizada com sucesso.', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
}
