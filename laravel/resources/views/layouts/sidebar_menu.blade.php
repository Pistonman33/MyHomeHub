<div class="sidebar-sticky">
  <ul class="nav flex-column">
    <li class="nav-item">
      <a class="nav-link active" href="{{ route('admin.dashboard') }}">
        <span data-feather="home"></span>
        Dashboard <span class="sr-only">(current)</span>
      </a>
    </li>
	</ul>
	<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
		<span>MyBlog</span>
		<a class="d-flex align-items-center text-muted" href="#">
			<span data-feather="plus-circle"></span>
		</a>
	</h6>
	<ul class="nav flex-column mb-2">
		<li class="nav-item">
			<a class="nav-link" href="{{ route('admin.blog.posts') }}">
				<i class="fa-solid fa-copy"></i>
				Posts
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
			<a class="nav-link" href="{{ route('admin.stats.index') }}">
				<i class="fa-solid fa-signal"></i>
				Stats info
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="{{ route('admin.finance.index') }}">
				<i class="fa-solid fa-money-check"></i>
				Display transactions
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="{{ route('admin.finance.all') }}">
				<i class="fa-solid fa-exchange-alt"></i>
				All transactions
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="{{ route('admin.finance.show') }}">
				<i class="fa-solid fa-marker"></i>
				Update transactions
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="{{ route('admin.finance.import') }}">
				<i class="fa-solid fa-file-import"></i>
				Import transactions
			</a>
		</li>
  </ul>
	<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
		<span>MyLibrary</span>
		<a class="d-flex align-items-center text-muted" href="#">
			<span data-feather="plus-circle"></span>
		</a>
	</h6>
	<ul class="nav flex-column mb-2">
		<li class="nav-item">
			<a class="nav-link" href="{{ route('admin.movies.all') }}">
				<i class="fa-solid fa-film"></i>
				All Movies
        <span class="badge badge-info">{{App\Models\Movie::getCountAllMovie()}}</span>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="{{ route('admin.movies.pending') }}">
				<i class="fa-solid fa-film"></i>
				Pending Movie(s)
        <span class="badge badge-danger">{{App\Models\Movie::getCountMovieNotInfo()}}</span>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="{{ route('admin.tvshows.pending') }}">
				<i class="fa-solid fa-film"></i>
				Pending Series(s)
        <span class="badge badge-danger">{{App\Models\Serie::getCountSerieNotInfo()}}</span>
			</a>
		</li>
	</ul>
		<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
		<span>MyFriends</span>
		<a class="d-flex align-items-center text-muted" href="#">
			<span data-feather="plus-circle"></span>
		</a>
	</h6>
	<ul class="nav flex-column mb-2">
		<li class="nav-item">
			<a class="nav-link" href="{{ route('admin.friends.index') }}">
				<i class="fa-solid fa-cake-candles"></i>
				Birthdates
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
			<a class="nav-link" href="{{ route('admin.backup.index') }}">
				<i class="far fa-file-archive"></i>
				Backup manager
			</a>
		</li>
  </ul>
</div>
