<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('layouts.seo')

    <title>{{ config('app.name', 'Laravel') }}</title>
    @include('layouts.css')
    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    @include('layouts.token')

    @include('layouts.analytics')
</head>
<body>
  @include('layouts.top_bar')
  <div class="ajaxloader">&nbsp;</div>
  <div class="container-fluid">
    <div class="row">
      <nav class="col-md-2 d-none d-md-block bg-light sidebar">
        @include('layouts.sidebar_menu')
      </nav>
      <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
        @yield('content')
      </main>
    </div>
  </div>
  <footer>
    <h2>&copy; Thiébault Michaël <?php echo date("Y"); ?></h2>
  </footer>
    @include('layouts.js')
</body>
</html>
