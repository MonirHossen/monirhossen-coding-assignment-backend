<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    protected $categoryRepository;


    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    //Display a listing of the categories.
    public function index(Request $request)
    {
        $parentId = $request->input('parent_id');

        // If parent_id is provided, filter categories by parent_id
        if ($parentId) {
            $categories = $this->categoryRepository->findByParentId($parentId);
        } else {
            $categories = $this->categoryRepository->all();
        }

        return CategoryResource::collection($categories);
    }

    //Store a newly created category in storage.
    public function store(Request $request)
    {
        // Validate user registration data
        $validatedData = $this->validateCategory($request);

        $category = $this->categoryRepository->create($validatedData);

        return response()->json(new CategoryResource($category), 201);
    }

    //Update the specified category in storage.
    public function update(Request $request, $id)
    {
        // Validate user registration data
        $validatedData = $this->validateCategory($request, $id);

        $category = $this->categoryRepository->update($id, $validatedData);

        return response()->json(new CategoryResource($category), 200);
    }

    // Remove the specified category from storage.

    public function find($id)
    {
        $category = $this->categoryRepository->find($id);

        return response()->json(new CategoryResource($category), 200);
    }

    public function destroy($id)
    {
        $this->categoryRepository->delete($id);

        return response()->json(null, 204);
    }

    // Validate Categroy data
    private function validateCategory(Request $request, $id = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$id,
            'parent_id' => 'nullable'
        ]);
    }
}
