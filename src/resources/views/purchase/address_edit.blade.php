@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/purchase.css') }}">
@endsection

@section('content')
<div class="c-card">
    <h2 class="c-card__title  p-address__title"> {{ $latestAddress ? '住所の変更' : '住所の登録' }} </h2>

    <form action="{{ route('purchase.address.store', ['item_id' => $item->id]) }}" method="post">
        @csrf

        <div class="p-address__input-area">
            {{-- 郵便番号 --}}
            <div class="c-input">
                <label for="postal_code" class="c-input__label">郵便番号</label>
                <input type="text"
                    name="postal_code"
                    value="{{ $latestAddress->postal_code ?? ''}}"
                    id="postal_code"
                    class="c-input__field c-input--sm">

                <div class="c-error u-mb-16">
                    @error('postal_code')
                    <span class="c-error__text">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- 住所 --}}
            <div class="c-input">
                <label for="address" class="c-input__label">住所</label>
                <input type="text"
                    name="address"
                    value="{{ $latestAddress->address ?? ''}}"
                    id="address"
                    class="c-input__field c-input--sm">

                <div class="c-error u-mb-40">
                    @error('address')
                    <span class="c-error__text">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- 建物名 --}}
            <div class="c-input">
                <label for="building" class="c-input__label">建物名</label>
                <input type="text"
                    name="building"
                    value="{{ $latestAddress->building ?? '' }}"
                    id="building"
                    class="c-input__field c-input--sm">
            </div>
        </div>

        <div class="l-button-wrapper">
            <button type="submit" class="c-button c-button--md c-button--primary">
                {{ $latestAddress ? '更新する' : '登録する' }}
            </button>
        </div>
    </form>
</div>
@endsection