@extends('backend.layouts.html')

@section('content')
    @php
        $monthNames = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];
        $activeAccountLabel = [
            'common' => 'Compte commun',
            'personal' => 'Mon compte personnel',
            'all' => 'Tous les comptes importés',
        ][$accountFilter];
        $salaryChartMax = max((float) $salaryTimeline->max('amount'), 1);
    @endphp

    <div class="finance-dashboard">
        <header class="finance-dashboard__header">
            <div>
                <h1>Dashboard Finance</h1>
                <p>Analyse des comptes importés — {{ $activeAccountLabel }}</p>
            </div>
        </header>

        <nav class="finance-dashboard-tabs" aria-label="Vues du dashboard financier">
            <a href="{{ route('admin.finance.dashboard', array_merge(request()->except(['view', 'categories']), ['view' => 'menage'])) }}"
                class="{{ $dashboard === 'menage' ? 'is-active' : '' }}">Ménage</a>
            <a href="{{ route('admin.finance.dashboard', array_merge(request()->except(['view', 'categories']), ['view' => 'moi'])) }}"
                class="{{ $dashboard === 'moi' ? 'is-active' : '' }}">Moi</a>
            <a href="{{ route('admin.finance.dashboard', array_merge(request()->except(['view', 'categories']), ['view' => 'evolution'])) }}"
                class="{{ $dashboard === 'evolution' ? 'is-active' : '' }}">Évolution</a>
        </nav>

        <form method="GET" action="{{ route('admin.finance.dashboard') }}" class="finance-dashboard-filters">
            <input type="hidden" name="view" value="{{ $dashboard }}">
            <label>
                Année
                <select name="year" onchange="this.form.submit()">
                    @foreach ($years as $availableYear)
                        <option value="{{ $availableYear }}" @selected((int) $availableYear === $year)>{{ $availableYear }}</option>
                    @endforeach
                    @if ($years->isEmpty())
                        <option value="{{ $year }}" selected>{{ $year }}</option>
                    @endif
                </select>
            </label>
            <label>
                Mois
                <select name="month" onchange="this.form.submit()">
                    <option value="">Toute l'année</option>
                    @foreach ($monthNames as $monthNumber => $monthName)
                        <option value="{{ $monthNumber }}" @selected($month === $monthNumber)>{{ $monthName }}</option>
                    @endforeach
                </select>
            </label>
            @if ($dashboard !== 'moi')
                <label>
                    Comptes
                    <select name="account" onchange="this.form.submit()">
                        <option value="common" @selected($accountFilter === 'common')>Compte commun</option>
                        <option value="personal" @selected($accountFilter === 'personal')>Mon compte personnel</option>
                        <option value="all" @selected($accountFilter === 'all')>Tous les comptes importés</option>
                    </select>
                </label>
            @endif
            @if ($dashboard === 'evolution')
                <label class="finance-category-filter">
                    Catégories de dépenses
                    <select name="categories[]" multiple size="4" aria-label="Catégories de dépenses">
                        @foreach ($expenseCategories as $category)
                            <option value="{{ $category->id }}" @selected($selectedCategoryIds->contains($category->id))>
                                {{ $category->nom }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <button type="submit">Appliquer</button>
            @endif
        </form>

        @if ($dashboard === 'menage' && !$commonAccountConfigured && $accountFilter === 'common')
            <div class="finance-dashboard-notice" role="status">
                Aucun compte nommé « COMPTE COMMUN » n'a été trouvé. Vérifie le nom du compte dans la table des comptes.
            </div>
        @elseif ($dashboard === 'moi' && !$personalAccountConfigured)
            <div class="finance-dashboard-notice" role="status">
                Aucun compte nommé « THIEBAULT MICHAEL » n'a été trouvé. Vérifie le nom du compte dans la table des comptes.
            </div>
        @endif

        @if ($dashboard === 'menage')
            <section class="finance-dashboard-intro">
                <h2>Dépenses du ménage</h2>
                <p>Par défaut, seules les dépenses du compte commun sont affichées. Les provisions et les revenus sont exclus.</p>
                <p class="finance-dashboard-note">Les dépenses et revenus du compte personnel de votre conjointe ne sont pas inclus, car ses transactions ne sont pas importées.</p>
            </section>
            <div class="finance-kpis">
                <article class="finance-kpi finance-kpi--red">
                    <span class="finance-kpi__icon">€</span>
                    <div><small>Dépenses nettes</small><strong>{{ number_format($expenses, 2, ',', ' ') }} €</strong>
                        <em>{{ $month ? $monthNames[$month] . ' ' : '' }}{{ $year }}</em></div>
                </article>
                <article class="finance-kpi finance-kpi--purple">
                    <span class="finance-kpi__icon">#</span>
                    <div><small>Catégories</small><strong>{{ $categories->count() }}</strong>
                        <em>Remboursements déduits de la catégorie correspondante</em></div>
                </article>
                <article class="finance-kpi finance-kpi--blue">
                    <span class="finance-kpi__icon">↔</span>
                    <div><small>Mouvements analysés</small><strong>{{ $transactionCount }}</strong>
                        <em>Transactions validées</em></div>
                </article>
            </div>
            <section class="finance-panel">
                <h2>Dépenses par catégorie</h2>
                @forelse ($categories as $category)
                    <div class="expense-row">
                        <span class="category-dot" style="background: {{ $category['color'] }}"></span>
                        <span>{{ $category['name'] }}
                            @if ($category['refunds'] > 0)
                                <small class="finance-expense-detail">{{ number_format($category['spent'], 2, ',', ' ') }} € dépensés · {{ number_format($category['refunds'], 2, ',', ' ') }} € remboursés</small>
                            @endif
                        </span>
                        <div class="expense-track"><i
                                style="width: {{ min(max(($category['amount'] / max($expenses, 1)) * 100, 0), 100) }}%; background: {{ $category['color'] }}"></i>
                        </div>
                        <b>{{ number_format($category['amount'], 2, ',', ' ') }} €</b>
                    </div>
                @empty
                    <p class="finance-dashboard-empty">Aucune dépense catégorisée pour cette période et ce périmètre.</p>
                @endforelse
                @if ($categories->isNotEmpty())
                    <div class="finance-dashboard-total"><span>Total des dépenses nettes</span><strong>{{ number_format($expenses, 2, ',', ' ') }} €</strong></div>
                @endif
            </section>
        @elseif ($dashboard === 'moi')
            <section class="finance-dashboard-intro">
                <h2>Mon compte personnel</h2>
                <p>Les revenus sont identifiés par la catégorie. Les provisions vers le compte commun sont présentées à part et ne sont ni des revenus ni des dépenses.</p>
                <p class="finance-dashboard-note">Les transactions du compte personnel de votre conjointe ne sont pas importées et ne sont donc pas incluses.</p>
            </section>
            <div class="finance-kpis">
                <article class="finance-kpi finance-kpi--green">
                    <span class="finance-kpi__icon">↗</span>
                    <div><small>Revenus réels</small><strong>{{ number_format($revenues, 2, ',', ' ') }} €</strong>
                        <em>Hors provisions et remboursements</em></div>
                </article>
                <article class="finance-kpi finance-kpi--red">
                    <span class="finance-kpi__icon">↘</span>
                    <div><small>Dépenses nettes</small><strong>{{ number_format($expenses, 2, ',', ' ') }} €</strong>
                        <em>Remboursements déduits par catégorie</em></div>
                </article>
                <article class="finance-kpi finance-kpi--orange">
                    <span class="finance-kpi__icon">→</span>
                    <div><small>Versements au compte commun</small><strong>{{ number_format($provisions, 2, ',', ' ') }} €</strong>
                        <em>Flux sortant, exclu des revenus et dépenses</em></div>
                </article>
            </div>
            <div class="finance-grid finance-grid--bottom">
                <section class="finance-panel">
                    <h2>Revenus par catégorie</h2>
                    <div class="finance-dashboard-line"><span>Salaire</span><strong>{{ number_format($salary, 2, ',', ' ') }} €</strong></div>
                    <div class="finance-dashboard-line"><span>WEBSITE / revenus web</span><strong>{{ number_format($websiteRevenue, 2, ',', ' ') }} €</strong></div>
                    @if ($otherRevenues > 0)
                        <div class="finance-dashboard-line"><span>Autres revenus catégorisés</span><strong>{{ number_format($otherRevenues, 2, ',', ' ') }} €</strong></div>
                    @endif
                    <div class="finance-dashboard-total"><span>Total des revenus réels</span><strong>{{ number_format($revenues, 2, ',', ' ') }} €</strong></div>
                </section>
                <section class="finance-panel">
                    <h2>Dépenses personnelles par catégorie</h2>
                    @forelse ($categories as $category)
                        <div class="finance-dashboard-line">
                            <span><i class="category-dot" style="background: {{ $category['color'] }}"></i>{{ $category['name'] }}
                                @if ($category['refunds'] > 0)
                                    <small class="finance-expense-detail">{{ number_format($category['spent'], 2, ',', ' ') }} € dépensés · {{ number_format($category['refunds'], 2, ',', ' ') }} € remboursés</small>
                                @endif
                            </span>
                            <strong>{{ number_format($category['amount'], 2, ',', ' ') }} €</strong>
                        </div>
                    @empty
                        <p class="finance-dashboard-empty">Aucune dépense catégorisée pour cette période.</p>
                    @endforelse
                </section>
            </div>
        @else
            <section class="finance-dashboard-intro">
                <h2>Évolution des revenus et des dépenses</h2>
                <p>Le salaire utilise exclusivement la catégorie SALAIRE. WEBSITE est suivi à part. Les provisions ne sont jamais intégrées aux revenus.</p>
            </section>
            <section class="finance-panel finance-evolution-panel">
                <h2>Évolution du salaire par mois</h2>
                @if ($salaryTimeline->isNotEmpty())
                    <div class="finance-salary-chart" role="img" aria-label="Évolution du salaire dans le temps">
                        @foreach ($salaryTimeline as $point)
                            <div class="finance-salary-point" title="{{ $point['period'] }} : {{ number_format($point['amount'], 2, ',', ' ') }} €">
                                <span style="height: {{ max(($point['amount'] / $salaryChartMax) * 100, 2) }}%"></span>
                                <small>{{ $point['period'] }}</small>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="finance-dashboard-empty">Aucun revenu de catégorie SALAIRE trouvé pour les comptes sélectionnés.</p>
                @endif
            </section>
            <section class="finance-panel finance-table-panel">
                <h2>Comparaison du salaire par mois et par année</h2>
                <div class="finance-dashboard-table-wrap">
                    <table>
                        <thead><tr><th>Mois</th>@foreach ($historicalYears as $historicalYear)<th>{{ $historicalYear }}</th>@endforeach</tr></thead>
                        <tbody>
                            @foreach ($salaryMonthlyHistory as $monthData)
                                <tr>
                                    <th>{{ $monthNames[$monthData['month']] }}</th>
                                    @foreach ($historicalYears as $historicalYear)
                                        <td>{{ number_format($monthData['values'][$historicalYear] ?? 0, 2, ',', ' ') }} €</td>
                                    @endforeach
                                </tr>
                            @endforeach
                            @if ($historicalYears->isEmpty())
                                <tr><td colspan="13">Aucune donnée de salaire disponible.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </section>
            <section class="finance-panel finance-table-panel">
                <h2>WEBSITE / revenus web par mois ({{ $year }})</h2>
                <div class="finance-dashboard-table-wrap">
                    <table>
                        <thead><tr><th>Mois</th><th>Revenus WEBSITE</th></tr></thead>
                        <tbody>
                            @foreach ($monthNames as $monthNumber => $monthName)
                                <tr><th>{{ $monthName }}</th><td>{{ number_format($websiteByYearMonth[$year . '-' . $monthNumber] ?? 0, 2, ',', ' ') }} €</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
            <section class="finance-panel finance-table-panel">
                <h2>Évolution mensuelle des dépenses par catégorie ({{ $year }})</h2>
                <div class="finance-dashboard-table-wrap">
                    <table>
                        <thead>
                            <tr><th>Catégorie</th>@foreach ($monthNames as $monthName)<th>{{ $monthName }}</th>@endforeach<th>Total</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($monthlyExpenseComparison as $category)
                                <tr>
                                    <th><i class="category-dot" style="background: {{ $category['color'] }}"></i>{{ $category['name'] }}</th>
                                    @foreach ($monthNames as $monthNumber => $monthName)
                                        <td>{{ number_format($category['values'][$monthNumber] ?? 0, 2, ',', ' ') }} €</td>
                                    @endforeach
                                    <td><strong>{{ number_format($category['values']->sum(), 2, ',', ' ') }} €</strong></td>
                                </tr>
                            @empty
                                <tr><td colspan="14">Aucune catégorie de dépenses sélectionnée.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
            <section class="finance-panel finance-table-panel">
                <h2>Dépenses par catégorie et comparaison annuelle</h2>
                <div class="finance-dashboard-table-wrap">
                    <table>
                        <thead><tr><th>Catégorie</th>@foreach ($years as $availableYear)<th>{{ $availableYear }}</th>@endforeach</tr></thead>
                        <tbody>
                            @forelse ($expenseComparison as $category)
                                <tr>
                                    <th><i class="category-dot" style="background: {{ $category['color'] }}"></i>{{ $category['name'] }}</th>
                                    @foreach ($years as $availableYear)
                                        <td>{{ number_format($category['values'][$availableYear] ?? 0, 2, ',', ' ') }} €</td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr><td colspan="{{ $years->count() + 1 }}">Aucune catégorie de dépenses sélectionnée.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
            <section class="finance-panel">
                <h2>Dépenses par catégorie — {{ $month ? $monthNames[$month] . ' ' : '' }}{{ $year }}</h2>
                @forelse ($categories as $category)
                    <div class="expense-row">
                        <span class="category-dot" style="background: {{ $category['color'] }}"></span>
                        <span>{{ $category['name'] }}
                            @if ($category['refunds'] > 0)
                                <small class="finance-expense-detail">{{ number_format($category['spent'], 2, ',', ' ') }} € dépensés · {{ number_format($category['refunds'], 2, ',', ' ') }} € remboursés</small>
                            @endif
                        </span>
                        <div class="expense-track"><i
                                style="width: {{ min(max(($category['amount'] / max($expenses, 1)) * 100, 0), 100) }}%; background: {{ $category['color'] }}"></i>
                        </div>
                        <b>{{ number_format($category['amount'], 2, ',', ' ') }} €</b>
                    </div>
                @empty
                    <p class="finance-dashboard-empty">Aucune dépense pour cette période.</p>
                @endforelse
                <div class="finance-dashboard-total"><span>Total des dépenses nettes</span><strong>{{ number_format($expenses, 2, ',', ' ') }} €</strong></div>
            </section>
        @endif

        <footer class="finance-dashboard__footer">Montants en euros (€) · Les montants positifs dans une catégorie de dépense réduisent cette dépense, ils ne deviennent pas des revenus.</footer>
    </div>
@endsection
