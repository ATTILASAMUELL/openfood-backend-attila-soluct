<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponseHelper;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Exception;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Registra um novo usuário.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->register($request->all());

            return JsonResponseHelper::jsonSuccessResponse([
                'user' => new UserResource($result['user']),
                'access_token' => $result['access_token'],
                'token_type' => $result['token_type'],
                'expires_at' => $result['expires_at'],
            ], $result['message'], 201);
        } catch (ValidationException $e) {
            return JsonResponseHelper::jsonErrorResponse('Erro de validação.', $e->errors(), 422);
        }
    }

    /**
     * Realiza o login de um usuário.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->only('email', 'password');
            $result = $this->authService->login($credentials);

            if (isset($result['error'])) {
                return JsonResponseHelper::jsonErrorResponse($result['error'], [], 401);
            }

            return JsonResponseHelper::jsonSuccessResponse($result, 'Login realizado com sucesso.');
        } catch (Exception $e) {
            Log::error("Erro no login: " . $e->getMessage());
            return JsonResponseHelper::jsonErrorResponse('Ocorreu um erro durante o login. Tente novamente mais tarde.', [], 500);
        }
    }

    /**
     * Realiza o logout de um usuário.
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            $this->authService->logout();
            return JsonResponseHelper::jsonSuccessResponse([], 'Logout realizado com sucesso.');
        } catch (Exception $e) {
            Log::error("Erro no logout: " . $e->getMessage());
            return JsonResponseHelper::jsonErrorResponse('Ocorreu um erro durante o logout. Tente novamente mais tarde.', [], 500);
        }
    }

    /**
     * Retorna o perfil do usuário logado.
     *
     * @return JsonResponse
     */
    public function profile(): JsonResponse
    {
        try {
            $user = $this->authService->profile();
            return JsonResponseHelper::jsonSuccessResponse((new UserResource($user))->toArray(request()), 'Perfil carregado com sucesso.');
        } catch (Exception $e) {
            Log::error("Erro ao carregar perfil: " . $e->getMessage());
            return JsonResponseHelper::jsonErrorResponse('Ocorreu um erro ao carregar o perfil. Tente novamente mais tarde.', [], 500);
        }
    }

    /**
     * Atualiza o token de autenticação.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        try {
            $newToken = $this->authService->refresh();
            return JsonResponseHelper::jsonSuccessResponse($newToken, 'Token atualizado com sucesso.');
        } catch (Exception $e) {
            Log::error("Erro ao atualizar token: " . $e->getMessage());
            return JsonResponseHelper::jsonErrorResponse('Ocorreu um erro ao atualizar o token. Tente novamente mais tarde.', [], 500);
        }
    }
}
