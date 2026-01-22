@php
    $type = 'series';
@endphp
@extends('frontend.layouts.movies.html')
@section('content')
@include('frontend.movies.showcase')
@include('frontend.movies.list_mobile_2columns')
<div class="btns">
  <a class="more_data">More {{ $type }}</a>
</div>            
@endsection
