<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_displays_user_information()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'name' => 'テスト太郎',
            'avatar_path' => 'test-avatar.png',
        ]);

        // 出品中の商品
        $sellingItems = Item::factory()
            ->count(2)
            ->create([
                'user_id' => $user->id,
                'status'  => 'selling',
            ]);

        // 購入済みの商品
        $purchasedItems = Purchase::factory()
            ->count(2)
            ->for(Item::factory())
            ->create([
                'user_id' => $user->id,
            ]);

        $this->actingAs($user);

        // プロフィールページ（出品した商品タブ）
        $response = $this->get(route('mypage.index'));
        $response->assertStatus(200);
        $response->assertSee('テスト太郎');
        $response->assertSee('test-avatar.png');

        foreach ($sellingItems as $item) {
            $response->assertSee($item->name);
        }

        // 購入済みタブ
        $response = $this->get('/mypage?page=buy');
        $response->assertStatus(200);

        foreach ($purchasedItems as $purchase) {
            $response->assertSee($purchase->item->name);
        }
    }
}
