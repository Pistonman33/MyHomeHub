<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('layouts.seo')

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
    @include('layouts.token')
    @include('layouts.analytics')
</head>
<body class="text-center">
  <form method="POST" action="{{ route('login') }}" class="form-signin">
      @csrf
      <img class="mb-4" src="/apple-touch-icon-114x114-precomposed.png" alt="" style="border-radius: 20%;">
      <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
      <label for="email" class="col-sm-4 col-form-label text-md-right sr-only">{{ __('Username') }}</label>
      <input id="email" type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{ old('username') }}" placeholder="Username" required autofocus>
      @if ($errors->has('username'))
          <span class="invalid-feedback">
              <strong>{{ $errors->first('username') }}</strong>
          </span>
      @endif
      <label for="password" class="col-md-4 col-form-label text-md-right sr-only">{{ __('Password') }}</label>
      <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="Password" name="password" required>
      @if ($errors->has('password'))
          <span class="invalid-feedback">
              <strong>{{ $errors->first('password') }}</strong>
          </span>
      @endif
      <div class="checkbox mb-3">
          <label>
              <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('Remember Me') }}
          </label>
      </div>
      <button type="submit" class="btn btn-lg btn-primary btn-block">
          {{ __('Login') }}
      </button>
      <p class="mt-5 mb-3 text-muted">&copy; Thiébault Michaël <?php echo date("Y"); ?></p>
  </form>
</body>
</html>
