<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Services\StripeService;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\URL;

class ItemPurchaseTest extends TestCase
{
    use RefreshDatabase;

    /**  StripeServiceの署名検証などskipするため、mockしてルートを再定義し購入処理する */
    //   本来mockはいらない処理
    private function purchaseItemProcessing(User $user, Item $item)
    {
        $this->withoutExceptionHandling();

        // StripeService をモック
        $mock = \Mockery::mock(\App\Services\StripeService::class);

        $mock->shouldReceive('createCheckoutSession')
            ->andReturn((object)[
                'id'  => 'cs_test_123',
                'url' => '/fake-stripe-redirect',
            ]);

        $mock->shouldReceive('verifyWebhookSignature')
            ->withAnyArgs()
            ->andReturn((object)[
                'type' => 'checkout.session.completed',
                'data' => (object)[
                    'object' => (object)[
                        'id' => 'cs_test_123',
                        'payment_intent' => 'pi_test_456',
                        'payment_method_types' => ['card'],
                    ],
                ],
            ]);

        $mock->shouldReceive('retrievePaymentIntent')
            ->with('pi_test_456')
            ->andReturn((object)[
                'metadata' => (object)[
                    'user_id' => $user->id,
                    'item_id' => $item->id,
                ],
            ]);

        $this->app->instance(StripeService::class, $mock);

        $this->app['router']
            ->middleware('api')
            ->post('/stripe/webhook',[StripeWebhookController::class, 'handle']);

        // カード決済リクエスト（Checkoutセッション作成）
        $this->actingAs($user)->post(route('purchase.store', ['item_id' => $item->id]), [
            'payment_method' => 'card',
            'address' => '東京都渋谷区1-2-3',
        ])->assertRedirect('/fake-stripe-redirect');

        // Webhookイベントを送信（purchaseItem() をトリガー）
        $this->postJson('/stripe/webhook', [], [
            'Stripe-Signature' => 'dummy-signature',
        ])->assertOk();

        // 成功画面にアクセス（署名付きURL）
        $this->get(URL::signedRoute('purchase.success', ['item_id' => $item->id]))
            ->assertRedirect(route('items.index'))
            ->assertSessionHas('success', '購入手続きが完了しました');
    }


    /** 購入が完了すると商品が sold になる */
    public function test_user_can_complete_purchase()
    {
        $user = User::factory()->withAddress()->create();
        $item = Item::factory()->create(['status' => 'selling']);

        $this->purchaseItemProcessing($user, $item);

        $this->assertDatabaseHas('items', [
            'id'     => $item->id,
            'status' => 'sold',
        ]);
    }

    /** sold ラベルが商品一覧に表示される */
    public function test_sold_label_is_displayed_in_item_list()
    {
        $user = User::factory()->withAddress()->create();
        $item = Item::factory()->create(['status' => 'selling']);

        $this->purchaseItemProcessing($user, $item);

        $this->get(route('items.index'))
            ->assertSee('sold');
    }

    /** 購入した商品がマイページの購入一覧に表示される */
    public function test_purchased_item_appears_in_profile_buy_list()
    {
        $user = User::factory()->withAddress()->create();
        $item = Item::factory()->create(['status' => 'selling']);

        $this->purchaseItemProcessing($user, $item);

        $this->get('/mypage?page=buy')
            ->assertSee($item->name);
    }
}
