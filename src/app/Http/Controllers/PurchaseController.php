<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Services\StripeService;

class PurchaseController extends Controller
{
    public function checkout($item_id)
    {
        $item = Item::findOrFail($item_id);

        if (! auth()->user()->can('purchase', $item)) {
            throw new AuthorizationException('この商品は購入できません');
        }

        $user = auth()->user();
        $latestAddress = $user->latestAddress;

        return view('purchase.checkout', compact('item', 'latestAddress'));
    }

    public function store(PurchaseRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        if (! auth()->user()->can('purchase', $item)) {
            throw new AuthorizationException('この商品は購入できません');
        }

        $payment_method = $request->payment_method;

        // コンビニ決済
        if ($payment_method === 'convenience') {
            return $this->payByConvenience($item);
        }

        // カード決済
        if ($payment_method === 'card') {
            return $this->payByCard($item, app(StripeService::class));
        }

        return redirect()
            ->route('purchase.checkout', ['item_id' => $item_id])
            ->with('error', '不正な支払い方法です');
    }

    private function payByConvenience($item)
    {
        auth()->user()->purchaseItem($item, 'convenience');

        return redirect()
            ->route('items.index')
            ->with('success', '購入手続きが完了しました');
    }

    private function payByCard($item, StripeService $stripeService)
    {
        // Stripe セッション作成
        $session = $stripeService->createCheckoutSession($item);

        return redirect($session->url);
    }

    public function success(Request $request, $item_id)
    {
        if (! $request->hasValidSignature()) {
            abort(403, '無効なアクセスです');
        }

        $item = Item::findOrFail($item_id);

        auth()->user()->purchaseItem($item, 'convenience');

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
