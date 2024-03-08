<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductRepository
{
    protected $model;
    
    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    // Retrieve all products.
    public function all()
    {
        return $this->model->all();
    }

    // Create a new product.
    public function create(array $data)
    {
        return $this->model->create([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'image' => $this->imageUpload($data['image']), // Save image path in the database
        ]);
    }

    // Update product by id.
    public function update($id, $data)
    {
        $product = $this->find($id);

        // Update product fields
        $product->name = $data['name'] ?? $product->name;
        $product->description = $data['description'] ?? $product->description;
        $product->price = $data['price'] ?? $product->price;

        // Update image if provided
        if (isset($data['image'])) {
             // Delete the previous image file if it exists
             if ($product->image) {
                Storage::delete($product->image);
            }

            $product->image = $this->imageUpload($data['image']);
        }

        $product->save();

        return $product;
    }

    // Delete product by id.
    public function delete($id)
    {
        $product = $this->find($id);
        if ($product->image) {
            Storage::delete($product->image);
        }
        $product->delete();
    }

    // Find a product by id.
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    // Retrieve products by category id.
    public function findByCategoryId($category_id)
    {
        return $this->model->where('category_id', $category_id)->get();
    }

    private function imageUpload($imageData)
    {
        // Decode base64 image and save it to storage
        $image = base64_decode($imageData);

        $imageName = 'product_' . time(); // You may customize the image name as needed
        $path = 'products/' . $imageName;

        Storage::put($path, $image);

        return $path;
    }
}
