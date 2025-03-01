<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductCategoryController extends ApiController
{
    public function __construct()
    {
        $this->middleware('client.credentials')->only(['index']);
        $this->middleware('auth:api')->except(['index']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Product $product)
    {
        $categories = $product->categories;

        return $this->showAll($categories);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Product  $product
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Product $product, Category $category)
    {
        $product->categories()->syncWithoutDetaching([$category->id]);

        return $this->showAll($product->categories);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Product $product, Category $category)
    {
        if (!$product->categories()->find($category->id)) {
            return $this->errorResponse('The specified category is not a category of this product', 404);
        }

        $product->categories()->detach($category->id);

        return $this->showAll($product->categories);
    }
}
