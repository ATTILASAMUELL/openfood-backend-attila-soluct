<?php

namespace App\Repositories;

use OpenFoodFacts\Laravel\OpenFoodFacts;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Exceptions\OpenFoodException;
use Illuminate\Support\Collection;
use App\Helpers\CryptographyHelper;

class OpenFoodRepository
{
    use CryptographyHelper;

    protected $client;
    protected $baseUri;
    protected $credentials;

    public function __construct()
    {
        $this->client = app(OpenFoodFacts::class);
        $this->baseUri = config('openfood.base_uri');
        $this->credentials = config('openfood.credentials');
    }

    /**
     * Adiciona uma foto a um produto existente.
     *
     * @param array $data Dados para adicionar a foto.
     * @return array Resultado da operação.
     * @throws OpenFoodException
     */
    public function addProductPhoto(array $data): array
    {
        try {
            $payload = [
                'code' => $data['code'],
                'imagefield' => $data['imagefield'],
                'imgupload_' . $data['imagefield'] => $data['image'], // Imagem enviada com base no campo imagefield
                'user_id' => $data['user_id'] ?? $this->credentials['user_id'],
                'password' => $data['password'] ?? $this->credentials['password'],
            ];

            $response = Http::withOptions(['verify' => false])
                ->asMultipart()
                ->post("{$this->baseUri}cgi/product_image_upload.pl", $payload);

            $responseData = $response->json();
            // Tratamento para verificar sucesso ou erro com base em $responseData
            if (isset($responseData['status']) && $responseData['status'] === 'status ok') {
                return [
                    'success' => true,
                    'data' => $responseData,
                    'message' => 'Foto adicionada com sucesso.',
                ];
            } elseif (isset($responseData['error'])) {
                // Tratamento para erros
                throw new OpenFoodException(
                    $responseData['error'],
                    0,
                    null,
                    ['response_data' => $responseData]
                );
            } else {
                throw new OpenFoodException(
                    'Falha ao adicionar foto ao produto.',
                    0,
                    null,
                    ['response_data' => $responseData]
                );
            }
        } catch (\Exception $e) {
            Log::error("Erro ao adicionar foto ao produto: {$e->getMessage()}");
            throw new OpenFoodException(
                "Erro ao adicionar foto ao produto.",
                0,
                $e,
                ['payload' => $payload, 'error_message' => $e->getMessage()]
            );
        }
    }


    /**
     * Adiciona ou edita um produto no Open Food Facts usando requisição direta com HTTP.
     *
     * @param array $data Dados do produto a serem adicionados ou editados.
     * @return array Resultado da operação, incluindo status e mensagem.
     * @throws OpenFoodException Caso ocorra algum erro ao realizar a operação.
     */
    public function addOrEditProduct(array $data): array
    {
        try {
            $payload = [
                'code' => $data['code'],
                'user_id' => $data['user_id'] ?? $this->credentials['user_id'],
                'password' => $data['password'] ?? $this->credentials['password'],
                'brands' => implode(',', $data['brands'] ?? []),
                'labels' => implode(',', $data['labels'] ?? []),
                'categories' => implode(',', $data['categories'] ?? []),
                'packaging' => $data['packaging'] ?? '',
                'comment' => $data['comment'] ?? 'new packaging from super-app',
                'app_name' => $data['app_name'] ?? config('app.name'),
                'app_version' => $data['app_version'] ?? '1.0',
                'app_uuid' => $data['app_uuid'] ?? uniqid('', true),
            ];

            $response = Http::withOptions(['verify' => false])
                ->asForm()
                ->post("{$this->baseUri}cgi/product_jqm2.pl", $payload);

            $responseData = $response->json();

            if (isset($responseData['status']) && $responseData['status'] === 1) {
                return [
                    'success' => true,
                    'data' => $responseData,
                    'message' => $responseData['status_verbose'] ?? 'Produto adicionado/editado com sucesso.'
                ];
            } else {
                throw new OpenFoodException(
                    $responseData['status_verbose'] ?? 'Falha ao adicionar/editar o produto.',
                    0,
                    null,
                    ['response_data' => $responseData]
                );
            }
        } catch (\Exception $e) {
            Log::error("Erro ao adicionar/editar produto: {$e->getMessage()}");
            throw new OpenFoodException(
                "Erro ao adicionar/editar o produto.",
                0,
                $e,
                ['payload' => $payload, 'error_message' => $e->getMessage()]
            );
        }
    }

