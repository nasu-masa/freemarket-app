@extends('layouts.app')

@section('content')

<div class="c-card">
    <h2 class="c-card__title u-mt-48 u-mb-48">プロフィール設定</h2>

    <form action="{{ route('mypage.profile.store') }}" method="post" enctype="multipart/form-data">
        @csrf

        <!-- プロフィール画像 -->
        <div class="c-input u-flex u-mb-32">
            <img src="{{ $user->avatar_path ?? asset('assets/default.icon.png') }}"
                alt=""
                id="preview"
                class="c-profile-image">

            <label for="imageInput" class="
                        c-image-button
                        c-image-button--profile
                        c-image-button--overlay
                        c-image-button--md
                        ">
                画像を選択する
                <input type="file"
                    name="avatar"
                    id="imageInput"
                    class="c-image-upload__field c-input--md">
            </label>
        </div>

        <div class="c-error">
            @error('avatar')
            <span class="c-error__text">{{ $message }}</span>
            @enderror
        </div>

        {{-- ユーザー名 --}}
        <div class="c-input u-mb-24">
            <label class="c-input__label">ユーザー名</label>
            <input type="text"
                name="name"
                value="{{ old('name', $user->name) }}"
                class="c-input__field c-input--md">

            <div class="c-error">
                @error('name')
                <span class="c-error__text">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- 郵便番号 --}}
        <div class="c-input">
            <label class="c-input__label">郵便番号</label>
            <input type="text"
                name="postal_code"
                value="{{ old('postal_code', $latestAddress->postal_code ?? '') }}"
                class="c-input__field c-input--md">

            <div class="c-error">
                @error('postal_code')
                <span class="c-error__text">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- 住所 --}}
        <div class="c-input">
            <label class="c-input__label">住所</label>
            <input type="text"
                name="address"
                value="{{ old('address', $latestAddress->address ?? '') }}"
                class="c-input__field c-input--md">

            <div class="c-error">
                @error('address')
                <span class="c-error__text">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- 建物名 --}}
        <div class="c-input">
            <label class="c-input__label">建物名</label>
            <input type="text"
                name="building"
                value="{{ old('building', $latestAddress->building ?? '') }}"
                class="c-input__field c-input--md">

            <div class="c-error">
                @error('building')
                <span class="c-error__text">{{ $message }}</span>
                @enderror
            </div>
        </div>


        <div class="l-button-wrapper u-mt-32">
            <button type="submit"
                class="c-button c-button--md c-button--primary">
                登録する
            </button>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script src="{{ asset('js/image-preview.js') }}"></script>
@endsection