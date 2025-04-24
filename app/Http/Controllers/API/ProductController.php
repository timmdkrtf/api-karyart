<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
   
class ProductController extends BaseController
{
    public function index(): JsonResponse
    {
        $products = Product::with('category')->get();
    
        return $this->sendResponse(ProductResource::collection($products), 'Products retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'detail_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id'
        ]);
   
        $imagePath = $request->file('detail_image')->store('catalogs', 'public');
   
        $product = Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'detail_image' => $imagePath,
            'description' => $request->description,
        ]);
    
   
        return $this->sendResponse(new ProductResource($product), 'Product created successfully.');
    } 

    public function show($id): JsonResponse
    {
        $product = Product::find($id);
  
        if (is_null($product)) {
            return $this->sendError('Product not found.');
        }
   
        return $this->sendResponse(new ProductResource($product), 'Product retrieved successfully.');
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $input = $request->all();
    
        $validator = Validator::make($input, [
            'name' => 'required',
            'detail_image' => 'required',
            'description' => 'required',
            'category_id' => 'required'
        ]);
    
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
    
        // Hapus gambar lama jika ada
        if ($product->detail_image && File::exists(public_path('storage/' . $product->detail_image))) {
            File::delete(public_path('storage/' . $product->detail_image));
        }
    
        // Simpan gambar baru
        $imagePath = $request->file('detail_image')->store('catalogs', 'public');
    
        // Simpan data produk
        $product->name = $input['name'];
        $product->detail_image = $imagePath;
        $product->description = $input['description'];
        $product->category_id = $input['category_id'];
        $product->save();
    
        return $this->sendResponse(new ProductResource($product), 'Product updated successfully.');
    }
    

    public function destroy($id): JsonResponse
    {
        $product = Product::find($id);
        File::delete(public_path('storage/' . $product->detail_image));

        $product->delete();
   
        return $this->sendResponse([], 'Product deleted successfully.');
    }
}
