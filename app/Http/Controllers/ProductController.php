<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ProductRepository;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * The product repository instance.
     *
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * Create a new controller instance.
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $categoryId = $request->input('category_id');

        if ($categoryId) {
            $products = $this->productRepository->findByCategoryId($categoryId);
        } else {
            $products = $this->productRepository->all();
        }

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  CreateProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $productData = $this->validateProduct($request);

        $product = $this->productRepository->create($productData);

        return new ProductResource($product);
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new ProductResource($product);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        $productData = $this->validateProduct($request, $id);
        $product = $this->productRepository->update($id, $productData);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new ProductResource($product);
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        $deleted = $this->productRepository->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Product not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Product deleted successfully']);
    }

    /**
     * Validate the incoming request data.
     */
    private function validateProduct($request, $id = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required',
            'description' => 'required',
            'price' => 'required'
        ]);
    }
}
