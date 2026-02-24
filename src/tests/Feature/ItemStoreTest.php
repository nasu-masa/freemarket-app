<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemStoreTest extends TestCase
{
    use RefreshDatabase;

    /** 商品が正常に保存され、画像もアップロードされる */
    public function test_item_can_be_stored()
    {
        Storage::fake('public');

        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::factory()->create();

        // 出品ページ表示
        $this->get(route('sell.create'))->assertStatus(200);

        $postData = [
            'categories'  => [$category->id],
            'condition'   => '良好',
            'name'        => '黄金の斧',
            'brand'       => 'arma aurea',
            'description' => '隣村の泉のそばに落ちてました',
            'price'       => 3980,
            'image'       => UploadedFile::fake()->create('test.jpeg', 100, 'image/jpeg'),
        ];

        // 商品登録
        $response = $this->post(route('sell.store'), $postData);

        $item = Item::first();
        $response->assertRedirect(route('item.show', ['item_id' => $item->id]));

        // items テーブル
        $this->assertDatabaseHas('items', [
            'condition'   => '良好',
            'name'        => '黄金の斧',
            'brand'       => 'arma aurea',
            'description' => '隣村の泉のそばに落ちてました',
            'price'       => 3980,
        ]);

        // 中間テーブル（カテゴリ紐付け）
        $this->assertDatabaseHas('category_item', [
            'item_id'     => $item->id,
            'category_id' => $category->id,
        ]);

        // 画像アップロード
        Storage::disk('public')->assertExists($item->images()->first()->image_path);

        // item_images テーブル
        $this->assertDatabaseHas('item_images', [
            'item_id'    => $item->id,
            'image_path' => $item->images()->first()->image_path,
        ]);
    }
}