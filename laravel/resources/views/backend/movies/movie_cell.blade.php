@if(isset($result))
  @foreach ($result as $movie)
    <li><img src="{{ url("storage/images/movies/$movie->poster") }}" class="img-thumbnail"/></li>
  @endforeach
@endif
