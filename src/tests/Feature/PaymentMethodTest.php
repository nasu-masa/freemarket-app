<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;


class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_method_is_reflected_in_subtotal_page()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'status' => 'selling'
        ]);

        $this->actingAs($user);

        // 購入手続きページを開く
        $this->get(route('purchase.checkout', ['item_id' => $item->id]))
            ->assertStatus(200);

        // 支払い方法を選択して反映させる
        $response = $this->post(route('purchase.store', ['item_id' => $item->id]), [
            'payment' => 'convenience',
        ]);

        $response = $this->followRedirects($response);

        // 画面に選んだ支払い方法が表示されていること
        $response->assertSee('コンビニ払い');
    }
}
