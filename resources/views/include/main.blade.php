<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Sistema Hospitalario')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @include('include.navbar')
    @include('include.header')

    <main class="py-4">
        @yield('content')
    </main>

    @include('include.footer')
</body>
</html>