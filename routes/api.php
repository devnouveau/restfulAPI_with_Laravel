<?php

use App\Http\Controllers\Buyer\BuyerController;
use App\Http\Controllers\Buyer\BuyerProductController;
use App\Http\Controllers\Buyer\BuyerTransactionController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Category\CategoryProductController;
use App\Http\Controllers\Product\ProductBuyerController;
use App\Http\Controllers\Product\ProductBuyerTransactionController;
use App\Http\Controllers\Product\ProductCategoryController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\ProductTransactionController;
use App\Http\Controllers\Seller\SellerBuyerController;
use App\Http\Controllers\Seller\SellerController;
use App\Http\Controllers\Seller\SellerProductController;
use App\Http\Controllers\Seller\SellerTransactionController;
use App\Http\Controllers\Transaction\TransactionController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// TODO : 추후 사용하지 않는 메서드 컨트롤러에서 일괄 제거
/**
 * Buyers
 */
Route::resource('buyers', BuyerController::class, ['only' => ['index', 'show']]); // 구매자 목록조회, 단건조회
Route::resource('buyers.products', BuyerProductController::class, ['only' => ['index']]); // 특정 구매자가 구매한 상품 목록 조회
Route::resource('buyers.transactions', BuyerTransactionController::class, ['only' => ['index']]); // 특정 구매자의 거래 목록 조회

/**
 * Categories
 */
Route::resource('categories', CategoryController::class, ['only' => ['index', 'show', 'store', 'update', 'destroy']]); // 상품의 카테고리 목록조회, 단건조회, 생성, 수정, 삭제
Route::resource('categories.products', CategoryProductController::class, ['only' => ['index']]); // 특정 카테고리에 속하는 상품 목록 조회

/**
 * Products
 */
Route::resource('products', ProductController::class, ['only' => ['index', 'show']]); // 상품 목록조회, 단건조회
Route::resource('products.buyers', ProductBuyerController::class, ['only' => ['index']]); // 특정 상품에 대한 구매자 목록 -> 어디에 필요한 걸까?
Route::resource('products.categories', ProductCategoryController::class, ['only' => ['index', 'update', 'destroy']]); // 특정 상품이 속한 카테고리 목록조회, 상품-카테고리 연결 추가, 삭제
Route::resource('products.transactions', ProductTransactionController::class, ['only' => ['index']]); // 특정 상품의 거래 목록조회
Route::resource('products.buyers.transactions', ProductBuyerTransactionController::class, ['only' => ['store']]); // 특정 상품 거래 체결

/**
 * Sellers
 */
Route::resource('sellers', SellerController::class, ['only' => ['index', 'show']]); // 판매자 목록조회, 단건조회
Route::resource('sellers.buyers', SellerBuyerController::class, ['only' => ['index']]); // 특정 판매자와 거래한 구매자 목록
Route::resource('sellers.products', SellerProductController::class, ['only' => ['index', 'store', 'update', 'destroy']]); // 특정 판매자가 가진 상품 목록조회, 등록, 수정, 삭제
Route::resource('sellers.transactions', SellerTransactionController::class, ['only' => ['index']]); // 특정 판매자의 거래 목록

/**
 * Transactions
 */
Route::resource('transactions', TransactionController::class, ['only' => ['index', 'show']]); // 거래 목록조회, 단건조회

/**
 * Users
 */
Route::resource('users', UserController::class,['only' => ['index', 'show', 'store', 'update', 'destroy']]); // 사용자 목록조회, 단건조회, 등록, 수정, 삭제
Route::name('verify')->get('users/verify/{token}', [UserController::class, 'verify']); // 토큰을 이용해 사용자를 인증된 상태로 변경 (사용자 생성, 이메일 변경시 토큰이 인증주소에 포함되어 사용자의 메일로 발송됨)
Route::name('resend')->get('users/{user}/resend', [UserController::class, 'resend']); // 인증메일 재발송

