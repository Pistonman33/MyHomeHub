@extends('backend.layouts.html')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="h4 mb-1">Analyse des logs</h2>
                <p class="text-muted mb-0">Fichier : {{ $logFile }}</p>
            </div>
            <div class="btn-group" role="group">
                <a href="{{ route('admin.logs.download') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-download"></i> Télécharger
                </a>
                <form action="{{ route('admin.logs.clear') }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Vider le fichier de log ?');">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="fa-solid fa-broom"></i> Vider
                    </button>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Total</h6>
                        <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Erreurs</h6>
                        <h3 class="mb-0">{{ $stats['by_level']['error'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Warnings</h6>
                        <h3 class="mb-0">{{ $stats['by_level']['warning'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Info</h6>
                        <h3 class="mb-0">{{ $stats['by_level']['info'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.logs.index') }}" class="form-inline">
                    <div class="form-group mr-3 mb-2">
                        <label for="level" class="mr-2">Niveau</label>
                        <select name="level" id="level" class="form-control">
                            <option value="all" {{ $level === 'all' ? 'selected' : '' }}>Tous</option>
                            @foreach ($levels as $key => $config)
                                <option value="{{ $key }}" {{ $level === $key ? 'selected' : '' }}>
                                    {{ ucfirst($key) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-3 mb-2">
                        <label for="search" class="mr-2">Recherche</label>
                        <input type="text" name="search" id="search" class="form-control"
                            value="{{ $search }}" placeholder="Message ou canal">
                    </div>
                    <div class="form-group mr-3 mb-2">
                        <label for="perPage" class="mr-2">Par page</label>
                        <select name="perPage" id="perPage" class="form-control">
                            @foreach ([25, 50, 100] as $value)
                                <option value="{{ $value }}" {{ $perPage == $value ? 'selected' : '' }}>
                                    {{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary mb-2">Filtrer</button>
                </form>
            </div>
        </div>

        @if (count($logs) > 0)
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Niveau</th>
                                    <th>Canal</th>
                                    <th>Message</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($logs as $log)
                                    <tr>
                                        <td class="text-nowrap">{{ $log['date'] }}</td>
                                        <td>
                                            <span class="badge badge-{{ $levels[$log['level']]['color'] ?? 'secondary' }}">
                                                <i
                                                    class="fa-solid fa-{{ $levels[$log['level']]['icon'] ?? 'circle-info' }}"></i>
                                                {{ strtoupper($log['level']) }}
                                            </span>
                                        </td>
                                        <td>{{ $log['channel'] }}</td>
                                        <td>
                                            <div>{{ $log['message'] }}</div>
                                            @if (!empty($log['extra']))
                                                <small
                                                    class="text-muted d-block mt-1">{{ str_replace("\n", ' ', $log['extra']) }}</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-3 d-flex justify-content-center">
                {{ $logs->appends(request()->query())->links() }}
            </div>
        @else
            <div class="alert alert-info mb-0">Aucun log trouvé pour cette sélection.</div>
        @endif
    </div>
@endsection
