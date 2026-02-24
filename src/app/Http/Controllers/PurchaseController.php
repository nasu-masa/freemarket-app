<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Purchase;
use App\Services\StripeService;

class PurchaseController extends Controller
{
    public function checkout($item_id)
    {
        $item = Item::findOrFail($item_id);

        // 自分の商品は購入不可
        if ($item->user_id === auth()->id()) {
            abort(403, '自分の商品は購入できません');
        }

        // 販売中以外は購入不可
        if ($item->status !== 'selling') {
            abort(403, 'この商品は購入できません');
        }

        $address = auth()->user()->address;

        return view('purchase.checkout', compact('item', 'address'));
    }

    public function store(PurchaseRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        // 自分の商品は購入不可
        if ($item->user_id === auth()->id()) {
            abort(403, '自分の商品は購入できません');
        }

        // 販売中以外は購入不可
        if ($item->status !== 'selling') {
            abort(403, 'この商品は購入できません');
        }

        $payment = $request->payment;

        // カード決済
        if ($payment === 'card') {
            return $this->payByCard($item, app(StripeService::class));
        }

        // コンビニ決済
        if ($payment === 'convenience') {
            return $this->payByConvenience($item);
        }

        return redirect()
            ->route('purchase>checkout')
            ->with('error', '不正な支払い方法です');
    }

    private function payByCard($item, StripeService $stripeService)
    {
        // Stripe セッション作成
        $session = $stripeService->createCheckoutSession($item);

        return redirect($session->url);
    }

    private function payByConvenience($item)
    {
        // 売却処理
        $item->update(['status' => 'sold']);

        // 購入履歴
        Purchase::create([
            'user_id' => auth()->id(),
            'item_id' => $item->id,
            'purchased_at' => now(),
        ]);

        return redirect()
            ->route('items.index')
            ->with('success', '購入手続きが完了しました');
    }

    public function success($item_id)
    {
        // Stripe 成功時の売却処理
        $item = Item::findOrFail($item_id);
        $item->update(['status' => 'sold']);

        // 購入履歴
        Purchase::create([
            'user_id' => auth()->id(),
            'item_id' => $item_id,
            'purchased_at' => now(),
        ]);

        return redirect()
            ->route('items.index')
            ->with('success', '購入手続きが完了しました');
    }

    public function cancel($item_id)
    {
        // キャンセル時は購入画面へ戻す
        return redirect()
            ->route('purchase.checkout', ['item_id' => $item_id])
            ->with('error', '購入手続きが中断されました');
    }
}
