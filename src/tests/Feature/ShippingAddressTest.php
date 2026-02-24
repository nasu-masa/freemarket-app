<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Services\StripeService;

class ShippingAddressTest extends TestCase
{
    use RefreshDatabase;

    // 住所登録済みユーザーと商品を準備
    private function prepareUserWithAddress()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => 'selling']);

        $this->actingAs($user);

        $this->put(route('purchase.address.update', ['item_id' => $item->id]), [
            'postal_code' => '123-4567',
            'address'     => '東京都台東区テスト1-2-3',
            'building'    => 'コーポⅡ 101号室',
        ])->assertRedirect();

        $user->refresh();

        return [$user, $item];
    }

    public function test_address_is_reflected_in_purchase_page()
    {
        [$item] = $this->prepareUserWithAddress();

        $response = $this->get(route('purchase.checkout', ['item_id' => $item->id]));
        $response->assertStatus(200);

        $response->assertSee('東京都台東区テスト1-2-3');
        $response->assertSee('コーポⅡ 101号室');
    }

    public function test_address_is_attached_to_purchased_item()
    {
        [$user, $item] = $this->prepareUserWithAddress();

        $this->get(route('purchase.checkout', ['item_id' => $item->id]));

        // Stripe モック
        $mock = \Mockery::mock(StripeService::class);
        $mock->shouldReceive('createCheckoutSession')
            ->andReturn((object)[
                'id'  => 'cs_test_123',
            ]);
        $this->app->instance(StripeService::class, $mock);

        // 購入処理（Stripe へリダイレクト）
        $this->post(route('purchase.store', ['item_id' => $item->id]), [
            'payment' => 'card',
        ])->assertRedirect("/purchase/{$item->id}");

        // Stripe 成功後のコールバック
        $this->get(route('purchase.success', ['item_id' => $item->id]))
            ->assertStatus(302);

        // 購入レコード確認
        $purchase = Purchase::where('user_id', $user->id)
            ->where('item_id', $item->id)
            ->firstOrFail();

        $this->assertNotNull($purchase->purchased_at);
    }
}
