@extends('layouts.app')

@section('content')

<div class="c-page-header">

    <div class="c-profile">
        <div class="c-profile__left">

            <!-- プロフィール画像 -->
            <img src="{{ $user->avatar_path ?? asset('assets/default.icon.png') }}"
                alt="プロフィール画像"
                class="c-profile-image">

            <!-- ユーザー名 -->
            <h2 class="c-profile__name">{{ $user->name }}</h2>
        </div>

        <a href="/mypage/profile" class="c-image-button c-image-button--lg">
            プロフィールを編集
        </a>
    </div>

    {{-- タブ --}}
    <div class="c-tabs">
        <a href="/mypage?page=sell" class="c-tabs__item {{ $tab === 'sell' ? 'is-active' : '' }}">
            出品した商品
        </a>

        <a href="/mypage?page=buy" class="c-tabs__item {{ $tab === 'buy' ? 'is-active' : '' }}">
            購入した商品
        </a>
    </div>

</div>

<hr class="l-divider u-mb-16 ">
</hr>

{{-- 商品一覧 --}}
<div class="c-product-list">
    @foreach ($items as $item)
    <div class="c-product-card">
        <a href="{{ route('item.show', ['item_id' => $item->id]) }}"
            class="c-product-card__link">
            <div class="c-product-card__image-wrapper
                {{ $tab === 'buy' ? '' : ($item->status === 'sold' ? 'is-sold' : '') }}">
                <img src="{{ asset('storage/' . $item->images->first()->image_path) }}"
                    alt="商品画像"
                    class="c-product-card__image">
            </div>

            <p class="c-product-card__name">{{ $item->name }}</p>
        </a>
    </div>
    @endforeach
</div>



@endsection