<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IngredientRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'required|string|in:ingredients_en', // Deve ser 'ingredients_en'
            'code' => 'required|string', // Código de barras do produto
            'process_image' => 'required|in:1', // Deve ser '1' para processar a imagem
            'ocr_engine' => 'required|string|in:google_cloud_vision', // Motor de OCR, exemplo 'google_cloud_vision'
            'page_size' => 'nullable|integer|min:1|max:100', // Tamanho da página, opcional
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'O campo id é obrigatório.',
            'id.in' => 'O campo id deve ser "ingredients_en".',
            'code.required' => 'O campo código de barras (code) é obrigatório.',
            'process_image.required' => 'O campo process_image é obrigatório.',
            'process_image.in' => 'O campo process_image deve ser "1".',
            'ocr_engine.required' => 'O campo ocr_engine é obrigatório.',
            'ocr_engine.in' => 'O campo ocr_engine deve ser "google_cloud_vision".',
            'page_size.integer' => 'O campo page_size deve ser um número inteiro.',
            'page_size.min' => 'O valor mínimo para page_size é 1.',
            'page_size.max' => 'O valor máximo para page_size é 100.',
        ];
    }
}
