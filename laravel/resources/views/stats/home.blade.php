@extends('layouts.html')
@section('content')
<section>
  <div class="form-group row">
      <div class="col-12" align="center">
        @foreach ($all_category as $category)
              <a href="{{ route('stats.show',$category->id) }}" class="btn" style="background-color:{{ $category->getColor() }};color:white;font-size:14px;margin-top:3px;">{{ $category->nom }}</a>
        @endforeach
      </div>
  </div>
</section>
@endsection
