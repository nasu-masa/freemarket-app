@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/pages/purchase.css') }}">
@endsection

@section('content')
    <div class="l-container p-purchase">

        {{-- 購入フォーム --}}
        <form
            action="{{ route('purchase.store', ['item_id' => $item->id]) }}"
            method="post"
            class="p-purchase__form">
            @csrf

            <div class="p-purchase__main">

                {{-- 商品情報 --}}
                <div class="p-purchase__item">
                    <img
                        src="{{ asset('storage/' . $item->images->first()->image_path) }}"
                        alt=""
                        class="p-purchase__item-image">

                    <div class="p-purchase__item-info">
                        <h2 class="p-purchase__item-name">{{ $item->name }}</h2>
                        <p class="p-purchase__item-price">
                            ¥<span class="p-purchase__item-price--value">
                                {{ number_format($item->price) }}
                            </span>
                        </p>
                    </div>
                </div>

                <hr class="l-divider">

                {{-- old値保持 --}}
                <input type="hidden" id="old-payment" value="{{ old('payment') }}">

                {{-- 支払い方法 --}}
                <section class="c-section c-section--purchase">
                    <h3 class="c-section__title c-section__title--md">支払い方法</h3>

                    <div class="c-select c-select--purchase">

                        <input
                            type="checkbox"
                            id="payment_method"
                            class="c-select__checkbox u-cursor-pointer">

                        <label for="payment_method" class="c-select__label">
                            選択してください
                        </label>

                        <div class="c-select__options">
                            <input
                                type="radio"
                                name="payment"
                                id="payment_convenience"
                                value="convenience"
                                class="c-select__radio"
                                {{ old('payment') === 'convenience' ? 'checked' : '' }}>
                            <label for="payment_convenience" class="c-select__option">
                                コンビニ払い
                            </label>

                            <input
                                type="radio"
                                name="payment"
                                id="payment_card"
                                value="card"
                                class="c-select__radio"
                                {{ old('payment') === 'card' ? 'checked' : '' }}>
                            <label for="payment_card" class="c-select__option">
                                カード払い
                            </label>
                        </div>

                        {{-- 支払い方法エラー --}}
                        <div class="p-error">
                            <span class="p-error__text">
                                @error('payment')
                                {{ $message }}
                                @enderror
                            </span>
                        </div>

                    </div>
                </section>

                <hr class="l-divider">

                {{-- 配送先 --}}
                <section class="c-section c-section--purchase">

                    <div class="p-purchase__section-container">
                        <h3 class="c-section__title c-section__title--md">配送先</h3>

                        @if ($address)
                        <a
                            href="{{ route('purchase.address.edit', ['item_id' => $item->id]) }}"
                            class="p-purchase__address-edit">
                            変更する
                        </a>
                        @endif
                    </div>

                    <div class="p-purchase__address">

                        {{-- 郵便番号 --}}
                        <p class="p-purchase__address-postal">
                            〒{{ $address->postal_code ?? 'XXX-YYYY' }}
                        </p>

                        {{-- 住所表示 --}}
                        <p
                            class="p-purchase__address-detail"
                            id="address-ui"
                            data-address="{{ $address->address ?? '' }}"
                            data-building="{{ $address->building ?? '' }}">
                            @if (!$address)
                            <span class="u-text-muted">住所が未登録です</span><br>
                            <a
                                href="{{ route('purchase.address.edit', ['item_id' => $item->id]) }}"
                                class="c-button p-address-register">
                                住所登録はこちらから
                            </a>
                            @else
                            {{ $address->address }}<br>
                            {{ $address->building }}
                            @endif
                        </p>

                        {{-- hidden 住所 --}}
                        <input
                            type="hidden"
                            name="address"
                            id="address-hidden"
                            value="{{ optional($address)->address ?? '' }}">
                        <input
                            type="hidden"
                            name="building"
                            id="building-hidden"
                            value="{{ optional($address)->building ?? '' }}">

                        {{-- 住所エラー --}}
                        <div class="p-error">
                            <span class="p-error__text">
                                @error('address')
                                {{ $message }}
                                @enderror
                            </span>
                        </div>

                    </div>
                </section>

                <hr class="l-divider">

            </div>

            {{-- 購入サマリー --}}
            <div class="p-purchase__summary">

                <table class="p-purchase-checkout__table">
                    <tr class="p-purchase-checkout__table-row">
                        <th class="p-purchase-checkout__table-title">商品代金</th>
                        <td class="p-purchase-checkout__table-price">
                            ¥<span class="p-purchase-checkout__table-value">
                                {{ number_format($item->price) }}
                            </span>
                        </td>
                    </tr>

                    <tr class="p-purchase-checkout__table-row">
                        <th class="p-purchase-checkout__table-title">支払い方法</th>
                        <td class="p-purchase-checkout__table-value" id="summary-payment">
                            @if (old('payment') === 'convenience')
                            コンビニ払い
                            @elseif (old('payment') === 'card')
                            カード払い
                            @else
                            未選択
                            @endif
                        </td>
                    </tr>
                </table>

                <div class="c-button__wrapper u-mt-64">
                    <button type="submit" class="c-button c-button--sm c-button--primary">
                        購入する
                    </button>
                </div>

            </div>

        </form>

    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/payment-method-select.js') }}"></script>
    <script src="{{ asset('js/address-sync.js') }}"></script>
@endsection