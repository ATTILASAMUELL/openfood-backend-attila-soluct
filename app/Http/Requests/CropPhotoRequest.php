<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CropPhotoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'code' => 'required|string',
            'imgid' => 'required|integer',
            'id' => 'required|string',
            'x1' => 'nullable|integer',
            'y1' => 'nullable|integer',
            'x2' => 'nullable|integer',
            'y2' => 'nullable|integer',
            'angle' => 'nullable|integer',
            'normalize' => 'nullable|boolean',
            'white_magic' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'code.required' => 'O código do produto é obrigatório.',
            'imgid.required' => 'O ID da imagem é obrigatório.',
            'id.required' => 'O identificador da imagem é obrigatório.',
            'x1.required' => 'A coordenada X inicial é obrigatória.',
            'y1.required' => 'A coordenada Y inicial é obrigatória.',
            'x2.required' => 'A coordenada X final é obrigatória.',
            'y2.required' => 'A coordenada Y final é obrigatória.',
        ];
    }
}
