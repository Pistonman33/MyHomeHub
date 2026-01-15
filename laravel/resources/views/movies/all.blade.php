@extends('layouts.html')
@section('content')
<section>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <div class="btn-group">
              <input type="hidden" name="supportid" id="supportid" value="{{$filter_supportid}}" />
              <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ $filter_support }}
              </button>
              <div class="dropdown-menu">
                @foreach ($all_support as $support)
                    <a class="dropdown-item" href=" {{url('movies/all?support='.$support->id) }}">{{ $support->type }}</a>
                @endforeach
              </div>
          </div>
          <div class="btn-group">
            <input type="hidden" name="genreid" id="genreid" value="{{$filter_genre}}" />
            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              {{ $filter_genre }}
            </button>
            <div class="dropdown-menu">
              @foreach ($all_genre as $genre)
                  <a class="dropdown-item" href=" {{url('movies/all?genre='.$genre['genre']) }}">{{ $genre['genre'] }}</a>
              @endforeach
            </div>
          </div>
          <div class="btn-group">
            <input type="text" class="form-controller" id="search" name="search"></input>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 nopadding">
        <ul class="thumbnails nopadding" id="movies">
        </ul>
      </div>
    </div>
  </div>
</section>
@endsection
