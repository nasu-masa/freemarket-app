@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/items.css') }}">
@endsection

@section('content')

<div class="p-item-detail">

    <div class="p-item-detail__image-container">

        <div class="p-item-detail__image-card {{ $item->status === 'sold' ? 'is-sold' : '' }}">
            <img src="{{ asset('storage/' . $item->images->first()->image_path) }}"
                alt="商品画像"
                class="p-item-detail__image">
        </div>

    </div>

    <div class="p-item-detail__content">

        <div class="p-item-detail__basic">
            {{-- 基本情報ブロック --}}
            <h2 class="p-item-detail__basic-title">{{ $item->name }}</h2>

            <p class="p-item-detail__basic-brand">
                {{ $item->brand }}
            </p>

            <p class="p-item-detail__basic-price">
                ¥<span class="p-item-detail__basic-price-value">
                    {{ number_format($item->price) }}
                </span>
                (税込)
            </p>

            <div class="p-item-detail__actions">
                <div class="p-item-detail__like">
                    <form action="{{ $isLiked
                            ? route('item.unlike', ['item_id' => $item->id])
                            : route('item.like', ['item_id' => $item->id])
                        }}"
                        method="post" class="p-item-detail__like-form">
                        @csrf
                        <button type="submit"
                            class="p-item-detail__icon-button p-item-detail__like-button {{ $isLiked ? 'is-liked' : '' }}">
                        </button>

                        <span class="p-item-detail__count">{{ $likeCount }}</span>
                    </form>
                </div>

                <div class=" p-item-detail__stock">
                    <button
                        type="button"
                        class="p-item-detail__icon-button p-item-detail__stock-button
                            p-item-detail__comment-scroll-button">
                    </button>
                    <span class="p-item-detail__count">{{ $contentCount }}</span>
                </div>
            </div>

            <div class="c-button__wrapper">
                <div class="c-button__wrapper">

                    {{-- 出品者自身 & 販売中 --}}
                    @if ($item->user_id === auth()->id() && $item->status === 'selling')
                    <div class="c-button c-button--sm c-button--owner u-text-center">
                        出品中
                    </div>

                    {{-- 出品者以外 & 販売中 --}}
                    @elseif ($item->status === 'selling')
                    <a href="{{ route('purchase.checkout', ['item_id' => $item->id]) }}"
                        class="c-button c-button--sm c-button--primary u-text-center">
                        購入手続きへ
                    </a>

                    {{-- 売り切れ --}}
                    @elseif ($item->status === 'sold')
                    <div class="c-button c-button--sm c-button--sold-out u-text-center">
                        売り切れました
                    </div>
                    @endif

                </div>

            </div>
        </div>

        {{-- 商品説明ブロック --}}
        <div class="p-item-detail__description">
            <h3 class="p-item-detail__description-title">
                商品説明</h3>
            <p class="p-item-detail__description-text u-m-0">{{ $item->description }}</p>
        </div>

        {{-- 商品情報ブロック --}}
        <div class="p-item-detail__info">
            <h3 class="p-item-detail__info-title">商品の情報</h3>
            <table class="p-item-detail__info-table">
                <tr class="p-item-detail__info-row">
                    <th class="p-item-detail__info-label">カテゴリー</th>
                    @foreach ($categories as $category)
                    <td class="p-item-detail__info-value">
                        <span class="p-item-detail__category">
                            {{ $category->name }}
                        </span>
                    </td>
                    @endforeach
                </tr>
                <tr class="p-item-detail__info-row">
                    <th class="p-item-detail__info-label">商品の状態</th>
                    <td class="p-item-detail__info-value">
                        <span class="p-item-detail__condition">
                            {{ $item->condition }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- コメントブロック --}}
        <div class="p-item-detail__comments">
            <h3 class="p-item-detail__comments-title">
                コメント({{ $contentCount }})
            </h3>
            <div class="p-item-detail__comments-user">
                <img src="{{ $avatar ?? asset('assets/default.icon.png') }}"
                    alt=""
                    class="p-item-detail__comments-user-image">

                <label class="p-item-detail__comments-user-name">
                    {{ $content->user->name ?? '' }}
                </label>
            </div>

            <p id="commentList" class="p-item-detail__comments-list">
                <span class="p-item-detail__comments-text">
                    {{ $content?->content ?? 'コメントはまだありません。' }}
                </span>
            </p>


            <form action="{{ route('item.comments.store', ['item_id' => $item->id]) }}"
                method="post"
                class="p-item-detail__comment-form">
                @csrf
                <label for="commentInput"
                    class="
                        p-item-detail__comment-label
                        u-font-bold">
                    商品へのコメント
                </label>

                <textarea
                    id="commentInput"
                    name="content"
                    class="p-item-detail__comment-input"></textarea>

                <div class="p-error">
                    @error('content')
                    <span class="c-error__text">
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                <div class="c-button__wrapper">

                    <button type="submit"
                        class="
                            c-button
                            c-button--sm
                            c-button--primary">
                            @auth
                            コメントを送信する
                            @else
                            ログインしてコメントする
                            @endauth
                    </button>

                </div>
            </form>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script src="{{ asset('js/jump-scroll.js') }}"></script>
@endsection