@extends('backend.layouts.html')
@section('content')
    <section class="finance-all-page">
        <div class="finance-all-header">
            <div>
                <span class="finance-eyebrow">MyFinance</span>
                <h1>Toutes les transactions</h1>
                <p>Retrouvez, filtrez et corrigez rapidement vos mouvements.</p>
            </div>
            <a class="finance-all-back" href="{{ route('admin.finance.dashboard') }}"><i
                    class="fa-solid fa-chart-pie"></i><span>Vue d'ensemble</span></a>
        </div>

        <form class="finance-all-toolbar" method="GET" action="{{ route('admin.finance.all') }}">
            <div class="finance-filter-group">
                <label class="finance-filter-label" for="year">Année</label>
                <select id="year" name="year" class="finance-filter-select">
                    @foreach ($years as $year)
                        <option value="{{ $year }}" {{ (int) $selectedYear === (int) $year ? 'selected' : '' }}>
                            {{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <div class="finance-filter-group">
                <label class="finance-filter-label" for="month">Mois</label>
                <select id="month" name="month" class="finance-filter-select">
                    <option value="">Tous les mois</option>
                    @foreach ($months as $month)
                        <option value="{{ $month }}"
                            {{ (string) $selectedMonth === (string) $month ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="finance-filter-group">
                <label class="finance-filter-label" for="category">Catégorie</label>
                <select id="category" name="category" class="finance-filter-select">
                    <option value="">Toutes les catégories</option>
                    @foreach ($all_category as $category)
                        <option value="{{ $category->id }}"
                            {{ (string) $selectedCategory === (string) $category->id ? 'selected' : '' }}>
                            {{ $category->nom }}</option>
                    @endforeach
                </select>
            </div>



            <label class="finance-search" for="search"><i class="fa-solid fa-magnifying-glass"></i>
                <input type="search" id="search" name="search" value="{{ request('search') }}"
                    placeholder="Rechercher une transaction..." autocomplete="off" />
            </label>

            <button type="submit" class="finance-filter-submit">Appliquer</button>
        </form>

        <div class="finance-all-table-card">
            <div class="finance-all-table-heading">
                <div>
                    <h2>Historique</h2>
                    <span>{{ $transactions->total() }} transaction(s)</span>
                </div>
                <div class="finance-all-heading-actions">
                    <button type="button" class="finance-save-all-btn" id="saveAllFinanceChanges"><i
                            class="fa-solid fa-floppy-disk"></i> Sauvegarder</button>
                    <i class="fa-solid fa-arrow-down-wide-short"></i>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table finance-all-table">
                    <thead>
                        <tr>
                            <th>
                                @php
                                    $sortUrl = request()->except(['date_sort', 'page']);
                                    $toggleSort = $selectedSort === 'asc' ? 'desc' : 'asc';
                                    $sortUrl['date_sort'] = $toggleSort;
                                @endphp
                                <a href="{{ route('admin.finance.all', $sortUrl) }}" class="finance-date-sort-header">
                                    Date
                                    <span class="finance-date-sort-icon">{{ $selectedSort === 'asc' ? '↑' : '↓' }}</span>
                                </a>
                            </th>
                            <th>Libellé</th>
                            <th>Catégorie</th>
                            <th>Groupe</th>
                            <th class="text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            @php $category = $transaction->category; @endphp
                            <tr data-record-id="{{ $transaction->id }}" data-libelle="{{ $transaction->libelle }}"
                                data-category-id="{{ $transaction->fk_id_categorie }}" class="finance-row">
                                <td class="td_date">
                                    <div class="all_transaction_date">
                                        <span
                                            class="all_transaction_date_day">{{ \App\Models\Display::day($transaction->date) }}</span>
                                        <span
                                            class="all_transaction_date_month">{{ \App\Models\Display::monthTextPrefixe(date('n', strtotime($transaction->date))) }}</span>
                                        <span
                                            class="all_transaction_date_year">{{ \App\Models\Display::year($transaction->date) }}</span>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="finance-inline-edit" data-field="libelle"
                                        data-record-id="{{ $transaction->id }}" data-value="{{ $transaction->libelle }}">
                                        {{ $transaction->libelle }}
                                    </button>
                                </td>
                                <td>
                                    <button type="button" class="finance-inline-edit finance-category-inline-edit"
                                        data-field="category" data-record-id="{{ $transaction->id }}"
                                        data-category-id="{{ $transaction->fk_id_categorie }}"
                                        data-category-name="{{ $category ? $category->nom : '' }}">
                                        @if ($category)
                                            <span class="finance-category-badge"
                                                style="background-color: {{ \App\Models\Categorie::getColorById($transaction->fk_id_categorie) }};">{{ $category->nom }}</span>
                                        @else
                                            <span class="finance-category-badge" style="background-color: #999;">Sans
                                                catégorie</span>
                                        @endif
                                    </button>
                                </td>
                                <td>
                                    <span
                                        class="finance-group-badge">{{ $category && $category->group ? $category->group : '—' }}</span>
                                </td>
                                <td class="text-right">
                                    <span
                                        class="amount {{ $transaction->retrait ? 'text-danger' : 'text-success' }} finance-amount-tooltip"
                                        data-tooltip="{{ $transaction->compte ? $transaction->compte->nom : 'Compte non renseigné' }}"
                                        aria-label="{{ $transaction->compte ? $transaction->compte->nom : 'Compte non renseigné' }}">
                                        {{ \App\Models\Display::transactionAmount($transaction) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="finance-no-data">Aucune transaction trouvée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($transactions->hasPages())
                <div class="finance-all-pagination">
                    <div class="finance-pagination-controls">
                        {{ $transactions->links() }}
                        <div class="finance-pagination-select-wrap">
                            <select id="per_page_bottom" class="finance-pagination-select"
                                onchange="window.location='{{ route('admin.finance.all') }}?{{ http_build_query(array_merge(request()->except(['per_page', 'page']), ['per_page' => '__VALUE__'])) }}'.replace('__VALUE__', this.value);">
                                @foreach ([5, 10, 20, 50, 100] as $pageSize)
                                    <option value="{{ $pageSize }}"
                                        {{ (int) $perPage === (int) $pageSize ? 'selected' : '' }}>{{ $pageSize }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <script type="application/json" id="finance-categories-json">
        {!! json_encode($all_category->map(function ($category) { return ['id' => $category->id, 'name' => $category->nom, 'group' => $category->group, 'color' => \App\Models\Categorie::getColorById($category->id)]; })->values()->all(), JSON_HEX_APOS | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
    </script>
@endsection
