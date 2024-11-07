<?php

namespace App\Services;

use App\Repositories\OpenFoodRepository;

class OpenFoodService
{
    protected $repository;

    public function __construct(OpenFoodRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getProductDetails(string $barcode): array
    {
        $product = $this->repository->getProductByBarcode($barcode);

        if (empty($product)) {
            return ['error' => true, 'message' => 'Produto não encontrado.'];
        }

        return ['error' => false, 'data' => $product];
    }

    public function searchProducts(array $filters): array
    {
        $results = $this->repository->searchProducts($filters);

        if ($results->isEmpty()) {
            return ['error' => true, 'message' => 'Nenhum produto encontrado para a pesquisa.'];
        }

        return ['error' => false, 'data' => $results];
    }



    public function getIngredientsUsingOCR(string $id, string $code, string $processImage, string $ocrEngine): array
    {
        // Chama o método do repositório para obter os ingredientes via OCR
        $ingredients = $this->repository->getIngredientsUsingOCR($id, $code, $processImage, $ocrEngine);

        // Retorna estrutura padrão de resposta
        if (empty($ingredients)) {
            return ['error' => true, 'message' => 'Nenhum ingrediente encontrado para o código de barras especificado.'];
        }

        return ['error' => false, 'data' => $ingredients];
    }




    public function getSuggestions(string $tagtype, string $term): array
    {
        $suggestions = $this->repository->getSuggestions($tagtype, $term);

        if (empty($suggestions)) {
            return ['error' => true, 'message' => 'Nenhuma sugestão encontrada para o termo especificado.'];
        }

        return ['error' => false, 'data' => $suggestions];
    }

    public function getNutrients(string $cc, string $lc): array
    {
        $nutrients = $this->repository->getNutrients($cc, $lc);

        if (empty($nutrients)) {
            return ['error' => true, 'message' => 'Nenhum nutriente encontrado para os parâmetros especificados.'];
        }

        return ['error' => false, 'data' => $nutrients];
    }

    public function getAttributeGroups(string $lc): array
    {
        $attributeGroups = $this->repository->getAttributeGroups($lc);

        if (empty($attributeGroups)) {
            return ['error' => true, 'message' => 'Nenhum grupo de atributos encontrado para a linguagem especificada.'];
        }

        return ['error' => false, 'data' => $attributeGroups];
    }

    public function getPreferences(string $lc): array
    {
        $preferences = $this->repository->getPreferences($lc);

        if (empty($preferences)) {
            return ['error' => true, 'message' => 'Nenhuma preferência encontrada para a linguagem especificada.'];
        }

        return ['error' => false, 'data' => $preferences];
    }

    public function addOrEditProduct(array $data): array
    {
        $response = $this->repository->addOrEditProduct($data);

        if (!$response['success']) {
            return ['error' => true, 'message' => $response['message']];
        }

        return ['error' => false, 'data' => $response['data']];
    }

    public function addProductPhoto(array $data): array
    {
        return $this->repository->addProductPhoto($data);
    }

    public function rotateProductPhoto(array $data): array
    {
        return $this->repository->rotateProductPhoto($data);
    }

    public function cropProductPhoto(array $data): array
    {
        return $this->repository->cropProductPhoto($data);
    }
}
