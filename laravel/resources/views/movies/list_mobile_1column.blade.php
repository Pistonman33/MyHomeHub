<section class="movies-heading">
    <h2>Films</h2>        
</section>
<section id="movies-list">
    @foreach ($movies2020 as $movie)
    <div class="movie-box">
      <div class="movie-img">
          <div class="quality">{{ $movie->type }}</div>
          <img src="{{ url("storage/images/movies/$movie->poster") }}">
          <span class="tag">{{ $movie->genre }}</span>
      </div>
      <div class="movie-txt">            
        <div class="movie-txt-title">            
            <strong>{{ $movie->title }}</strong>
        </div>
        <div class="movie-txt-footer">            
            <p><i class="fa fa-calendar"></i>&nbsp;&nbsp;{{ $movie->duration }}</p>
            <p><i class="fa fa-clock-o"></i>&nbsp;&nbsp;{{ $movie->year }}</p> 
        </div>
      </div>
    </div>
    @endforeach
</section>