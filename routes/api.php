
<?php
  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
  
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CategoryController;
   
Route::controller(RegisterController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::get('products', [ProductController::class, 'index']);
Route::get('categories', [CategoryController::class, 'index']);
         
Route::middleware('auth:sanctum')->group( function () {
    Route::resource('products', ProductController::class)->except(['index']);
    Route::resource('categories', CategoryController::class)->except(['index']);
});

Route::options('{any}', function () {
    return response()->json([], 204);
})->where('any', '.*');
