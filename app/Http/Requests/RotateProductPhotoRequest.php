<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RotateProductPhotoRequest extends FormRequest
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
            'code' => 'required|string',
            'id' => 'required|string',
            'imgid' => 'required|integer',
            'angle' => 'required|integer|in:90,180,270', // Aceitando apenas 90, 180 ou 270
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
            'id.required' => 'O ID da imagem é obrigatório.',
            'imgid.required' => 'O ID da imagem é obrigatório.',
            'angle.required' => 'O ângulo de rotação é obrigatório.',
            'angle.in' => 'O ângulo deve ser 90, 180 ou 270.',
        ];
    }
}
