<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;
use App\Models\MyListItem;

class ItemShowTest extends TestCase
{
    use RefreshDatabase;

    /** 商品詳細ページに必要な情報が表示される */
    public function test_item_detail_displays_all_required_information()
    {
        // 商品
        $item = Item::factory()->create([
            'name'        => '金の斧',
            'brand'       => 'nympha fontis',
            'price'       => 1980,
            'description' => '誤って近所の泉に斧を落としたときに水中より現れた泉の精霊にもらった金の斧です。',
            'condition'   => '目立った傷や汚れなし',
        ]);

        $image = $item->images()->first();

        // コメント投稿者
        $commentUser = User::factory()->create([
            'avatar_path' => 'test_profile.jpg',
        ]);

        // コメント
        Comment::factory()->create([
            'item_id' => $item->id,
            'user_id' => $commentUser->id,
            'content' => '本物の金でできてるんですか？',
        ]);

        // いいね（3件）
        MyListItem::factory()->count(3)->create([
            'item_id' => $item->id,
        ]);

        $response = $this->get(route('item.show', ['item_id' => $item->id]));
        $response->assertStatus(200);

        // 商品情報
        $response->assertSee($image->image_path);
        $response->assertSee('金の斧');
        $response->assertSee('nympha fontis');
        $response->assertSee('1,980');
        $response->assertSee('金の斧です。');
        $response->assertSee('目立った傷や汚れなし');

        // コメント
        $response->assertSee('test_profile.jpg');
        $response->assertSee($commentUser->name);
        $response->assertSee('本物の金でできてるんですか？');

        // いいね数
        $response->assertSee('3');
    }

    /** 複数カテゴリが表示される */
    public function test_item_detail_displays_multiple_categories()
    {
        $item = Item::factory()->create();

        $categories = Category::factory()->count(3)->create();
        $item->categories()->attach($categories->pluck('id'));

        $response = $this->get(route('item.show', ['item_id' => $item->id]));
        $response->assertStatus(200);

        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }
}
