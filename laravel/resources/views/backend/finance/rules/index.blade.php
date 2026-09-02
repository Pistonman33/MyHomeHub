@php
    use Illuminate\Support\Str;
@endphp

@extends('backend.layouts.app')

@section('content')
    <div class="container-fluid admin-page admin-rules-page">
        <div class="row mb-4">
            <div class="col-md-8">
                <span class="admin-eyebrow">MyFinance</span>
                <h1 class="admin-page-title">Categorization Rules</h1>
                <p class="text-muted">Manage automatic rules for categorizing bank transactions</p>
            </div>
            <div class="col-md-4 text-right admin-page-action">
                <a href="{{ route('admin.finance.rules.create') }}" class="admin-primary-action">
                    <i class="fa-solid fa-plus"></i> New Rule
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

        <div class="card admin-panel">
            <div class="card-body border-bottom">
                <form method="GET" action="{{ route('admin.finance.rules.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label for="category" class="form-label small text-muted mb-1">Filter by category</label>
                        <select name="category" id="category" class="form-control form-control-sm">
                            <option value="">All categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ (string) $selectedCategoryId === (string) $category->id ? 'selected' : '' }}>
                                    {{ $category->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="finance-filter-submit"><i class="fa-solid fa-filter"></i>
                            Filter</button>
                    </div>
                    @if ($selectedCategoryId)
                        <div class="col-md-2">
                            <a href="{{ route('admin.finance.rules.index') }}" class="admin-secondary-action">View all</a>
                        </div>
                    @endif
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0 rules-table">
                    <thead class="bg-light">
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Keywords</th>
                            <th>Status</th>
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
                                        <span class="badge bg-warning">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ Str::limit($rule->match_pattern, 50) }}
                                    </small>
                                </td>
                                <td>
                                    <form action="{{ route('admin.finance.rules.toggle', $rule->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="admin-rule-switch {{ $rule->active ? 'is-active' : '' }}"
                                            title="{{ $rule->active ? 'Disable' : 'Enable' }}"
                                            aria-label="{{ $rule->active ? 'Disable' : 'Enable' }}">
                                            <span></span>
                                        </button>
                                    </form>
                                </td>
                                <td class="text-right">
                                    <div class="btn-group btn-group-sm rule-action-group" role="group">
                                        <a href="{{ route('admin.finance.rules.edit', $rule->id) }}"
                                            class="admin-icon-action admin-icon-action--edit" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <form action="{{ route('admin.finance.rules.destroy', $rule->id) }}" method="POST"
                                            style="display: inline;"
                                            onsubmit="return confirm('Are you sure you want to delete this rule?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-icon-action admin-icon-action--delete"
                                                title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <p class="text-muted mb-0">No rules have been created yet</p>
                                    <small>
                                        <a href="{{ route('admin.finance.rules.create') }}">Create the first rule</a>
                                    </small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-pagination mt-4">
            <div class="admin-pagination-controls">
                {{ $rules->links() }}
                <select class="admin-pagination-select" aria-label="Nombre de règles par page"
                    onchange="window.location='{{ route('admin.finance.rules.index') }}?{{ http_build_query(array_merge(request()->except(['per_page', 'page']), ['per_page' => '__VALUE__'])) }}'.replace('__VALUE__', this.value);">
                    @foreach ([5, 10, 20, 50, 100] as $pageSize)
                        <option value="{{ $pageSize }}" @selected((int) $perPage === $pageSize)>{{ $pageSize }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Info Cards -->
        <div class="row mt-4 admin-summary-cards">
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
