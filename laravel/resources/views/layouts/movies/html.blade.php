<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>
    @include('layouts.movies.css')
    @include('layouts.movies.js')
</head>
<body>
  @include('layouts.movies.nav')
  @yield('content')
  @include('layouts.movies.footer')
  @include('layouts.movies.js_footer')
</body>
</html>
