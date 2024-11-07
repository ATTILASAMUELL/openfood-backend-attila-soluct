<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddProductPhotoRequest extends FormRequest
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
            'imagefield' => 'required|string|in:front_en,ingredients_en,nutrition_en,packaging_en,other_en',
            'image' => 'required|file|mimes:jpeg,jpg,png,gif,heic|max:2048',
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
            'imagefield.required' => 'O campo de tipo de imagem é obrigatório.',
            'imagefield.in' => 'O campo de tipo de imagem deve ser um dos seguintes valores: front_en, ingredients_en, nutrition_en, packaging_en, other_en.',
            'image.required' => 'A imagem é obrigatória.',
            'image.file' => 'A imagem deve ser um arquivo.',
            'image.mimes' => 'A imagem deve ser um arquivo do tipo: jpeg, jpg, png, gif, heic.',
            'image.max' => 'A imagem não pode ser maior que 2 MB.',
        ];
    }
}
