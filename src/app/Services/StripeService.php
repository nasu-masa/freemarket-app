<?php

namespace App\Services;

use Illuminate\Support\Facades\URL;
use Stripe\StripeClient;
use App\Models\Item;

class StripeService
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    /**
     * 商品購入用の Stripe Checkout セッションを作成
     */
    public function createCheckoutSession(Item $item)
    {
        return $this->stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency'     => 'jpy',
                        'unit_amount'  => $item->price,
                        'product_data' => [
                            'name' => $item->name,
                        ],
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode'        => 'payment',
            'success_url' => URL::signedRoute('purchase.success', ['item_id' => $item->id]),
            'cancel_url'  => route('purchase.cancel', ['item_id' => $item->id]),
        ]);
    }
}
