<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    #[OA\Get(
        path: "/api/products",
        summary: "Obtener lista de productos disponibles",
        tags: ["Productos"]
    )]
    #[OA\Response(
        response: 200,
        description: "Lista de productos obtenida con éxito."
    )]
    

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $products = $this->productService->getPaginated($search);

        return response()->json($products);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json($product);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Producto creado con éxito.',
            'data' => $product
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $updatedProduct = $this->productService->update($product, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado con éxito.',
            'data' => $updatedProduct
        ]);
    }

    /**
     * Remove the specified resource from storage (Soft Delete).
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->productService->delete($product);

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado con éxito (lógicamente).'
        ]);
    }
}
