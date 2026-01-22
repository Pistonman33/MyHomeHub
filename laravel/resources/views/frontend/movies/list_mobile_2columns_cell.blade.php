@if(isset($movies))
    @if(sizeof($movies)==0)
        <p>No {{$type}} found</p>
    @else
        @foreach ($movies as $movie)
            @if(sizeof($movies)<=9)
            <div class="movie-box">
                <div class="movie-img">
                    <div class="quality">{{ $movie->type }}</div>
                    @if($type === 'series')
                    <a data-toggle="modal" data-target="#modal-{{ $movie->fk_id_serie_info; }}">
                    @else
                    <a data-toggle="modal" data-target="#modal-{{ $movie->fk_id_movie_info; }}">
                    @endif
                    <img src="{{ url("storage/images/$type/$movie->poster") }}">
                    </a>
                    <span class="tag">{{ $movie->genre }}</span>
                </div>
                <div class="movie-txt">            
                  <div class="movie-txt-title">            
                      <strong>{{ $movie->title }} @if($type === 'series') ({{$movie->year}}) @endif</strong>
                  </div>
                  <div class="movie-txt-footer">
                    @if($type == 'series')                          
                        <p><i class="fa fa-film"></i>&nbsp;&nbsp;{{ $movie->episodeCount }} ({{ $movie->seasonCount }})</p> 
                        <p><i class="fa fa-clock-o"></i>&nbsp;&nbsp;{{ $movie->yearStart }} to {{ $movie->yearEnd }}</p> 
                    @else
                      <p><i class="fa fa-calendar"></i>&nbsp;&nbsp;{{ $movie->duration }}</p>
                      <p><i class="fa fa-clock-o"></i>&nbsp;&nbsp;{{ $movie->year }}</p> 
                    @endif
                  </div>
                </div>
            </div>                            
            @else
            <div class="movie-box-2cols">
                <div class="movie-img-2cols">
                    <div class="quality-2cols">{{ $movie->type }}</div>
                    @if($type === 'series')
                    <a data-toggle="modal" data-target="#modal-{{ $movie->fk_id_serie_info; }}">
                    @else
                    <a data-toggle="modal" data-target="#modal-{{ $movie->fk_id_movie_info; }}">
                    @endif
                    <img src="{{ url("storage/images/$type/$movie->poster") }}">
                    </a>
                    <span class="tag-2cols">{{ $movie->genre }}</span>
                </div>
                <div class="movie-txt-2cols">            
                    <div class="movie-txt-title-2cols">            
                        <strong>{{ $movie->title }} @if($type === 'series') ({{$movie->year}}) @endif</strong>
                    </div>
                    <div class="movie-txt-footer-2cols">            
                        @if($type == 'series')                          
                        <p><i class="fa fa-film"></i>&nbsp;&nbsp;{{ $movie->episodeCount }} ({{ $movie->seasonCount }})</p> 
                        <p><i class="fa fa-clock-o"></i>&nbsp;&nbsp;{{ $movie->yearStart }} to {{ $movie->yearEnd }}</p> 
                        @else
                        <p><i class="fa fa-calendar"></i>&nbsp;&nbsp;{{ $movie->duration }}</p>
                        <p><i class="fa fa-clock-o"></i>&nbsp;&nbsp;{{ $movie->year }}</p> 
                        @endif
                    </div>
                </div>
            </div>                        
            @endif
            <!--- Modal --->
            @if($type == 'series')                          
            <div class="modal" id="modal-{{ $movie->fk_id_serie_info}}">
            @else
            <div class="modal" id="modal-{{ $movie->fk_id_movie_info}}">
            @endif
                <div class="modal-dialog">
                    <div class="card">
                        <img src="{{ url("storage/images/$type/$movie->poster") }}" class="responsive">
                        <div class="card-body">
                          <p class="card-text">{{ $movie->synopsis }}</p>
                        </div>
                        <div class="card-footer text-muted">
                            @if(strlen(trim($movie->location)) > 2 )
                            <?php 
                                $items = explode("/",$movie->location);
                                $file = array_pop($items);
                                $path = implode("/",$items);
                            ?>
                            <p style="color:#3a3a3a">
                            @if($type == 'series')     
                            <strong>Season(s):</strong> {{ $movie->seasonCount }}<br/>
                            <strong>Episode(s):</strong> {{ $movie->episodeCount }}<br/>
                            @else
                            <strong>Path:</strong> {{ $path }}<br/>
                            <strong>File:</strong> <em>{{ $file }}</em>
                            @endif
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
              </div>            
        @endforeach
    @endif
@endif
