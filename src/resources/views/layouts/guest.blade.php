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
    <title>フリマアプリ</title>
</head>

<body class="l-body">

    <header class="l-header">
        <div class="l-header__container">
            <h1 class="l-header__inner u-m-0 u-flex">
                <img src="{{ asset('assets/COACHTECHヘッダーロゴ.png') }}"
                    alt="COACHTECH"
                    class="l-header__logo">
            </h1>
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

    <script src="{{ asset('js/flash.js') }}"></script>

</body>

</html>