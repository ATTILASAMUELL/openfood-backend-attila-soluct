<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddOrEditProductRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Regras de validação para a requisição.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'required|numeric',
            'user_id' => 'nullable|string',
            'password' => 'nullable|string',
            'app_name' => 'nullable|string',
            'brands' => 'nullable|array',
            'labels' => 'nullable|array',
            'categories' => 'nullable|array',
            'packaging' => 'nullable|string',
            'comment' => 'nullable|string',
            'app_version' => 'nullable|string',
            'app_uuid' => 'nullable|string',
        ];
    }

    /**
     * Mensagens de erro personalizadas para as validações.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'code.required' => 'O código do produto é obrigatório.',
            'app_name.required' => 'O nome do aplicativo é obrigatório.',
        ];
    }
}
