<nav>
    <a href="{{ url('/') }}" class="logo">
        <img src="/apple-touch-icon-57x57-precomposed.png">
    </a>
    <input type="checkbox" class="menu-btn" id="menu-btn" />
    <label class="menu-icon" for="menu-btn">
        <span class="nav-icon"></span>
    </label>
    <ul class="menu">
        <li><a href="{{ url('/movies') }}">Films</a></li>
        <li><a href="{{ route('tvshows') }}">Séries</a></li>
        <li><a href="#">Dessin Animés</a></li>
    </ul>
    <div class="search">
        <input type="text" placeholder="Avengers" id="search">
        <i class="fa fa-search"></i>
    </div>
  </nav>
