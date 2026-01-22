@extends('backend.layouts.html')
@section('content')
<section>
  @include('backend.layouts.success')
	@include('backend.layouts.error')
  @if($current_movie)
  <nav aria-label="Page navigation movies">
    <ul class="pagination justify-content-end">
      <li class="page-item {{ $previous_movie !== null ? "" : "disabled" }} ">
        <a class="page-link" href="{{ $previous_movie !== null ? url($type.'/pending/'.$previous_movie) : "#"  }}" aria-label="Previous">
          <span aria-hidden="true">&laquo;</span>
          <span class="sr-only">Previous</span>
        </a>
      </li>
      <li><button type="button" class="btn btn-info">
        movie(s) <span class="badge badge-light">{{$nb_movies}}</span>
        </button>
      </li>
      <li class="page-item {{ $next_movie !== null ? "" : "disabled" }} ">
        <a class="page-link" href="{{ $next_movie !== null ? url($type.'/pending/'.$next_movie) : "#" }}" aria-label="Next">
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
                      <span>
                      {{ $current_movie->title }}
                      </span>
                      <span class="pull-right badge badge-light">
                      <?php echo $support; ?>
                      </span>
                  </div>

                  <div class="card-body">
                      <form method="POST" action="{{ route($type.'.pending') }}" id="movieUpdateForm">
                          @csrf
                          <input type="hidden" name="infomovie_id" value="" />
                          <input type="hidden" name="movie_id" value="" />
                          <div class="card-columns">
                          @each('movies.pending_card', $infomovies, 'movie', 'movies.no-pending_card')
                          </div>
                          <div class="form-group row mb-0">
                              <div class="col-md-6 offset-md-4">
                                <a class="btn btn-xs btn-danger" data-button-type="delete"
                                   href="{{ url($type.'/delete/'.$current_movie->id) }}"><i class="fa fa-trash-o"></i>
                                    Delete</a>
                              </div>
                          </div>

                      </form>
                  </div>
                  <div class="card-footer text-muted">
                    <form method="POST" action="{{ route($type.'.pending') }}" id="searchAnotherMovie">
                      @csrf
                    <div class="form-row">
                        <div class="col-8">
                          <input type="text" class="form-control" id="new_title" name="new_movie" value="{{$current_movie->title}}">
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
      No new movie to update from the Synology.
    </div>
  @endif
</section>
@endsection
