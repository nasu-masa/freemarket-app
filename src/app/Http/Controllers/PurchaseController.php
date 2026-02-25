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

        // 住所変更があればプロフィール住所を更新
        auth()->user()->address->update([
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'building' => $request->building, ]);

        $payment_method = $request->payment_method;

        // カード決済
        if ($payment_method === 'card') {
            return $this->payByCard($item, app(StripeService::class));
        }

        // コンビニ決済
        if ($payment_method === 'convenience') {
            return $this->payByConvenience($item);
        }

        return redirect()
            ->route('purchase.checkout', ['item_id' => $item_id])
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
            'payment_method' => 'convenience',
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
            'payment_method' => 'card',
            'purchased_at' => now(),
        ]);

        return redirect()
            ->route('items.index')
            ->with('success', '購入手続きが完了しました');
    }

    public function cancel($item_id)
    {
        return redirect()
            ->route('purchase.checkout', ['item_id' => $item_id])
            ->with('error', '購入手続きが中断されました');
    }
}
