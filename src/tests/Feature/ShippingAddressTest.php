<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Services\StripeService;
use Illuminate\Support\Facades\URL;

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

        $this->post(route('purchase.address.store', ['item_id' => $item->id]), [
            'postal_code' => '123-4567',
            'address'     => '東京都台東区テスト1-2-3',
            'building'    => 'コーポⅡ 101号室',
        ])->assertRedirect();

        $user->refresh();

        return [$user, $item];
    }

    public function test_address_is_reflected_in_purchase_page()
    {
        [$user, $item] = $this->prepareUserWithAddress();

        $this->actingAs($user);

        $response = $this->get(route('purchase.checkout', ['item_id' => $item->id]));
        $response->assertStatus(200);

        $response->assertSee('東京都台東区テスト1-2-3');
        $response->assertSee('コーポⅡ 101号室');
    }

    public function test_address_is_attached_to_purchased_item()
    {
        [$user, $item] = $this->prepareUserWithAddress();

        $user->purchaseItem($item, 'card');

        // 購入レコード確認
        $purchase = Purchase::where('user_id', $user->id)
            ->where('item_id', $item->id)
            ->firstOrFail();

        $this->assertNotNull($purchase->purchased_at);
    }
}
