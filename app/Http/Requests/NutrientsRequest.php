<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NutrientsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'cc' => 'required|string|size:2',
            'lc' => 'required|string|size:2',
        ];
    }
}
