<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this['id'] ?? null,
            'barcode' => $this['barcode'] ?? null,
            'name' => $this['name'] ?? 'N/A',
            'categories' => $this['categories'] ?? [],
            'brands' => $this['brands'] ?? 'N/A',
            'ingredients_text' => $this['ingredients_text'] ?? 'N/A',
            'nutriments' => $this['nutriments'] ?? [],
            'image_url' => $this['image_url'] ?? null,
            'quantity' => $this['quantity'] ?? 'N/A',
        ];
    }
}