    /**
     * Busca informações de um produto pelo código de barras.
     *
     * @param string $barcode Código de barras do produto.
     * @return array Dados do produto.
     * @throws OpenFoodException Caso ocorra algum erro na busca.
     */
    public function getProductByBarcode(string $barcode): array
    {
        try {
            $product = $this->client->barcode($barcode);

            if (!empty($product)) {
                return $this->mapProductData($product);
            }

            return [];
        } catch (\Exception $e) {
            Log::error("Erro ao buscar produto pelo código de barras: " . $e->getMessage());
            throw new OpenFoodException("Erro ao buscar produto pelo código de barras.", 0, $e);
        }
    }

    /**
     * Pesquisa produtos com base em um termo.
     *
     * @param string $query Termo de pesquisa.
     * @param int $page Número da página.
     * @param int $pageSize Número de itens por página.
     * @param array $filters Filtros adicionais para a pesquisa.
     * @return Collection Coleção de produtos encontrados.
     * @throws OpenFoodException Caso ocorra algum erro na busca.
     */
    public function searchProducts(array $filters): Collection
    {
        try {

            $response = Http::withOptions(['verify' => false])->get("{$this->baseUri}/api/v2/search", $filters);

            $responseData = $response->json();

            if (isset($responseData['products']) && is_array($responseData['products']) && !empty($responseData['products'])) {

                return collect($responseData['products'])->map(function ($product) {
                    return $this->mapProductData($product);
                });
            }

            throw new OpenFoodException('Nenhum produto encontrado para a pesquisa.');

        } catch (\Exception $e) {
            throw new OpenFoodException("Erro ao pesquisar produtos.", 0, $e);
        }
    }




    /**
     * Obtém ingredientes usando OCR para um produto específico
     *
     * @param string $id
     * @param string $code
     * @param string $processImage
     * @param string $ocrEngine
     * @return array
     * @throws OpenFoodException
     */
    public function getIngredientsUsingOCR(string $id, string $code, string $processImage, string $ocrEngine): array
    {
        try {
            $params = [
                'id' => $id,
                'code' => $code,
                'process_image' => $processImage,
                'ocr_engine' => $ocrEngine
            ];

            $response = Http::withOptions(['verify' => false])->get("{$this->baseUri}/cgi/ingredients.pl", $params);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['status']) && $data['status'] == 1) {
                    Log::error("Erro ao obter ingredientes via OCR: Status 1 retornado.");
                    throw new OpenFoodException(
                        "Falha ao obter ingredientes: status de erro retornado.",
                        0,
                        null,
                        ['response' => $data]
                    );
                }

