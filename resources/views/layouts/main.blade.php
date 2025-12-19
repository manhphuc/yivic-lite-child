<!DOCTYPE html>
<html {{ language_attributes() }}>
<head>
    <meta charset="{{ bloginfo('charset') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', get_bloginfo('name'))</title>

    @wp_head
</head>
<body {{ body_class() }}>

@include('partials.header')

<main id="content" class="site-content">
    @yield('content')
</main>

@include('partials.footer')

@wp_footer
</body>
</html>
