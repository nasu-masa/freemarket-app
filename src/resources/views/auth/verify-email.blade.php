@extends('layouts.guest')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/auth.css') }}">
<link rel="stylesheet" href="{{ asset('css/pages/email.css') }}">
@endsection

@section('content')
<div class="p-email">

    <p class="p-email__text u-text-center">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </p>

    <div class="p-email__button">
        <a href="http://localhost:8025"
            class="p-email__button--submit u-flex-center u-cursor-pointer ">
            認証はこちらから
        </a>
    </div>

    <div class="c-link u-flex-center">
        <form action="/email/resend" method="post">
            @csrf
            <button type="submit"
                class="c-link__text u-cursor-pointer u-background-none u-border-none">
                認証メールを再送する
            </button>
        </form>
    </div>

</div>
@endsection