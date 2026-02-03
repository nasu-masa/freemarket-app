<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/

// PG01: 商品一覧（トップ）
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// PG02: 商品一覧（マイリストタブ）※クエリで判定
// /?tab=mylist

// PG03: 会員登録
Route::get('/register', [AuthController::class, 'showRegister'])->name('register.show');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');

// PG04: ログイン
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.show');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');

// ログアウト（画面はないが必要）
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// PG05: 商品詳細
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');

// 商品削除（出品者のみ）
Route::delete('/item/{item_id}', [ItemController::class, 'destroy'])->name('item.destroy');

// PG06: 商品購入画面
Route::get('/purchase/{item_id}', [PurchaseController::class, 'index'])->name('purchase.index');

// 購入処理（POST）
Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchase.store');

// PG07: 送付先住所変更
Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
Route::put('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

// PG08: 商品出品
Route::get('/sell', [SellController::class, 'index'])->name('sell.index');
Route::post('/sell', [SellController::class, 'store'])->name('sell.store');


/*
|--------------------------------------------------------------------------
| My Page
|--------------------------------------------------------------------------
*/

// PG09: プロフィール（マイページ）
Route::get('/mypage', [MyPageController::class, 'index'])->name('mypage.index');

// PG10: プロフィール編集
Route::get('/mypage/profile', [MyPageController::class, 'editProfile'])->name('mypage.profile.edit');
Route::put('/mypage/profile', [MyPageController::class, 'updateProfile'])->name('mypage.profile.update');

// PG11: 購入した商品一覧（?page=buy）
// PG12: 出品した商品一覧（?page=sell）
// → /mypage?page=buy /mypage?page=sell
// → コントローラ内で request('page') を参照