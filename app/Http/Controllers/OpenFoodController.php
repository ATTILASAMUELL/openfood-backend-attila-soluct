<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductBarcodeRequest;
use App\Http\Requests\ProductSearchRequest;
use App\Http\Requests\CategoryRequest;
use App\Http\Requests\IngredientRequest;
use App\Http\Requests\CountryRequest;
use App\Http\Requests\OcrRequest;
use App\Http\Requests\SuggestionsRequest;
use App\Http\Requests\NutrientsRequest;
use App\Http\Requests\AttributeGroupsRequest;
use App\Http\Requests\PreferencesRequest;
use App\Http\Requests\AddOrEditProductRequest;
use App\Http\Requests\AddProductPhotoRequest;
use App\Http\Requests\RotateProductPhotoRequest;
use App\Http\Requests\CropPhotoRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\IngredientOCRResource;
use App\Services\OpenFoodService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Helpers\JsonResponseHelper;

class OpenFoodController extends Controller
{
    protected $openFoodService;

    public function __construct(OpenFoodService $openFoodService)
    {
        $this->openFoodService = $openFoodService;
    }

    /**
     * Adiciona ou edita um produto.
     *
     * @param AddOrEditProductRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function addOrEditProduct(AddOrEditProductRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $response = $this->openFoodService->addOrEditProduct($data);

            if (!$response['error']) {
                return JsonResponseHelper::jsonSuccessResponse($response['data'], 'Produto adicionado/editado com sucesso.');
            }

            return JsonResponseHelper::jsonErrorResponse($response['message'], [], 400);
        } catch (Exception $e) {
            Log::error("Erro ao adicionar/editar produto: " . $e->getMessage());
            return JsonResponseHelper::jsonErrorResponse('Erro ao adicionar/editar o produto. Tente novamente mais tarde.', [], 500);
        }
    }

    /**
     * Adiciona uma foto a um produto existente.
     *
     * @param AddProductPhotoRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function addProductPhoto(AddProductPhotoRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $response = $this->openFoodService->addProductPhoto($data);

            return JsonResponseHelper::jsonSuccessResponse($response, 'Foto adicionada com sucesso.');
        } catch (Exception $e) {
            Log::error("Erro ao adicionar foto ao produto: " . $e->getMessage());
            return JsonResponseHelper::jsonErrorResponse('Erro ao adicionar foto ao produto.', [], 500);
        }
    }

    /**
     * Obtém detalhes de um produto pelo código de barras.
     *
     * @param string $barcode
     * @return JsonResponse
     * @throws Exception
     */
    public function getProductByBarcode($barcode): JsonResponse
    {
        try {
            $product = $this->openFoodService->getProductDetails($barcode);

            if ($product['error']) {
                return JsonResponseHelper::jsonErrorResponse($product['message'], [], 404);
            }

            return JsonResponseHelper::jsonSuccessResponse((new ProductResource($product['data']))->toArray(request()));
        } catch (Exception $e) {
            Log::error("Erro ao obter produto: " . $e->getMessage());
            return JsonResponseHelper::jsonErrorResponse('Erro ao buscar o produto. Tente novamente mais tarde.', [], 500);
        }
    }

