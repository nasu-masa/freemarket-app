<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;

class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    /** 全商品が一覧に表示される */
    public function test_all_items_are_displayed()
    {
        $items = Item::factory()->count(3)->create();

        $response = $this->get('/');

        foreach ($items as $item) {
            $response->assertSee($item->name);

            $image = $item->images()->first();
            $response->assertSee($image->image_path);
        }
    }

    /** sold 商品には soldラベルが付与される */
    public function test_sold_items_have_sold_class()
    {
        $item = Item::factory()->create([
            'status' => 'sold',
        ]);

        $image = $item->images()->first();

        $response = $this->get('/');
        $response->assertSee('is-sold');
        $response->assertSee($image->image_path);
    }

    /** 自分が出品した商品はタブに表示されない */
    public function test_own_items_are_not_displayed()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $ownItem = Item::factory()->create([
            'user_id' => $user->id,
            'status'  => 'selling',
        ]);

        $otherItem = Item::factory()->create([
            'user_id' => User::factory(),
            'status'  => 'selling',
        ]);

        $response = $this->get('/?tab=recommend');

        $response->assertDontSeeText($ownItem->name);
        $response->assertSeeText($otherItem->name);
    }
}
