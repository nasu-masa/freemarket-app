@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/pages/mypage.css') }}">
@endsection

@section('content')
    <div class="c-card">
        <h2 class="c-card__title">商品の出品</h2>

        <section class="c-section">
            <label class="c-section__label">商品画像</label>

            <form action="{{ route('sell.store') }}" method="post" enctype="multipart/form-data" class="c-form">
                @csrf

                {{-- 商品画像 --}}
                <div class="c-input__area u-flex-center">
                    <label for="imageInput" class="c-image-button c-image-button--sm">
                        画像を選択する
                        <input
                            type="file"
                            name="image"
                            id="imageInput"
                            class="c-image-upload__field">
                    </label>
                </div>

                <div class="c-error u-mb-24">
                    <span class="c-error__text">
                        @error('image')
                        {{ $message }}
                        @enderror
                    </span>
                </div>
        </section>

        {{-- 商品詳細セクション --}}
        <section class="c-section">
            <h3 class="c-section__title c-section__title--lg">商品詳細</h3>
            <hr class="c-section__divider">

            {{-- カテゴリ --}}
            <div class="c-section__container">
                <label class="c-section__label u-mb-32">カテゴリー</label>

                <div class="c-section__input">
                    @foreach ($categories as $category)
                    <input
                        type="checkbox"
                        id="category_{{ $category->id }}"
                        name="categories[]"
                        value="{{ $category->id }}"
                        class="c-section__checkbox"
                        {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                    <label
                        for="category_{{ $category->id }}"
                        class="c-section__category u-cursor-pointer">
                        {{ $category->name }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="c-error--category">
                <span class="c-error__text">
                    @error('categories')
                    {{ $message }}
                    @enderror
                </span>
            </div>

            {{-- 商品状態 --}}
            <label class="c-section__label u-mb-16">商品の状態</label>

            <div class="c-select">
                <input
                    type="checkbox"
                    id="condition_toggle"
                    class="c-select__checkbox u-cursor-pointer">

                <label for="condition_toggle" class="c-select__label c-select__label--lg">
                    選択してください
                </label>

                <div class="c-select__options">
                    <input
                        type="radio"
                        name="condition"
                        id="condition_good"
                        class="c-select__radio"
                        value="良好"
                        {{ old('condition') === '良好' ? 'checked' : '' }}>
                    <label for="condition_good" class="c-select__option c-select__option--lg">良好</label>

                    <input
                        type="radio"
                        name="condition"
                        id="condition_clean"
                        class="c-select__radio"
                        value="目立った傷や汚れなし"
                        {{ old('condition') === '目立った傷や汚れなし' ? 'checked' : '' }}>
                    <label for="condition_clean" class="c-select__option c-select__option--lg">目立った傷や汚れなし</label>

                    <input
                        type="radio"
                        name="condition"
                        id="condition_some"
                        class="c-select__radio"
                        value="やや傷や汚れあり"
                        {{ old('condition') === 'やや傷や汚れあり' ? 'checked' : '' }}>
                    <label for="condition_some" class="c-select__option c-select__option--lg">やや傷や汚れあり</label>

                    <input
                        type="radio"
                        name="condition"
                        id="condition_bad"
                        class="c-select__radio"
                        value="状態が悪い"
                        {{ old('condition') === '状態が悪い' ? 'checked' : '' }}>
                    <label for="condition_bad" class="c-select__option c-select__option--lg">状態が悪い</label>
                </div>
            </div>

            <div class="c-error">
                <span class="c-error__text">
                    @error('condition')
                    {{ $message }}
                    @enderror
                </span>
            </div>
        </section>

        {{-- 販売情報セクション --}}
        <section class="c-section">
            <h3 class="c-section__title c-section__title--lg">商品名と説明</h3>
            <hr class="c-section__divider">

            {{-- 商品名 --}}
            <div class="c-input">
                <label class="c-input__label">商品名</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    class="c-input__field c-input--sm">
            </div>

            <div class="c-error">
                <span class="c-error__text">
                    @error('name')
                    {{ $message }}
                    @enderror
                </span>
            </div>

            {{-- ブランド名 --}}
            <div class="c-input u-mb-40">
                <label class="c-input__label">ブランド名</label>
                <input
                    type="text"
                    name="brand"
                    value="{{ old('brand') }}"
                    class="c-input__field c-input--sm">
            </div>

            {{-- 商品説明 --}}
            <div class="c-input">
                <label class="c-input__label">商品の説明</label>
                <textarea
                    name="description"
                    class="c-input__field c-input--description">{{ old('description') }}</textarea>
            </div>

            <div class="c-error">
                <span class="c-error__text">
                    @error('description')
                    {{ $message }}
                    @enderror
                </span>
            </div>

            {{-- 販売価格 --}}
            <div class="c-input c-input--yen">
                <label class="c-input__label">販売価格</label>
                <input
                    type="text"
                    name="price"
                    id="priceInput"
                    value="{{ old('price') }}"
                    class="c-input__field c-input--price c-input--sm">
            </div>

            <div class="c-error u-mb-80">
                <span class="c-error__text">
                    @error('price')
                    {{ $message }}
                    @enderror
                </span>
            </div>

            <div class="c-button__wrapper">
                <button type="submit" class="c-button c-button--sm c-button--primary">
                    出品する
                </button>
            </div>
        </section>

        </form>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/select-ui-control.js') }}"></script>
    <script src="{{ asset('js/image-preview.js') }}"></script>
    <script src="{{ asset('js/price-input-format.js') }}"></script>
@endsection