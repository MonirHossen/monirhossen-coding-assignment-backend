<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class CategoryRepository
{
    protected $model;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }
    
    // Retrieve all categories.
    public function all()
    {
        return $this->model->all();
    }

    // create new category
    public function create($data)
    {
        return $this->model->create([
            'name' => $data['name'],
            'parent_id' => $data['parent_id'],
            'image' => $this->imageUpload($data['image']), // Save image path in the database
        ]);
    }
    // update category by id
    public function update($id, $data)
    {
        $category = $this->find($id);

        // Update product fields
        $category->name = $data['name'] ?? $category->name;
        $category->parent_id = $data['parent_id'] ?? $category->parent_id;

        // Update image if provided
        if (isset($data['image'])) {
             // Delete the previous image file if it exists
             if ($category->image) {
                Storage::delete($category->image);
            }
            
            $category->image = $this->imageUpload($data['image']);
        }

        $category->save();

        return $category;
    }
    // delete category by id
    public function delete($id)
    {
        $category = $this->find($id);
        if ($category->image) {
            Storage::delete($category->image);
        }
        $category->delete();
    }
    // find cateogry by id
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }
    // filter category by parent id
    public function findByParentId($parentId)
    {
        return $this->model->where('parent_id', $parentId)->get();
    }
    // for image upload
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
