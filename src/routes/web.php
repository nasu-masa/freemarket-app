<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MyListItemController;
use App\Http\Controllers\CommentController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/
// 商品一覧（トップ）
Route::get('/', [ItemController::class, 'index'])
    ->name('items.index');

// 商品詳細
Route::get('/item/{item_id}', [ItemController::class, 'show'])
    ->name('item.show');

// 会員登録
Route::get('/register', [RegisterController::class, 'show'])
    ->name('register.show');
Route::post('/register', [RegisterController::class, 'store'])
    ->name('register.store');

// ログイン
Route::get('/login', [LoginController::class, 'show'])
    ->name('login');
Route::post('/login', [LoginController::class, 'login'])
    ->name('login.store');

/*
|--------------------------------------------------------------------------
| Auth Required
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // ログアウト
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');

    Route::post('/item/{item_id}/like', [MyListItemController::class, 'store'])
        ->name('item.like');

    Route::post('/item/{item_id}/unlike', [MyListItemController::class, 'destroy'])
        ->name('item.unlike');

    Route::post('/item/{item_id}/comments', [CommentController::class, 'store'])
        ->name('item.comments.store');

    // 商品購入画面
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'checkout'])
        ->name('purchase.checkout');

    // 商品購入処理
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])
        ->name('purchase.store');

    Route::get('/success/{item_id}', [PurchaseController::class, 'success'])
        ->name('purchase.success');
    Route::get('/cancel/{item_id}', [PurchaseController::class, 'cancel'])
        ->name('purchase.cancel');

    // 住所変更ページ
    Route::get('/purchase/address/{item_id}', [AddressController::class, 'editAddress'])
        ->name('purchase.address.edit');
    Route::put('/purchase/address/{item_id}', [AddressController::class, 'updateAddress'])
        ->name('purchase.address.update');


    // 商品出品画面
    Route::get('/sell', [ItemController::class, 'create'])
        ->name('sell.create');

    // 商品出品処理
    Route::post('/sell', [ItemController::class, 'store'])
        ->name('sell.store');
});

/*
|--------------------------------------------------------------------------
| My Page
|--------------------------------------------------------------------------
*/
Route::middleware('auth', 'verified')->group(function () {

    // プロフィール（マイページ）
    Route::get('/mypage', [ProfileController::class, 'index'])
        ->name('mypage.index');

    // プロフィール編集
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])
        ->name('mypage.profile.edit');

    Route::put('/mypage/profile', [ProfileController::class, 'update'])
        ->name('mypage.profile.update');
});

// 誘導画面（ログイン後の未認証チェック
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// メール内のリンクを踏んだときの “認証処理”
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('mypage.profile.edit')->with('success', '認証が完了しました');
})->middleware(['auth', 'signed'])->name('verification.verify');

// 認証メールの “再送”
Route::post('/email/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return redirect()
            ->route('verification.notice')
            ->with('success', '認証メールを再送しました');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');