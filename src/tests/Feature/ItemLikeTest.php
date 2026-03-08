<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\MyListItem;
use Tests\TestCase;

class ItemLikeTest extends TestCase
{
    use RefreshDatabase;

    /** ユーザーが商品にいいねできる */
    public function test_user_can_like_an_item()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $this->post(route('item.like', ['item_id' => $item->id]));

        // DB 登録
        $this->assertDatabaseHas('my_list_items', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // UI
        $response = $this->get(route('item.show', ['item_id' => $item->id]));
        $response->assertSee('1');         // いいね数
        $response->assertSee('is-liked');  // いいね済みアイコン
    }

    /** 既にいいね済みの場合は色付きアイコンが表示される */
    public function test_liked_icon_is_colored_when_user_already_liked()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create();

        MyListItem::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('item.show', ['item_id' => $item->id]));
        $response->assertSee('is-liked');
    }

    /** ユーザーがいいね解除できる */
    public function test_user_can_unlike_an_item()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create();

        MyListItem::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs($user);

        $this->post(route('item.like', ['item_id' => $item->id]));

        // DB 削除
        $this->assertDatabaseMissing('my_list_items', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // UI
        $response = $this->get(route('item.show', ['item_id' => $item->id]));
        $response->assertSee('0');            // いいね数
        $response->assertDontSee('is-liked'); // アイコン非表示
    }
}
