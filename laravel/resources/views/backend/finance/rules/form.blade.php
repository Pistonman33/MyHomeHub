@extends('backend.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="h2">
                    @if ($rule)
                        Modifier la Règle: {{ $rule->name }}
                    @else
                        Créer une Nouvelle Règle
                    @endif
                </h1>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h4 class="alert-heading">Erreurs de Validation</h4>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informations de la Règle</h5>
                    </div>
                    <div class="card-body">
                        <form
                            action="{{ $rule ? route('admin.finance.rules.update', $rule->id) : route('admin.finance.rules.store') }}"
                            method="POST">
                            @csrf
                            @if ($rule)
                                @method('PUT')
                            @endif

                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    Nom de la Règle <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $rule?->name) }}"
                                    placeholder="ex: Salaire EASI" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Un nom descriptif pour identifier la règle</small>
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">
                                    Catégorie <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                    name="category_id" required>
                                    <option value="">-- Sélectionner une catégorie --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            @if (old('category_id', $rule?->category_id) == $category->id) selected @endif
                                            style="background-color: {{ $category->getColor() }}; color: white;">
                                            {{ $category->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">La catégorie à laquelle sera assignée la
                                    transaction</small>
                            </div>

                            <div class="mb-3">
                                <label for="match_pattern" class="form-label">
                                    Motif de Correspondance <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('match_pattern') is-invalid @enderror" id="match_pattern" name="match_pattern"
                                    rows="4" placeholder="Mots-clés séparés par |&#10;ex: SHELL|DATS 24|ESSO" required>{{ old('match_pattern', $rule?->match_pattern) }}</textarea>
                                @error('match_pattern')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Entrez les mots-clés à rechercher dans la description de la transaction,
                                    séparés par des caractères | (pipe).
                                    La recherche est insensible à la casse.
                                </small>
                            </div>

                            <div class="mb-3">
                                <label for="libelle_template" class="form-label">
                                    Modèle de Libellé <span class="badge bg-info">Optionnel</span>
                                </label>
                                <input type="text" class="form-control @error('libelle_template') is-invalid @enderror"
                                    id="libelle_template" name="libelle_template"
                                    value="{{ old('libelle_template', $rule?->libelle_template) }}"
                                    placeholder="ex: Carburant {month}-{year}">
                                @error('libelle_template')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Variables disponibles: {month}, {month_text}, {year}
                                </small>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="priority" class="form-label">
                                            Priorité <span class="badge bg-info">Optionnel</span>
                                        </label>
                                        <input type="number" class="form-control @error('priority') is-invalid @enderror"
                                            id="priority" name="priority"
                                            value="{{ old('priority', $rule?->priority ?? 100) }}" min="0"
                                            max="1000">
                                        @error('priority')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Plus faible = plus prioritaire (défaut: 100)
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox" id="active" name="active"
                                                value="1" @if (old('active', $rule?->active ?? true)) checked @endif>
                                            <label class="form-check-label" for="active">
                                                Règle Active
                                            </label>
                                            <small class="form-text text-muted d-block">
                                                Les règles inactives ne seront pas appliquées
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-sm-flex">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fa-solid fa-save"></i>
                                    @if ($rule)
                                        Mettre à jour
                                    @else
                                        Créer la Règle
                                    @endif
                                </button>
                                <a href="{{ route('admin.finance.rules.index') }}" class="btn btn-secondary btn-lg">
                                    <i class="fa-solid fa-times"></i> Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fa-solid fa-info-circle"></i> Aide
                        </h5>
                    </div>
                    <div class="card-body">
                        <h6>Exemple de Règle</h6>
                        <div class="bg-white p-2 rounded mb-3" style="font-size: 0.9rem;">
                            <strong>Nom:</strong> Carburant<br>
                            <strong>Catégorie:</strong> Carburant<br>
                            <strong>Motif:</strong> SHELL|DATS 24|ESSO<br>
                            <strong>Libellé:</strong> Carburant {month}-{year}<br>
                            <strong>Priorité:</strong> 15<br>
                        </div>

                        <h6>À Propos des Motifs</h6>
                        <ul class="small">
                            <li>Utilisez le caractère <code>|</code> pour séparer les mots-clés</li>
                            <li>La recherche est <strong>insensible à la casse</strong></li>
                            <li>Les espaces sont importants: « SHELL » ne trouvera pas « SHELL123 »</li>
                            <li>Utilisez des mots-clés spécifiques pour éviter les faux positifs</li>
                        </ul>

                        <h6 class="mt-3">À Propos de la Priorité</h6>
                        <ul class="small">
                            <li>Les règles sont appliquées par ordre de priorité croissant</li>
                            <li>Donnez une priorité basse (ex: 10) aux règles génériques</li>
                            <li>Donnez une priorité haute (ex: 100+) aux règles spécifiques</li>
                        </ul>

                        @if ($rule)
                            <hr>
                            <small class="text-muted">
                                <strong>Créée:</strong> {{ $rule->created_at->format('d/m/Y H:i') }}<br>
                                <strong>Mise à jour:</strong> {{ $rule->updated_at->format('d/m/Y H:i') }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
