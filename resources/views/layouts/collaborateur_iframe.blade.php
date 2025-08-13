<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    @yield('styles')
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body style="background: white;">
    @yield('content')
    @yield('scripts')
</body>
</html>
