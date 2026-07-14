<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $products = $this->productService->getPaginated($search);

        return Inertia::render('Products/Index', [
            'products' => $products,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->productService->create($request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Producto creado con éxito.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->productService->update($product, $request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado con éxito.');
    }

    /**
     * Remove the specified resource from storage (Soft Delete).
     */
    public function destroy(Product $product): RedirectResponse
    {
        $this->productService->delete($product);

        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado con éxito (lógicamente).');
    }
}
