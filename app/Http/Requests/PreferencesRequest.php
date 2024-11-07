<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PreferencesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'lc' => 'required|string|size:2',
        ];
    }
}
