<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** 名前が未入力の場合はエラーになる */
    public function test_name_empty()
    {
        $this->from('/register')->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password12345',
            'password_confirmation' => 'password12345'
        ])->assertRedirect('/register');

        $this->get('/register')->assertSee('お名前を入力してください');
    }

    /** メールアドレスが未入力の場合はエラーになる */
    public function test_email_empty()
    {
        $this->from('/register')->post('/register', [
            'name' => 'test',
            'email' => '',
            'password' => 'password12345',
            'password_confirmation' => 'password12345'
        ])->assertRedirect('/register');

        $this->get('/register')->assertSee('メールアドレスを入力してください');
    }

    /** パスワードが未入力の場合はエラーになる */
    public function test_password_empty()
    {
        $this->from('/register')->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => ''
        ])->assertRedirect('/register');

        $this->get('/register')->assertSee('パスワードを入力してください');
    }

    /** パスワードが7文字以下の場合はエラーになる */
    public function test_password_7_characters_or_less()
    {
        $this->from('/register')->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567'
        ])->assertRedirect('/register');

        $this->get('/register')->assertSee('パスワードは8文字以上で入力してください');
    }

    /** パスワード確認が一致しない場合はエラーになる */
    public function test_password_confirmation_no_match()
    {
        $this->from('/register')->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password12345',
            'password_confirmation' => 'confirmation12345'
        ])->assertRedirect('/register');

        $this->get('/register')->assertSee('パスワードと一致しません');
    }

    /** 正しい入力の場合は登録処理が成功する */
    public function test_input_correct()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'tests@example.com',
            'password' => 'password12345',
            'password_confirmation' => 'password12345'
        ]);

        $response->assertStatus(200);
        $response->assertSee('登録していただいたメールアドレスに認証メールを送付しました。');
        $response->assertSee('認証はこちら');

        $this->assertDatabaseHas('users', [
            'email' => 'tests@example.com'
        ]);
    }
}