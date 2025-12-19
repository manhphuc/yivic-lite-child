<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Yivic CodeMall')</title>

    {{-- If your theme/plugin already enqueues CSS via WP, you can omit CSS tags here --}}
</head>
<body class="yivic-site">
<header class="yivic-site__header">
    <div class="yivic-site__container">
        <a class="yivic-site__brand" href="/">Yivic CodeMall</a>
    </div>
</header>

<main class="yivic-site__main">
    <div class="yivic-site__container">
        @yield('content')
    </div>
</main>

<footer class="yivic-site__footer">
    <div class="yivic-site__container">
        © {{ date('Y') }} Yivic CodeMall
    </div>
</footer>
</body>
</html>
