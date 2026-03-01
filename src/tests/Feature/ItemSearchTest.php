<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_items_are_search_by_partial_match_keyword()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        Item::factory()->create(['name' => '金の斧']);
        Item::factory()->create(['name' => '銀の斧']);

        Item::factory()->create(['name' => '銅のじょうろ']);

        $response = $this->get('/?keyword=斧');

        $response->assertSeeText('金の斧');
        $response->assertSeeText('銀の斧');

        $response->assertDontSeeText('銅のじょうろ');
    }

    public function test_search_keyword_is_preserved_in_my_list_tab()
    {
        $this->get('/?keyword=斧')
            ->assertSee('value="斧"', false);

        $response = $this->get('/?tab=myList');

        $response->assertSee('value="斧"', false);
    }
}

