<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IngredientOCRResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Suporte para arrays e objetos
        return [
            'ingredients_text_from_image_orig' => $this['ingredients_text_from_image_orig'] ?? null,
            'ingredients_text_from_image' => $this['ingredients_text_from_image'] ?? '',
            'status' => $this['status'] ?? 0,
        ];
    }
}
