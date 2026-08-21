<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('backend.layouts.seo')

    <title>{{ config('app.name', 'Laravel') }}</title>
    @include('backend.layouts.css')
    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @include('backend.layouts.token')

    @include('backend.layouts.analytics')
</head>

<body>
    <div class="ajaxloader">&nbsp;</div>
    <div class="container-fluid">
        <div class="row">
            <nav class="sidebar">
                @include('backend.layouts.sidebar_menu')
            </nav>
            <main role="main" class="admin-main">
                @yield('content')
            </main>
        </div>
    </div>
    <footer>
        <h2>&copy; Thiébault Michaël <?php echo date('Y'); ?></h2>
    </footer>
    @include('backend.layouts.js')
</body>

</html>
