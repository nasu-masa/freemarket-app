<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/utility.css') }}">
    @yield('css')
    <title>coachtechフリマ</title>
</head>

<body class="l-body">

    <header class="l-header">
        <div class="l-header__container">
            <h1 class="l-header__inner u-m-0">
                <a href="/" class="l-header__logo-link">
                    <img src="{{ asset('assets/COACHTECHヘッダーロゴ.png') }}"
                        alt="COACHTECH"
                        class="l-header__logo">
                </a>
            </h1>

            <div class="c-search">
                <form action="{{ route('items.index') }}" method="get" class="c-search-form">
                    <input type="text"
                        name="keyword"
                        class="c-search-input"
                        value="{{ $keyword ?? '' }}"
                        placeholder="なにをお探しですか？">
                </form>
            </div>

            <nav class="c-nav">

                <ul class="c-nav__list">

                    {{-- 未ログイン時（ゲスト） --}}
                    @guest
                    <li class="c-nav__item">
                        <a href="/login" class="c-nav__link">
                            ログイン
                        </a>
                    </li>
                    @endguest

                    {{-- ログイン時 --}}
                    @auth
                    <li class="c-nav__item">
                        <form action="/logout" method="POST">
                            @csrf
                            <button class="c-nav__link">
                                ログアウト
                            </button>
                        </form>
                    </li>
                    @endauth

                    <li class="c-nav__item">
                        <a href="/mypage" class="c-nav__link">
                            マイページ
                        </a>
                    </li>

                    <li class="c-nav__item">
                        <a href="/sell" class="c-nav__link--button">
                            出品
                        </a>
                    </li>

                </ul>

            </nav>
        </div>
    </header>


    @if (session('success'))
    <div class="c-flash">
        <span class="c-flash__inner c-flash--success">
            {{ session('success') }}
        </span>
    </div>
    @endif

    @if (session('error'))
    <div class="c-flash">
        <span class="c-flash__inner c-flash--error">
            {{ session('error') }}
        </span>
    </div>
    @endif

    <main class="l-main">
        @yield('content')
    </main>

    @yield('scripts')

    <script src="{{ asset('js/flash.js') }}"></script>

</body>

</html>