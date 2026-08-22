<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Jurnal Sekolah')</title>
    <link rel="stylesheet" href="{{ asset('css/jurnal.css') }}">
</head>
<body>
<div class="login-page">
    @yield('content')
</div>
</body>
</html>
