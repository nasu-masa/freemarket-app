<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** メールアドレスが未入力の場合はエラーになる */
    public function test_email_empty()
    {
        User::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('password12345'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email'    => '',
            'password' => 'password12345',
        ]);

        $response->assertRedirect('/login');

        $this->get('/login')->assertSee('メールアドレスを入力してください');
    }

    /** パスワードが未入力の場合はエラーになる */
    public function test_password_empty()
    {
        User::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('password12345'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email'    => 'test@example.com',
            'password' => '',
        ]);

        $response->assertRedirect('/login');

        $this->get('/login')->assertSee('パスワードを入力してください');
    }

    /** 認証情報が間違っている場合はエラーになる */
    public function test_wrong_credentials()
    {
        User::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('password12345'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email'    => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect('/login');

        $this->get('/login')->assertSee('ログイン情報が登録されていません');
    }

    /** 正しい入力の場合はログインに成功する */
    public function test_login_success()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('password12345'),
        ]);

        $response = $this->post('/login', [
            'email'    => 'test@example.com',
            'password' => 'password12345',
        ]);

        $response->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
    }
}
