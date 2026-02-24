<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\MyListItem;

class ItemIndexMyListTest extends TestCase
{
    use RefreshDatabase;

    /** いいねした商品だけが表示される */
    public function test_only_liked_items_are_displayed_in_my_list()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $likedItem = Item::factory()->create([
            'user_id' => User::factory(),
            'status'  => 'selling',
        ]);

        $notLikedItem = Item::factory()->create([
            'user_id' => User::factory(),
            'status'  => 'selling',
        ]);

        $ownItem = Item::factory()->create([
            'user_id' => $user->id,
            'status'  => 'selling',
        ]);

        MyListItem::create(['user_id' => $user->id, 'item_id' => $likedItem->id]);
        MyListItem::create(['user_id' => $user->id, 'item_id' => $ownItem->id]);

        $image = $likedItem->images()->first();

        $response = $this->get('/?tab=myList');

        $response->assertSee($image->image_path);
        $response->assertSeeText($likedItem->name);
        $response->assertDontSeeText($notLikedItem->name);
        $response->assertDontSeeText($ownItem->name);
    }

    /** 購入済み商品には sold ラベルが表示される */
    public function test_sold_items_have_sold_label_in_my_list()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $soldItem = Item::factory()->create([
            'user_id' => User::factory(),
            'status'  => 'sold',
        ]);

        MyListItem::create(['user_id' => $user->id, 'item_id' => $soldItem->id]);

        $image = $soldItem->images()->first();

        $response = $this->get('/?tab=myList');

        $response->assertSee($image->image_path);
        $response->assertSee('is-sold');
    }

    /** 未ログインの場合は何も表示されない */
    public function test_my_list_shows_nothing_when_unauthenticated()
    {
        $response = $this->get('/?tab=myList');

        $response->assertDontSee('<div class="c-product-card">', false);
    }
}
