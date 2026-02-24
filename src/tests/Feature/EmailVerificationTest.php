<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;
use App\Models\User;
use App\Notifications\CustomVerifyEmail;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** 会員登録後に認証メールが送信される */
    public function test_verification_email_is_sent_after_registration()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name'                  => 'test',
            'email'                 => 'verifytest@example.com',
            'password'              => 'password12345',
            'password_confirmation' => 'password12345',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'email' => 'verifytest@example.com',
        ]);

        $user = User::where('email', 'verifytest@example.com')->first();

        Notification::assertSentTo([$user], CustomVerifyEmail::class);

        $this->assertNull($user->email_verified_at);
    }

    /** 認証画面から認証リンクにアクセスするとメール認証が完了する */
    public function test_verification_notice_screen_links_to_verification_site()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->actingAs($user);

        $this->get('/email/verify')->assertStatus(200);

        // 認証リンク生成
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // 認証リンクアクセス
        $verifyResponse = $this->get($url);
        $verifyResponse->assertStatus(302);

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    /** 認証済みユーザーはプロフィール編集ページへリダイレクトされる */
    public function test_verified_user_is_redirected_to_profile_page()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id'   => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->get($url);

        $response->assertRedirect(route('mypage.profile.edit'));
    }
}