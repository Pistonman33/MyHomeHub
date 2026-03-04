<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>My Media Library</title>
    @include('frontend.layouts.movies.css')
    @include('frontend.layouts.movies.js')
</head>

<body>
    @include('frontend.layouts.movies.nav')
    @yield('content')
    @include('frontend.layouts.movies.footer')
    @include('frontend.layouts.movies.js_footer')
</body>

</html>
