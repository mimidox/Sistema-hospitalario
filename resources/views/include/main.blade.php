<!DOCTYPE html>

<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Sistema Hospitalario')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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