@extends('layouts.html')
@section('content')
<section>
  @include('layouts.success')
 @include('layouts.error')
  @if($current_info_movie)
  <nav aria-label="Page navigation movies">
    <a href="{{ url('movies/check/valid/poster') }}" class="btn btn-primary info_selection" >Valid All Poster</a>
    <ul class="pagination justify-content-end">
      <li class="page-item {{ $previous_movie !== null ? "" : "disabled" }} ">
        <a class="page-link" href="{{ $previous_movie !== null ? url('admin/movies/check/picture/'.$previous_movie) : "#"  }}" aria-label="Previous">
          <span aria-hidden="true">&laquo;</span>
          <span class="sr-only">Previous</span>
        </a>
      </li>
      <li><button type="button" class="btn btn-info">
        movie(s) <span class="badge badge-light">{{$nb_movies}}</span>
        </button>
      </li>
      <li class="page-item {{ $next_movie !== null ? "" : "disabled" }} ">
        <a class="page-link" href="{{ $next_movie !== null ? url('admin/movies/check/picture/'.$next_movie) : "#" }}" aria-label="Next">
          <span aria-hidden="true">&raquo;</span>
          <span class="sr-only">Next</span>
        </a>
      </li>
    </ul>
  </nav>
  <div class="container">
      <div class="row justify-content-center">
          <div class="col-md-12">
              <div class="card">
                  <div class="card-header alert-info" align="center">
                    @if(isset($current_info_movie->poster))
                    <div class="card" style="width: 18rem;">
                      <img src="{{ url("storage/images/movies/$current_info_movie->poster") }}" class="card-img-top" >
                      <div class="card-body">
                        <h5 class="card-title">{{ $current_info_movie->title }}</h5>
                        <p class="card-text">
                          Résolution:
                          <?php 
                              $filename = url("storage/images/movies/$current_info_movie->poster");
                              $resolution = getimagesize($filename);
                              echo $resolution[0]."x".$resolution[1];
                          ?>    
                        </p>
                      </div>
                    </div>
                    @else
                      <span>{{ $current_info_movie->title }}</span>
                    @endif
                  </div>
   
                  <div class="card-body">
                      <form method="POST" action="{{ route('admin.movies.checkPicture') }}" id="movieUpdateForm">
                          @csrf
                          <input type="hidden" name="type" value="{{$type}}" />
                          <input type="hidden" name="new_img" value="" />
                          <input type="hidden" name="info_movie_id" value="{{$current_info_movie->id}}" />
                          <div class="card-columns">
                            @foreach($infomovies as $movie)  
                            <div class="card" style="width:300px;">
                                <img src="{{ env("THEMOVIEDB_IMG_URL").$movie["poster_path"] }}" class="card-img-top" />
                                <div class="card-body">
                                  <h5 class="card-title">{{ $movie["original_title"] }}</h5>
                                  <p class="card-text">{{ $movie["overview"] }}</p>
                                  <a href="#" class="btn btn-primary info_selection" image="{{ $movie["poster_path"] }}">Select</a>
                                </div>
                            </div>
                            @endforeach
                          </div>
                      </form>
                  </div>
                  <div class="card-footer text-muted">
                    <form method="POST" action="{{ route('admin.movies.checkPicture') }}" id="searchAnotherMovie">
                      @csrf
                    <div class="form-row">
                        <div class="col-8">
                          <input type="text" class="form-control" id="new_title" name="new_movie" value="{{$current_info_movie->title}}">
                        </div>
                        <div class="col-2">
                          <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="isSerie" name="isSerie" value="yes">
                            <label class="form-check-label" for="isSerie">Serie?</label>
                          </div>
                        </div>
                        <div class="col-2">
                          <button type="submit" class="btn btn-primary mb-2">Search</button>
                        </div>
                    </div>
                  </form>
                  </div>
              </div>
          </div>
      </div>
  </div>
  @else
    <div class="alert alert-warning" role="alert">
      No more issue with pictures
    </div>
  @endif
</section>
@endsection
