<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Address;

class UserProfileEditTest extends TestCase
{
    use RefreshDatabase;

    /** プロフィール編集ページにユーザー情報が表示される */
    public function test_profile_edit_page_displays_user_information()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'name'        => 'テスト太郎',
            'avatar_path' => 'test-avatar.png',
        ]);

        Address::factory()->create([
            'user_id'     => $user->id,
            'postal_code' => '123-4567',
            'address'     => '東京都台東区1-2-3',
            'building'    => 'コーポⅡ 101号室',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('mypage.profile.edit'));
        $response->assertStatus(200);

        $response->assertSee('テスト太郎');
        $response->assertSee('test-avatar.png');
        $response->assertSee('123-4567');
        $response->assertSee('東京都台東区1-2-3');
        $response->assertSee('コーポⅡ 101号室');
    }
}
