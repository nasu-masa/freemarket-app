<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Services\StripeService;

class ItemPurchaseTest extends TestCase
{
    use RefreshDatabase;

    // 購入処理（Stripe モック含む）
    private function purchaseItem(User $user, Item $item)
    {
        $this->actingAs($user);

        $mock = \Mockery::mock(StripeService::class);
        $mock->shouldReceive('createCheckoutSession')
            ->andReturn((object)[
                'id'  => 'cs_test_123',
                'url' => '/fake-stripe-redirect',
            ]);

        $this->app->instance(StripeService::class, $mock);

        $this->post(route('purchase.store', ['item_id' => $item->id]), [
            'payment' => 'card',
        ]);

        $this->get(route('purchase.success', ['item_id' => $item->id]));
    }

    /** 購入が完了すると商品が sold になる */
    public function test_user_can_complete_purchase()
    {
        $user = User::factory()->withAddress()->create();
        $item = Item::factory()->create(['status' => 'selling']);

        $this->purchaseItem($user, $item);

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

        $this->purchaseItem($user, $item);

        $this->get(route('items.index'))
            ->assertSee('sold');
    }

    /** 購入した商品がマイページの購入一覧に表示される */
    public function test_purchased_item_appears_in_profile_buy_list()
    {
        $user = User::factory()->withAddress()->create();
        $item = Item::factory()->create(['status' => 'selling']);

        $this->purchaseItem($user, $item);

        $this->get('/mypage?page=buy')
            ->assertSee($item->name);
    }
}
