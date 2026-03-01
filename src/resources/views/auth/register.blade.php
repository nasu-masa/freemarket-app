@extends('layouts.guest')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/auth.css') }}">
@endsection

@section('content')
<div class="c-card">
    <h2 class="c-card__title p-register__title u-mb-48">会員登録</h2>

    <form action="/register" method="POST">
        @csrf

        {{-- ユーザー名 --}}
        <div class="c-input">
            <label class="c-input__label">ユーザー名</label>
            <input
                type="text"
                name="name"
                value="{{ old('name') }}"
                class="c-input__field c-input--lg">

            <div class="c-error">
                <span class="c-error__text">
                    @error('name')
                    {{ $message }}
                    @enderror
                </span>
            </div>
        </div>

        {{-- メールアドレス --}}
        <div class="c-input">
            <label class="c-input__label">メールアドレス</label>
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="c-input__field c-input--lg">

            <div class="c-error">
                <span class="c-error__text">
                    @error('email')
                    {{ $message }}
                    @enderror
                </span>
            </div>
        </div>

        {{-- パスワード --}}
        <div class="c-input">
            <label class="c-input__label">パスワード</label>
            <input
                type="password"
                name="password"
                class="c-input__field c-input--lg">

            <div class="c-error">
                <span class="c-error__text">
                    @error('password')
                    {{ $message }}
                    @enderror
                </span>
            </div>
        </div>

        {{-- 確認用パスワード --}}
        <div class="c-input">
            <label class="c-input__label">確認用パスワード</label>
            <input
                type="password"
                name="password_confirmation"
                class="c-input__field c-input--lg">

            <div class="c-error">
                <span class="c-error__text">
                    @error('password_confirmation')
                    {{ $message }}
                    @enderror
                </span>
            </div>
        </div>

        <div class="l-button-wrapper u-mt-80">
            <button
                type="submit"
                class="c-button c-button--lg c-button--primary">
                登録する
            </button>
        </div>
    </form>

    <div class="c-link u-mt-32">
        <a href="/login" class="c-link__text">ログインはこちら</a>
    </div>
</div>
@endsection