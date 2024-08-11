<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);

//Route::get('/check-mrp/{id}', [\App\Http\Controllers\MRPController::class, 'getMRPResult']);
Route::group(['middleware' => ['jwt.verify']], function () {

    Route::group(['prefix' => 'material'], function () {
        Route::match(['post', 'get'],'/', [\App\Http\Controllers\MaterialController::class, 'index']);
        Route::match(['post', 'get'],'/{id}', [\App\Http\Controllers\MaterialController::class, 'findByID']);
        Route::delete('/{id}/delete', [\App\Http\Controllers\MaterialController::class, 'destroy']);

    });

    Route::group(['prefix' => 'product'], function () {
        Route::match(['post', 'get'],'/', [\App\Http\Controllers\ProductController::class, 'index']);
        Route::match(['post', 'get'],'/{id}', [\App\Http\Controllers\ProductController::class, 'findByID']);
        Route::delete('/{id}/delete', [\App\Http\Controllers\ProductController::class, 'destroy']);
        Route::match(['post', 'get'],'/{id}/material', [\App\Http\Controllers\ProductController::class, 'product_material']);
    });

    Route::group(['prefix' => 'recipe'], function () {
        Route::match(['post', 'get'],'/', [\App\Http\Controllers\RecipeController::class, 'index']);
        Route::match(['post', 'get'],'/{id}', [\App\Http\Controllers\RecipeController::class, 'findByID']);
        Route::delete('/{id}/delete', [\App\Http\Controllers\RecipeController::class, 'destroy']);
        Route::match(['post', 'get'],'/{id}/material', [\App\Http\Controllers\RecipeController::class, 'product_material']);
    });

    Route::group(['prefix' => 'mrp'], function () {
        Route::match(['post', 'get'],'/', [\App\Http\Controllers\MRPController::class, 'getMRPResult']);
    });

    Route::group(['prefix' => 'material-in'], function () {
        Route::match(['post', 'get'],'/', [\App\Http\Controllers\MaterialInController::class, 'index']);
    });

    Route::group(['prefix' => 'product-out'], function () {
        Route::match(['post', 'get'],'/', [\App\Http\Controllers\ProductOutController::class, 'index']);
    });

    Route::group(['prefix' => 'material-in-report'], function () {
        Route::match(['post', 'get'],'/', [\App\Http\Controllers\ReportMaterialInController::class, 'index']);
    });

    Route::group(['prefix' => 'product-out-report'], function () {
        Route::match(['post', 'get'],'/', [\App\Http\Controllers\ReportProductOutController::class, 'index']);
    });
});
