@extends('backend.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h2">Règles de Catégorisation</h1>
                <p class="text-muted">Gérez les règles automatiques pour catégoriser les transactions bancaires</p>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('admin.finance.rules.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Nouvelle Règle
                </a>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-check-circle"></i> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-exclamation-circle"></i> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Nom</th>
                            <th>Catégorie</th>
                            <th>Mots-clés</th>
                            <th>Priorité</th>
                            <th>État</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rules as $rule)
                            <tr>
                                <td>
                                    <strong>{{ $rule->name }}</strong>
                                </td>
                                <td>
                                    @if ($rule->category)
                                        <span class="badge"
                                            style="background-color: {{ $rule->category->getColor() }}; color: white;">
                                            {{ $rule->category->nom }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning">Non assignée</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ Str::limit($rule->match_pattern, 50) }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $rule->priority }}</span>
                                </td>
                                <td>
                                    @if ($rule->active)
                                        <span class="badge bg-success">
                                            <i class="fa-solid fa-check"></i> Active
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fa-solid fa-times"></i> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.finance.rules.edit', $rule->id) }}"
                                            class="btn btn-outline-primary" title="Modifier">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.finance.rules.toggle', $rule->id) }}" method="POST"
                                            style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-warning"
                                                title="{{ $rule->active ? 'Désactiver' : 'Activer' }}">
                                                <i class="fa-solid fa-power-off"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.finance.rules.destroy', $rule->id) }}" method="POST"
                                            style="display: inline;"
                                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette règle ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <p class="text-muted mb-0">Aucune règle n'a été créée pour le moment</p>
                                    <small>
                                        <a href="{{ route('admin.finance.rules.create') }}">Créer la première règle</a>
                                    </small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $rules->links() }}
        </div>

        <!-- Info Cards -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total</h5>
                        <p class="card-text display-4">{{ $rules->total() }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Actives</h5>
                        <p class="card-text display-4">{{ \App\Models\Rule::active()->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Inactives</h5>
                        <p class="card-text display-4">{{ \App\Models\Rule::where('active', false)->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Catégories</h5>
                        <p class="card-text display-4">{{ $categories->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
