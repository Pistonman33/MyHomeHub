<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />

    <title>Thiébault Michaël Pongiste</title>

    @include('frontend.layouts.ctt.tailwindcss')
</head>

<body class="font-[Inter] bg-gray-900 text-gray-100">
    @yield('content')
</body>

</html>
