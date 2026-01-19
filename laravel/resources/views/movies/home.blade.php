@php
    $type = 'movies';
@endphp
@extends('layouts.movies.html')
@section('content')
@include('movies.showcase')
@include('movies.list_mobile_2columns')
<div class="btns">
  <a class="more_data">More {{ $type }}</a>
</div>            
@endsection
