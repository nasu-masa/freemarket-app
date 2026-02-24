<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class ItemCommentTest extends TestCase
{
    use RefreshDatabase;

    /** ユーザーはコメントを投稿できる */
    public function test_user_can_post_comment()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $this->post(route('item.comments.store', ['item_id' => $item->id]), [
            'content' => 'これはテストコメントです。',
        ]);

        // DB
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'これはテストコメントです。',
        ]);

        // UI
        $response = $this->get(route('item.show', ['item_id' => $item->id]));
        $response->assertSee('これはテストコメントです。');
        $response->assertSee('1'); // コメント数
    }

    /** ゲストはコメントを投稿できない */
    public function test_guest_cannot_post_comment()
    {
        $item = Item::factory()->create();

        $response = $this->post(route('item.comments.store', ['item_id' => $item->id]), [
            'content' => 'ゲストコメント',
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => 'ゲストコメント',
        ]);
    }

    /** コメントは必須 */
    public function test_comment_is_required()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $this->post(route('item.comments.store', ['item_id' => $item->id]), [
            'content' => '',
        ]);

        $response = $this->get(route('item.show', ['item_id' => $item->id]));
        $response->assertSee('コメントを入力してください');

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => '',
        ]);
    }

    /** コメントは255文字以内 */
    public function test_comments_must_not_exceed_255_characters()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $longText = str_repeat('あ', 256);

        $response = $this->post(route('item.comments.store', ['item_id' => $item->id]), [
            'content' => $longText,
        ]);

        $response->assertSessionHasErrors(['content']);

        $response = $this->get(route('item.show', ['item_id' => $item->id]));
        $response->assertSee('コメントは255文字以内で入力してください');

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => $longText,
        ]);
    }
}