    /**
     * Pesquisa produtos com base em filtros.
     *
     * @param ProductSearchRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function searchProducts(ProductSearchRequest $request): JsonResponse
    {
        try {
            $filters = $request->all();

            $results = $this->openFoodService->searchProducts($filters);

            if ($results['error']) {
                return JsonResponseHelper::jsonErrorResponse($results['message'], [], 404);
            }

            return JsonResponseHelper::jsonSuccessResponse($results['data']);
        } catch (Exception $e) {
            Log::error("Erro ao pesquisar produtos: " . $e->getMessage());
            return JsonResponseHelper::jsonErrorResponse('Erro ao realizar a pesquisa. Tente novamente mais tarde.', [], 500);
        }
    }

    /**
     * Obtém os ingredientes de um produto usando OCR.
     *
     * @param IngredientRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function getProductIngredientsOCR(IngredientRequest $request): JsonResponse
    {
        try {
            $id = $request->input('id');
            $code = $request->input('code');
            $processImage = $request->input('process_image');
            $ocrEngine = $request->input('ocr_engine');

            $response = $this->openFoodService->getIngredientsUsingOCR($id, $code, $processImage, $ocrEngine);

            if ($response['error']) {
                return JsonResponseHelper::jsonErrorResponse($response['message'], [], 404);
            }

            return JsonResponseHelper::jsonSuccessResponse((new IngredientOCRResource($response['data']))->toArray(request()));
        } catch (\Exception $e) {
            Log::error("Erro ao obter ingredientes via OCR: " . $e->getMessage());
            return JsonResponseHelper::jsonErrorResponse('Erro ao obter ingredientes via OCR. Tente novamente mais tarde.', [], 500);
        }
    }

    /**
     * Obtém sugestões de produtos com base em um termo.
     *
     * @param SuggestionsRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function getSuggestions(SuggestionsRequest $request): JsonResponse
    {
        try {
            $tagtype = $request->input('tagtype');
            $term = $request->input('term');
            $result = $this->openFoodService->getSuggestions($tagtype, $term);

            if ($result['error']) {
                return JsonResponseHelper::jsonErrorResponse($result['message'], [], 404);
            }

            return JsonResponseHelper::jsonSuccessResponse($result['data']);
        } catch (Exception $e) {
            Log::error("Erro ao obter sugestões: " . $e->getMessage());
            return JsonResponseHelper::jsonErrorResponse('Erro ao obter sugestões. Tente novamente mais tarde.', [], 500);
        }
    }

    /**
     * Obtém uma lista de nutrientes.
     *
     * @param NutrientsRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function getNutrients(NutrientsRequest $request): JsonResponse
    {
        try {
            $cc = $request->input('cc');
            $lc = $request->input('lc');
            $result = $this->openFoodService->getNutrients($cc, $lc);

            if ($result['error']) {
                return JsonResponseHelper::jsonErrorResponse($result['message'], [], 404);
            }

            return JsonResponseHelper::jsonSuccessResponse($result['data']);
        } catch (Exception $e) {
            Log::error("Erro ao obter nutrientes: " . $e->getMessage());
            return JsonResponseHelper::jsonErrorResponse('Erro ao obter nutrientes. Tente novamente mais tarde.', [], 500);
        }
    }

    /**
     * Obtém os grupos de atributos para pesquisa personalizada.
     *
     * @param AttributeGroupsRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function getAttributeGroups(AttributeGroupsRequest $request): JsonResponse
    {
        try {
            $lc = $request->input('lc');
            $result = $this->openFoodService->getAttributeGroups($lc);

            if ($result['error']) {
                return JsonResponseHelper::jsonErrorResponse($result['message'], [], 404);
            }

            return JsonResponseHelper::jsonSuccessResponse($result['data']);
        } catch (Exception $e) {
            Log::error("Erro ao obter grupos de atributos: " . $e->getMessage());
            return JsonResponseHelper::jsonErrorResponse('Erro ao obter grupos de atributos. Tente novamente mais tarde.', [], 500);
        }
    }

    /**
     * Obtém as preferências de atributos para cálculos de recomendação.
     *
     * @param PreferencesRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function getPreferences(PreferencesRequest $request): JsonResponse
    {
        try {
            $lc = $request->input('lc');
            $result = $this->openFoodService->getPreferences($lc);

            if ($result['error']) {
                return JsonResponseHelper::jsonErrorResponse($result['message'], [], 404);
            }

            return JsonResponseHelper::jsonSuccessResponse($result['data']);
        } catch (Exception $e) {
            Log::error("Erro ao obter preferências: " . $e->getMessage());
            return JsonResponseHelper::jsonErrorResponse('Erro ao obter preferências. Tente novamente mais tarde.', [], 500);
        }
    }

    /**
     * Rotaciona a foto de um produto.
     *
     * @param RotateProductPhotoRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function rotateProductPhoto(RotateProductPhotoRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $response = $this->openFoodService->rotateProductPhoto($data);

            if ($response['success']) {
                // Extrai apenas os dados necessários para evitar redundâncias
                $cleanedData = $response['data']['data'] ?? $response['data'];

                return JsonResponseHelper::jsonSuccessResponse(
                    $cleanedData,
                    $response['data']['message'] ?? 'Foto rotacionada com sucesso.'
                );
            }

            return JsonResponseHelper::jsonErrorResponse($response['message'], [], 400);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return JsonResponseHelper::jsonErrorResponse('Erro ao rotacionar a foto.', [], 500);
        }
    }

    /**
     * Recorta a foto de um produto.
     *
     * @param CropPhotoRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function cropProductPhoto(CropPhotoRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $response = $this->openFoodService->cropProductPhoto($data);

            if (!$response['success']) {
                return JsonResponseHelper::jsonErrorResponse($response['message'], [], 400);
            }

            return JsonResponseHelper::jsonSuccessResponse($response['data'], 'Imagem recortada com sucesso.');
        } catch (Exception $e) {
            Log::error("Erro ao recortar a imagem: " . $e->getMessage());
            return JsonResponseHelper::jsonErrorResponse('Erro ao recortar a imagem.', [], 500);
        }
    }
}
