<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\CategoryProductResource;
use App\Http\Resources\Product\CategoryResource;
use App\Http\Resources\Product\ProductItemResource;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Traits\Files;
use App\Traits\Pagination;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    use Files, Pagination;

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'featured_image' => ['required', 'mimes:jpg,jpeg,png', 'max:5120'],
            'type' => ['required', 'in:physical,digital'],
            'title' => ['required', 'string', 'max:191'],
            'description' => ['required', 'string'],
            'about' => ['required', 'string'],
            'price' => ['array', 'min:2', 'max:2'],
            'price.0.currency' => ['required', 'string', 'in:NGN'],
            'price.0.amount' => ['required', 'numeric', 'min:0'],
            'price.1.currency' => ['required', 'string', 'in:USD'],
            'price.1.amount' => ['required', 'numeric', 'min:0'],
            'benefits' => ['array'],
            'benefits.*' => ['required', 'string', 'max:191'],
            'target_users' => ['array'],
            'target_users.*' => ['required', 'string', 'max:191'],
            'how_to_use' => ['array'],
            'how_to_use.*' => ['required', 'string', 'max:191'],
            'access_delivery' => ['array'],
            'access_delivery.*' => ['required', 'string', 'max:191']
        ]);

        $featuredImage = $this->uploadFile($request->file('featured_image'), 'products/featured-images');
        $data['featured_image'] = $featuredImage;
        $data['price'] = json_encode($data['price']);
        $data['benefits'] = json_encode($data['benefits'] ?? []);
        $data['target_users'] = json_encode($data['target_users'] ?? []);
        $data['how_to_use'] = json_encode($data['how_to_use'] ?? []);
        $data['access_delivery'] = json_encode($data['access_delivery'] ?? []);
        Product::create($data);

        return $this->success(null, 'Product created successfully.');
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'slug' => ['nullable', 'string', 'max:191', 'unique:product_categories,slug'],
            'description' => ['required', 'string']
        ]);

        ProductCategory::create($data);
        return $this->success(null, 'Category created successfully.');
    }

    public function updateCategory(ProductCategory $category, Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'slug' => ['nullable', 'string', 'max:191', 'unique:product_categories,slug,' . $category->id],
            'description' => ['required', 'string']
        ]);

        $category->update($data);
        return $this->success(null, 'Category updated successfully.');
    }

    public function destroyCategory(ProductCategory $category)
    {
        $category->delete();
        return $this->success(null, 'Category deleted successfully.');
    }

    public function update(Product $product, Request $request)
    {
        $data = $request->validate([
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'featured_image' => ['nullable', 'mimes:jpg,jpeg,png', 'max:5120'],
            'type' => ['required', 'in:physical,digital'],
            'title' => ['required', 'string', 'max:191'],
            'description' => ['required', 'string'],
            'about' => ['required', 'string'],
            'price' => ['array', 'min:2', 'max:2'],
            'price.0.currency' => ['required', 'string', 'in:NGN'],
            'price.0.amount' => ['required', 'numeric:', 'min:0'],
            'price.1.currency' => ['required', 'string', 'in:USD'],
            'price.1.amount' => ['required', 'numeric:', 'min:0'],
            'benefits' => ['array'],
            'benefits.*' => ['required', 'string', 'max:191'],
            'target_users' => ['array'],
            'target_users.*' => ['required', 'string', 'max:191'],
            'how_to_use' => ['array'],
            'how_to_use.*' => ['required', 'string', 'max:191'],
            'access_delivery' => ['array'],
            'access_delivery.*' => ['required', 'string', 'max:191']
        ]);

        $featuredImage = $request->file('featured_image') ?
            $this->uploadFile($request->file('featured_image'), 'products/featured-images') : $product->featured_image;
        $data['featured_image'] = $featuredImage;
        $data['price'] = json_encode($data['price']);
        $data['benefits'] = json_encode($data['benefits'] ?? []);
        $data['target_users'] = json_encode($data['target_users'] ?? []);
        $data['how_to_use'] = json_encode($data['how_to_use'] ?? []);
        $data['access_delivery'] = json_encode($data['access_delivery'] ?? []);
        $product->update($data);

        return $this->success(null, 'Product updated successfully.');
    }

    public function viewAll()
    {
        $products = Product::filter()->latest()->paginate(12);
        $list = ProductItemResource::collection($products);
        $data = $this->paginatedData($products, $list);

        return $this->success($data);
    }

    public function view(Product $product)
    {
        $data = new ProductItemResource($product);
        return $this->success($data);
    }

    public function overview()
    {
        $categories = ProductCategory::latest()->paginate(12);
        $list = CategoryProductResource::collection($categories);
        $data = $this->paginatedData($categories, $list);

        return $this->success($data);
    }

    public function viewCategories()
    {
        $categories = ProductCategory::latest()->paginate(12);
        $list = CategoryResource::collection($categories);
        $data = $this->paginatedData($categories, $list);

        return $this->success($data);
    }

    public function viewAllCategories()
    {
        $categories = ProductCategory::all();
        $list = CategoryResource::collection($categories);

        return $this->success($list);
    }

    public function viewCategory(ProductCategory $category)
    {
        $data = new CategoryResource($category);
        return $this->success($data);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return $this->success(null, 'Product deleted successfully.');
    }
}
