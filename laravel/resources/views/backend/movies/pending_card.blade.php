<div class="card">
  <img src="{{ $movie->picture }}" class="card-img-top" />
  <div class="card-body">
    <h5 class="card-title">{{ $movie->title }} </h5>
    <p class="card-text">{{ $movie->description }}</p>
    <a href="#" class="btn btn-primary info_selection" infomovie_id="{{ $movie->infomovie_id }}" movie_id="{{ $movie->movie_id }}">Select</a>
  </div>
</div>
