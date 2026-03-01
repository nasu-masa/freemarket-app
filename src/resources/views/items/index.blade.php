@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/items.css') }}">
@endsection

@section('content')

{{-- タブ --}}
<div class="l-container">

    <div class="c-tabs u-mt-48">
        <a href="/?tab=recommend&keyword={{ $keyword }}"
            class="c-tabs__item {{ $tab === 'recommend' ? 'is-active' : '' }}">
            おすすめ
        </a>

        <a href="/?tab=myList&keyword={{ $keyword }}"
            class="c-tabs__item {{ $tab === 'myList' ? 'is-active' : '' }}">
            マイリスト
        </a>
    </div>
</div>

<hr class="l-divider">

{{-- 商品一覧 --}}
<div class="c-product-list">
    @foreach ($items as $item)
    <div class="c-product-card">
        <a href="{{ route('item.show', ['item_id' => $item->id]) }}"
            class="c-product-card__link">
            <div class="c-product-card__image-wrapper
                {{ $item->status === 'sold' ? 'is-sold' : '' }}">
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