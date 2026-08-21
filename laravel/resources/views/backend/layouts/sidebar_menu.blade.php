@php
    $sections = [
        'MyBlog' => [
            ['label' => 'Posts', 'icon' => 'fa-copy', 'route' => 'admin.blog.posts', 'pattern' => 'admin/blog*'],
        ],
        'MyFinance' => [
            [
                'label' => 'Annual dashboard',
                'icon' => 'fa-chart-pie',
                'route' => 'admin.finance.dashboard',
                'pattern' => 'admin/finance/dashboard*',
            ],
            [
                'label' => 'Stats info',
                'icon' => 'fa-signal',
                'route' => 'admin.stats.index',
                'pattern' => 'admin/stats*',
            ],
            [
                'label' => 'Display transactions',
                'icon' => 'fa-money-check',
                'route' => 'admin.finance.index',
                'pattern' => 'admin/finance',
            ],
            [
                'label' => 'All transactions',
                'icon' => 'fa-exchange-alt',
                'route' => 'admin.finance.all',
                'pattern' => 'admin/finance/all*',
            ],
            [
                'label' => 'Update transactions',
                'icon' => 'fa-marker',
                'route' => 'admin.finance.show',
                'pattern' => 'admin/finance/show*',
            ],
            [
                'label' => 'Import transactions',
                'icon' => 'fa-file-import',
                'route' => 'admin.finance.import',
                'pattern' => 'admin/finance/import*',
            ],
            [
                'label' => 'Rules',
                'icon' => 'fa-cogs',
                'route' => 'admin.finance.rules.index',
                'pattern' => 'admin/finance/rules*',
            ],
        ],
        'MyLibrary' => [
            [
                'label' => 'All Movies',
                'icon' => 'fa-film',
                'route' => 'admin.movies.all',
                'pattern' => 'admin/movies/all*',
            ],
            [
                'label' => 'Pending Movie(s)',
                'icon' => 'fa-film',
                'route' => 'admin.movies.pending',
                'pattern' => 'admin/movies/pending*',
            ],
            [
                'label' => 'Pending Series(s)',
                'icon' => 'fa-film',
                'route' => 'admin.tvshows.pending',
                'pattern' => 'admin/tvshows/pending*',
            ],
        ],
        'MyFriends' => [
            [
                'label' => 'Birthdates',
                'icon' => 'fa-cake-candles',
                'route' => 'admin.friends.index',
                'pattern' => 'admin/friends*',
            ],
        ],
        'Management' => [
            [
                'label' => 'Backup manager',
                'icon' => 'fa-file-archive',
                'route' => 'admin.backup.index',
                'pattern' => 'admin/backup*',
            ],
            [
                'label' => 'Log analyzer',
                'icon' => 'fa-file-lines',
                'route' => 'admin.logs.index',
                'pattern' => 'admin/logs*',
            ],
        ],
    ];
@endphp

<div class="sidebar-sticky">
    <a class="sidebar-brand" href="{{ route('admin.dashboard') }}">
        <span class="sidebar-brand__mark"><i class="fa-solid fa-people-roof"></i></span>
        <span class="sidebar-brand__name">Ma Famille</span>
        <small>Tableau de bord</small>
    </a>

    <div class="sidebar-sections">
        @foreach ($sections as $title => $links)
            @php
                $sectionId = 'sidebar-' . \Illuminate\Support\Str::slug($title);
                $sectionActive = collect($links)->contains(fn($link) => Request::is($link['pattern']));
            @endphp
            <section class="sidebar-section">
                <button class="sidebar-section__toggle {{ $sectionActive ? 'is-active' : '' }}" type="button"
                    data-bs-toggle="collapse" data-bs-target="#{{ $sectionId }}"
                    aria-expanded="{{ $sectionActive ? 'true' : 'false' }}" aria-controls="{{ $sectionId }}">
                    <span>{{ $title }}</span><i class="fa-solid fa-chevron-down"></i>
                </button>
                <ul id="{{ $sectionId }}"
                    class="nav flex-column sidebar-section__links collapse {{ $sectionActive ? 'show' : '' }}">
                    @foreach ($links as $link)
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is($link['pattern']) ? 'active' : '' }}"
                                href="{{ route($link['route']) }}">
                                <i class="fa-solid {{ $link['icon'] }}"></i><span>{{ $link['label'] }}</span>
                                @if (($link['route'] ?? '') === 'admin.movies.pending')
                                    <span
                                        class="badge badge-danger">{{ \App\Models\Movie::getCountMovieNotInfo() }}</span>
                                @endif
                                @if (($link['route'] ?? '') === 'admin.tvshows.pending')
                                    <span
                                        class="badge badge-danger">{{ \App\Models\Serie::getCountSerieNotInfo() }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach
    </div>

    <div class="sidebar-account">
        @guest
            <a href="{{ route('admin.login') }}"><i class="fa-solid fa-right-to-bracket"></i><span>Login</span></a>
        @else
            <div class="sidebar-account__user"><span class="sidebar-account__avatar"><i
                        class="fa-solid fa-user"></i></span><span>{{ Auth::user()->name }}</span></div>
            <a href="{{ route('admin.register') }}"><i class="fa-solid fa-user-plus"></i><span>Register</span></a>
            <a href="{{ route('admin.logout') }}"
                onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();"><i
                    class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a>
            <form id="sidebar-logout-form" action="{{ route('admin.logout') }}" method="POST" hidden>@csrf</form>
        @endguest
    </div>
</div>
