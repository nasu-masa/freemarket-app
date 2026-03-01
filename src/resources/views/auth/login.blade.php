@extends('layouts.guest')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/auth.css') }}">
<link rel="stylesheet" href="{{ asset('css/pages/email.css') }}">
@endsection

@section('content')
<div class="c-card">
    <h2 class="c-card__title p-login__title u-mb-48">ログイン</h2>

    <form action="/login" method="POST">
        @csrf

        {{-- メールアドレス --}}
        <div class="c-input">
            <label class="c-input__label">メールアドレス</label>
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="c-input__field c-input--md">

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
                class="c-input__field c-input--md">

            <div class="c-error">
                <span class="c-error__text">
                    @error('password')
                    {{ $message }}
                    @enderror
                </span>
            </div>
        </div>

        <div class="l-button-wrapper u-mt-40">
            <button
                type="submit"
                class="c-button c-button--lg c-button--primary">
                ログインする
            </button>
        </div>
    </form>

    <div class="c-link u-mt-32">
        <a href="/register" class="c-link__text">会員登録はこちら</a>
    </div>
</div>
@endsection