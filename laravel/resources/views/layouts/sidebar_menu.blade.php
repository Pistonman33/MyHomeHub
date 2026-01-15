<div class="sidebar-sticky">
  <ul class="nav flex-column">
    <li class="nav-item">
      <a class="nav-link active" href="{{ route('home') }}">
        <span data-feather="home"></span>
        Dashboard <span class="sr-only">(current)</span>
      </a>
    </li>
	</ul>
	<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
		<span>Working Process</span>
		<a class="d-flex align-items-center text-muted" href="#">
			<span data-feather="plus-circle"></span>
		</a>
	</h6>
	<ul class="nav flex-column mb-2">
		<li class="nav-item">
			<a class="nav-link" href="{{ route('stats') }}">
				<i class="fas fa-signal"></i>
				Stats info
			</a>
		</li>
	</ul>
	<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
		<span>MyFinance</span>
		<a class="d-flex align-items-center text-muted" href="#">
			<span data-feather="plus-circle"></span>
		</a>
	</h6>
	<ul class="nav flex-column mb-2">
		<li class="nav-item">
			<a class="nav-link" href="{{ route('finance') }}">
				<i class="fas fa-money-check"></i>
				Display transactions
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="{{ route('finance.all') }}">
				<i class="fas fa-exchange-alt"></i>
				All transactions
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="{{ route('finance.show') }}">
				<i class="fas fa-marker"></i>
				Update transactions
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="{{ route('finance.import') }}">
				<i class="fas fa-file-import"></i>
				Import transactions
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="{{ route('charge') }}">
				<i class="fas fa-charging-station"></i>
				Charge Info
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="{{ route('charge.save') }}">
				<i class="fas fa-save"></i>
				Save Charge
			</a>
		</li>
  </ul>
	<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
		<span>Movies</span>
		<a class="d-flex align-items-center text-muted" href="#">
			<span data-feather="plus-circle"></span>
		</a>
	</h6>
	<ul class="nav flex-column mb-2">
		<li class="nav-item">
			<a class="nav-link" href="{{ route('movies') }}">
				<i class="fas fa-film"></i>
				Movies
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="{{ route('movies.all') }}">
				<i class="fas fa-film"></i>
				All Movies
        <span class="badge badge-info">{{App\Movie::getCountAllMovie()}}</span>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="{{ route('movies.pending') }}">
				<i class="fas fa-film"></i>
				Pending Movie(s)
        <span class="badge badge-danger">{{App\Movie::getCountMovieNotInfo()}}</span>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="{{ route('tvshows') }}">
				<i class="fas fa-tv"></i>
				TVShows
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="{{ route('tvshows.pending') }}">
				<i class="fas fa-film"></i>
				Pending Series(s)
        <span class="badge badge-danger">{{App\Serie::getCountSerieNotInfo()}}</span>
			</a>
		</li>
  </ul>
	<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
		<span>Library</span>
		<a class="d-flex align-items-center text-muted" href="#">
			<span data-feather="plus-circle"></span>
		</a>
	</h6>
	<ul class="nav flex-column mb-2">
		<li class="nav-item">
			<a class="nav-link" href="{{ route('library.scan') }}">
				<i class="fas fa-barcode"></i>
				Scan barcodes
			</a>
		</li>
  </ul>
	<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
		<span>Management</span>
		<a class="d-flex align-items-center text-muted" href="#">
			<span data-feather="plus-circle"></span>
		</a>
	</h6>
	<ul class="nav flex-column mb-2">
		<li class="nav-item">
			<a class="nav-link" href="{{ route('backup') }}">
				<i class="far fa-file-archive"></i>
				Backup manager
			</a>
		</li>
  </ul>
</div>
