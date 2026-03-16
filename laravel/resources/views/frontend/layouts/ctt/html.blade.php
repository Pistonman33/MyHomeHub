<!doctype html>
<html lang="{{ app()->getLocale() }}" x-data="darkModeData()" :class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />

    <title>Thiébault Michaël Pongiste</title>

    @include('frontend.layouts.ctt.tailwindcss')
</head>

<body>
    @yield('content')
</body>

</html>
