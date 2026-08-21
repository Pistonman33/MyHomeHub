@extends('backend.layouts.html')
@section('content')
    <section class="finance-all-page">
        <div class="finance-all-header">
            <div>
                <span class="finance-eyebrow">MyFinance</span>
                <h1>Toutes les transactions</h1>
                <p>Retrouvez et filtrez l'ensemble de vos mouvements financiers.</p>
            </div>
            <a class="finance-all-back" href="{{ route('admin.finance.dashboard') }}"><i
                    class="fa-solid fa-chart-pie"></i><span>Vue d'ensemble</span></a>
        </div>

        <div class="finance-all-toolbar">
            <input type="hidden" name="catid" id="catid" value="{{ $filter_categoryid }}" />
            <div class="finance-filter-group">
                <span class="finance-filter-label">Catégorie</span>
                <div class="dropdown">
                    <button type="button" class="finance-filter-select dropdown-toggle" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="fa-solid fa-tag"></i><span>{{ $filter_category }}</span>
                    </button>
                    <div class="dropdown-menu finance-category-menu">
                        @foreach ($all_category as $category)
                            <a class="dropdown-item {{ (int) $filter_categoryid === (int) $category->id ? 'active' : '' }}"
                                href="{{ url('admin/finance/all?category=' . $category->id) }}">{{ $category->nom }}</a>
                        @endforeach
                    </div>
                </div>
            </div>

            <label class="finance-search" for="search"><i class="fa-solid fa-magnifying-glass"></i><input type="search"
                    id="search" name="search" placeholder="Rechercher une transaction..." autocomplete="off" /></label>
            <span class="finance-live-status"><i class="fa-solid fa-circle"></i> Recherche instantanée</span>
        </div>

        <div class="finance-all-table-card">
            <div class="finance-all-table-heading">
                <div>
                    <h2>Historique</h2><span>Les transactions sont chargées automatiquement au fil du défilement.</span>
                </div><i class="fa-solid fa-arrow-down-wide-short"></i>
            </div>
            <div class="table-responsive">
                <table class="table finance-all-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Libellé</th>
                            <th>Catégorie</th>
                            <th class="text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
