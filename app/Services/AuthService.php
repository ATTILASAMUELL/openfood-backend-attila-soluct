<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\OpenFoodRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AuthService
{
    protected $openFoodRepository;

    public function __construct(OpenFoodRepository $openFoodRepository)
    {
        $this->openFoodRepository = $openFoodRepository;
    }

    /**
     * Realiza o registro de um novo usuário e retorna os dados do usuário junto com o token de autenticação.
     *
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $expiresAt = Carbon::now()->addWeeks(1);

        return [
            'message' => 'Usuário criado com sucesso',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt->toDateTimeString(),
        ];
    }

    /**
     * Realiza o login do usuário e retorna o token e a sessão da OpenFood API.
     *
     * @param array $credentials
     * @return array
     */
    public function login(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            return ['error' => 'Credenciais inválidas para o sistema local.'];
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'message' => 'Login realizado com sucesso nos dois sistemas.',
        ];
    }

    /**
     * Retorna o perfil do usuário autenticado.
     *
     * @return User
     */
    public function profile(): User
    {
        return Auth::user();
    }

    /**
     * Efetua logout e revoga o token atual.
     */
    public function logout(): void
    {
        Auth::user()->currentAccessToken()->delete();
    }

    /**
     * Renova o token de acesso.
     *
     * @return array
     */
    public function refresh(): array
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();
        $newToken = $user->createToken('auth_token')->plainTextToken;

        return [
            'access_token' => $newToken,
            'token_type' => 'Bearer',
        ];
    }
}