                return $data;
            }

            Log::error("Erro ao obter ingredientes via OCR: " . $response->body());
            throw new OpenFoodException(
                "Falha ao obter ingredientes com OCR.",
                0,
                null,
                ['response' => $response->json()]
            );
        } catch (\Exception $e) {
            Log::error("Erro ao obter ingredientes via OCR: " . $e->getMessage());
            throw new OpenFoodException(
                "Erro ao obter ingredientes via OCR.",
                0,
                $e,
                ['code' => $code, 'details' => $e->getMessage()]
            );
        }
    }


    /**
     * Busca sugestões para auxiliar na adição/edição de produtos.
     *
     * @param string $tagtype Tipo de tag para sugestões.
     * @param string $term Termo de pesquisa.
     * @return array Lista de sugestões.
     * @throws OpenFoodException Caso ocorra algum erro na busca de sugestões.
     */
    public function getSuggestions(string $tagtype, string $term): array
    {
        try {
            $response = Http::withOptions(['verify' => false])->get("{$this->baseUri}/cgi/suggest.pl", [
                'tagtype' => $tagtype,
                'term' => $term,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("Erro ao buscar sugestões: " . $response->body());
            throw new OpenFoodException("Falha ao buscar sugestões.", 0, null, ['response' => $response->json()]);
        } catch (\Exception $e) {
            Log::error("Erro ao buscar sugestões: " . $e->getMessage());
            throw new OpenFoodException("Erro ao buscar sugestões.", 0, $e);
        }
    }

    /**
     * Obtém lista de nutrientes para exibir na tabela de informações nutricionais.
     *
     * @param string $cc Código do país.
     * @param string $lc Código da língua.
     * @return array Lista de nutrientes.
     * @throws OpenFoodException Caso ocorra algum erro ao buscar nutrientes.
     */
    public function getNutrients(string $cc, string $lc): array
    {
        try {
            $response = Http::withOptions(['verify' => false])->get("{$this->baseUri}/cgi/nutrients.pl", [
                'cc' => $cc,
                'lc' => $lc,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("Erro ao buscar nutrientes: " . $response->body());
            throw new OpenFoodException("Falha ao buscar nutrientes.", 0, null, ['response' => $response->json()]);
        } catch (\Exception $e) {
            Log::error("Erro ao buscar nutrientes: " . $e->getMessage());
            throw new OpenFoodException("Erro ao buscar nutrientes.", 0, $e);
        }
    }

     /**
     * Obtém a lista de grupos de atributos para pesquisa pessoal.
     *
     * @param string $lc Código da língua.
     * @return array Lista de grupos de atributos.
     * @throws OpenFoodException Caso ocorra algum erro ao buscar grupos de atributos.
     */
    public function getAttributeGroups(string $lc): array
    {
        try {
            $response = Http::withOptions(['verify' => false])->get("{$this->baseUri}/api/v2/attribute_groups", [
                'lc' => $lc,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("Erro ao obter grupos de atributos: " . $response->body());
            throw new OpenFoodException('Nenhum grupo de atributos encontrado para a linguagem especificada.');
        } catch (\Exception $e) {
            Log::error("Erro ao obter grupos de atributos: " . $e->getMessage());
            throw new OpenFoodException("Erro ao obter grupos de atributos.", 0, $e);
        }
    }


    /**
     * Obtém as preferências de atributos para cálculos de produto pessoal.
     *
     * @param string $lc Código da língua.
     * @return array Lista de preferências.
     * @throws OpenFoodException Caso ocorra algum erro ao buscar preferências.
     */
    public function getPreferences(string $lc): array
    {
        try {
            $response = Http::withOptions(['verify' => false])->get("{$this->baseUri}/api/v2/preferences", [
                'lc' => $lc,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (empty($data)) {
                    throw new OpenFoodException('Nenhuma preferência encontrada para a linguagem especificada.');
                }
                return $data;
            }

            Log::error("Erro ao obter preferências: " . $response->body());
            throw new OpenFoodException("Falha ao obter preferências.");

        } catch (\Exception $e) {
            Log::error("Erro ao obter preferências: " . $e->getMessage());
            throw new OpenFoodException("Erro ao obter preferências.", 0, $e);
        }
    }


    public function rotateProductPhoto(array $data): array
    {
        try {
            $response = Http::withOptions(['verify' => false])
                ->get("{$this->baseUri}cgi/product_image_crop.pl", [
                    'code' => $data['code'],
                    'id' => $data['id'],
                    'imgid' => $data['imgid'],
                    'angle' => $data['angle'],
                    'user_id' => $data['user_id'] ?? $this->credentials['user_id'],
                    'password' => $data['password'] ?? $this->credentials['password'],
                ]);

            $responseData = $response->json();

            if (isset($responseData['status']) && $responseData['status'] === 'status ok') {
                return [
                    'success' => true,
                    'data' => $responseData,
                    'message' => 'Foto rotacionada com sucesso.',
                ];
            } else {
                $errorMessage = $responseData['error'] ?? 'Falha ao rotacionar a foto.';
                throw new OpenFoodException($errorMessage);
            }
        } catch (\Exception $e) {
            Log::error("Erro ao rotacionar foto: {$e->getMessage()}");
            throw new OpenFoodException("Erro ao rotacionar foto.", 0, $e);
        }
    }

    /**
     * Recorta a foto de um produto.
     *
     * @param array $data
     * @return array
     */
    public function cropProductPhoto(array $data): array
    {
        try {
            $payload = [
                'code' => $data['code'],
                'imgid' => $data['imgid'],
                'id' => $data['id'],
                'x1' => $data['x1'] ?? null,
                'y1' => $data['y1'] ?? null,
                'x2' => $data['x2'] ?? null,
                'y2' => $data['y2'] ?? null,
                'angle' => $data['angle'] ?? 0,
                'normalize' => $data['normalize'] ?? false,
                'white_magic' => $data['white_magic'] ?? false,
            ];

            return [
                'success' => true,
                'message' => 'Foto recortada com sucesso.',
                'data' => $payload
            ];
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao recortar a foto.',
            ];
        }
    }



    /**
     * Mapeia os dados do produto para os campos principais.
     *
     * @param array $product Dados brutos do produto a serem mapeados.
     * @return array Dados do produto com apenas os campos principais.
     */
    private function mapProductData(array $product): array
    {
        return [
            'id' =>$this->encryptValue($product['_id']) ?? null,
            'barcode' => $this->encryptValue($product['code']) ?? null,
            'name' => $product['product_name'] ?? 'N/A',
            'categories' => $product['categories_tags'] ?? [],
            'brands' => $product['brands'] ?? 'N/A',
            'ingredients_text' => $product['ingredients_text'] ?? 'N/A',
            'nutriments' => $product['nutriments'] ?? [],
            'image_url' => $product['image_url'] ?? null,
            'quantity' => $product['quantity'] ?? 'N/A',
        ];
    }
}
