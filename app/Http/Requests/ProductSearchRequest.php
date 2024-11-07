<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductSearchRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'query' => 'nullable|string',
            'additives_tags' => 'nullable|string',
            'allergens_tags' => 'nullable|string',
            'brands_tags' => 'nullable|string',
            'categories_tags' => 'nullable|string',
            'countries_tags_en' => 'nullable|string',
            'emb_codes_tags' => 'nullable|string',
            'labels_tags' => 'nullable|string',
            'manufacturing_places_tags' => 'nullable|string',
            'nutrition_grades_tags' => 'nullable|string',
            'origins_tags' => 'nullable|string',
            'packaging_tags_de' => 'nullable|string',
            'purchase_places_tags' => 'nullable|string',
            'states_tags' => 'nullable|string',
            'stores_tags' => 'nullable|string',
            'traces_tags' => 'nullable|string',
            'fields' => 'nullable|string',
            'sort_by' => 'nullable|in:product_name,last_modified_t,scans_n,unique_scans_n,created_t,completeness,popularity_key,nutriscore_score,nova_score,nothing,ecoscore_score',
            'page' => 'nullable|integer|min:1',
            'page_size' => 'nullable|integer|min:1|max:100',
            // Parâmetros dinâmicos de nutrientes
            '*.tags_*' => 'nullable|string', // Aceita qualquer parâmetro com _tags_
            '*.lt_*' => 'nullable|numeric', // Para nutrientes com "<"
            '*.gt_*' => 'nullable|numeric', // Para nutrientes com ">"
            '*.eq_*' => 'nullable|numeric', // Para nutrientes com "="
        ];
    }
}
