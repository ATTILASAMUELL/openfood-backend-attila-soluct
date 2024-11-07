<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductBarcodeRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Permite que qualquer usuário autorizado faça esta requisição
    }

    public function rules()
    {
        return [
            'barcode' => 'required|string|min:8|max:13', // Valida o código de barras como string de tamanho razoável
        ];
    }
}
