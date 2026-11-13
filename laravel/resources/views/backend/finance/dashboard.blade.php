@extends('backend.layouts.html')

@section('content')
    @php
        $monthNames = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];
        $activeAccountLabel = $dashboard === 'evolution'
            ? 'Revenus personnels · dépenses du compte commun'
            : ($dashboard === 'moi' ? 'Mon compte personnel' : 'Compte commun');
        $periodLabel = $month ? $monthNames[$month] . ' ' . $year : $year;
        $percentChange = function ($current, $previous) {
            return $previous > 0 ? (($current - $previous) / $previous) * 100 : null;
        };
        $incomeChange = $percentChange($revenues, $previousRevenues);
        $expenseChange = $percentChange($expenses, $previousExpenses);
        $positiveExpenses = $categories->sum(function ($category) {
            return max((float) $category['amount'], 0);
        });
        $donutStart = 0;
        $donutStops = [];
        foreach ($categories as $category) {
            $amount = max((float) $category['amount'], 0);
            $donutEnd = $donutStart + ($positiveExpenses > 0 ? ($amount / $positiveExpenses) * 100 : 0);
            if ($donutEnd > $donutStart) {
                $donutStops[] = $category['color'] . ' ' . $donutStart . '% ' . $donutEnd . '%';
            }
            $donutStart = $donutEnd;
        }
        $donutBackground = $donutStops ? implode(', ', $donutStops) : '#e8edf4 0 100%';
        $monthlyMax = max((float) $monthly->max('revenue'), (float) $monthly->max('expense'), 1);
        $maxCategory = max((float) $categories->max('amount'), 1);
    @endphp

    <div class="finance-dashboard">
        <header class="finance-dashboard__header">
            <div>
                <h1>Dashboard Finance</h1>
                <p>{{ $activeAccountLabel }} · {{ $periodLabel }}</p>
            </div>
            <form method="GET" action="{{ route('admin.finance.dashboard') }}" class="finance-year-filter">
                <input type="hidden" name="view" value="{{ $dashboard }}">
                <label for="dashboard-year">Année</label>
                <span class="finance-year-select-wrap">
                    <select id="dashboard-year" name="year" onchange="this.form.submit()">
                        @foreach ($years as $availableYear)
                            <option value="{{ $availableYear }}" @selected((int) $availableYear === $year)>{{ $availableYear }}</option>
                        @endforeach
                        @if ($years->isEmpty())
                            <option value="{{ $year }}" selected>{{ $year }}</option>
                        @endif
                    </select><span class="fa-regular fa-calendar {{ $month ? 'is-visible' : '' }}"></span>
                </span>
                <button type="button" class="finance-dashboard-filter-button" id="dashboard-filter-button">
                    <i class="fa-solid fa-filter"></i> Filtrer
                </button>
                <select id="dashboard-month" name="month"
                    class="finance-dashboard-month-select {{ $month ? 'is-visible' : '' }}" aria-label="Mois"
                    onchange="this.form.submit()">
                    <option value="">Toute l'année</option>
                    @foreach ($monthNames as $monthNumber => $monthName)
                        <option value="{{ $monthNumber }}" @selected($month === $monthNumber)>{{ $monthName }}</option>
                    @endforeach
                </select>
                @if ($dashboard === 'evolution')
                    @foreach ($selectedCategoryIds as $selectedCategoryId)
                        <input type="hidden" name="categories[]" value="{{ $selectedCategoryId }}">
                    @endforeach
                @endif
            </form>
        </header>

        <nav class="finance-dashboard-tabs" aria-label="Vues du dashboard financier">
            <a href="{{ route('admin.finance.dashboard', array_merge(request()->except(['view', 'categories', 'account']), ['view' => 'menage'])) }}"
                class="{{ $dashboard === 'menage' ? 'is-active' : '' }}">Ménage</a>
            <a href="{{ route('admin.finance.dashboard', array_merge(request()->except(['view', 'categories', 'account']), ['view' => 'moi'])) }}"
                class="{{ $dashboard === 'moi' ? 'is-active' : '' }}">Moi</a>
            <a href="{{ route('admin.finance.dashboard', array_merge(request()->except(['view', 'categories', 'account']), ['view' => 'evolution'])) }}"
                class="{{ $dashboard === 'evolution' ? 'is-active' : '' }}">Évolution</a>
            <a href="{{ route('admin.finance.dashboard', ['view' => 'maison']) }}">Maison</a>
        </nav>

        @if ($dashboard === 'evolution')
            <form method="GET" action="{{ route('admin.finance.dashboard') }}" class="finance-dashboard-filters">
                <input type="hidden" name="view" value="{{ $dashboard }}">
                <input type="hidden" name="year" value="{{ $year }}">
                <input type="hidden" name="month" value="{{ $month }}">
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
            </form>
        @endif

        @if ($dashboard === 'menage' && !$commonAccountConfigured)
            <div class="finance-dashboard-notice" role="status">
                Aucun compte nommé « COMPTE COMMUN » n'a été trouvé. Vérifie le nom du compte dans la table des comptes.
            </div>
        @elseif ($dashboard === 'moi' && !$personalAccountConfigured)
            <div class="finance-dashboard-notice" role="status">
                Aucun compte nommé « THIEBAULT MICHAEL » n'a été trouvé. Vérifie le nom du compte dans la table des comptes.
            </div>
        @elseif ($dashboard === 'evolution' && (!$commonAccountConfigured || !$personalAccountConfigured))
            <div class="finance-dashboard-notice" role="status">
                @if (!$personalAccountConfigured)
                    Le compte « THIEBAULT MICHAEL » est introuvable : les revenus de l'évolution ne seront pas disponibles.
                @endif
                @if (!$commonAccountConfigured)
                    Le compte « COMPTE COMMUN » est introuvable : les dépenses de l'évolution ne seront pas disponibles.
                @endif
            </div>
        @endif

        @if ($dashboard === 'menage')
            <p class="finance-dashboard-note">
                Les dépenses du ménage sont basées sur le compte commun. Les provisions ne sont ni des revenus ni des dépenses.
                Les transactions du compte personnel de votre conjointe ne sont pas importées.
            </p>
        @elseif ($dashboard === 'moi')
            <p class="finance-dashboard-note">
                Les provisions sont isolées des revenus et dépenses. Les transactions du compte personnel de votre conjointe ne sont pas importées.
            </p>
        @else
            <p class="finance-dashboard-note">
                Les revenus (dont salaire et WEBSITE) et provisions viennent de votre compte personnel ; les dépenses par catégorie viennent du compte commun.
                L'épargne globale du ménage n'est pas calculée faute de transactions du compte personnel de votre conjointe.
            </p>
        @endif

        <div class="finance-kpis">
            <article class="finance-kpi finance-kpi--green">
                <span class="finance-kpi__icon">↗</span>
                <div>
                    <small>Revenus réels</small>
                    <strong>{{ number_format($revenues, 0, ',', ' ') }} €</strong>
                    <em>{{ $dashboard === 'evolution' ? 'Mon compte personnel' : ($incomeChange !== null ? sprintf('%+.1f %% vs %s', $incomeChange, $month ? $monthNames[$month] . ' ' . ($year - 1) : $year - 1) : 'Pas de période précédente comparable') }}</em>
                </div>
            </article>
            <article class="finance-kpi finance-kpi--red">
                <span class="finance-kpi__icon">↘</span>
                <div>
                    <small>Dépenses nettes</small>
                    <strong>{{ number_format($expenses, 0, ',', ' ') }} €</strong>
                    <em>{{ $dashboard === 'evolution' ? 'Compte commun' : ($expenseChange !== null ? sprintf('%+.1f %% vs %s', $expenseChange, $month ? $monthNames[$month] . ' ' . ($year - 1) : $year - 1) : 'Remboursements déduits par catégorie') }}</em>
                </div>
            </article>
            <article class="finance-kpi finance-kpi--blue">
                <span class="finance-kpi__icon">●</span>
                <div>
                    <small>{{ $dashboard === 'evolution' ? 'Salaire — compte personnel' : ($dashboard === 'moi' ? 'Reste après dépenses et provisions' : 'Écart revenus / dépenses') }}</small>
                    <strong>{{ $dashboard === 'evolution' ? number_format($salary, 0, ',', ' ') . ' €' : number_format($savings, 0, ',', ' ') . ' €' }}</strong>
                    <em>{{ $dashboard === 'evolution' ? 'Catégorie SALAIRE uniquement' : ($dashboard === 'moi' ? 'Les provisions restent hors dépenses' : 'Les revenus de la conjointe ne sont pas inclus') }}</em>
                </div>
            </article>
            <article class="finance-kpi finance-kpi--purple">
                <span class="finance-kpi__icon">%</span>
                <div>
                    <small>{{ $dashboard === 'evolution' ? 'Revenus WEBSITE — compte personnel' : 'Taux d’épargne' }}</small>
                    <strong>{{ $dashboard === 'evolution' ? number_format($websiteRevenue, 0, ',', ' ') . ' €' : number_format($savingRate, 1, ',', ' ') . ' %' }}</strong>
                    <em>{{ $dashboard === 'evolution' ? 'Suivis séparément du salaire' : ($transactionCount . ' transaction(s) validée(s) · ' . ($dashboard === 'moi' ? number_format($provisions, 0, ',', ' ') . ' € de provisions' : 'Provisions exclues')) }}</em>
                </div>
            </article>
            <article class="finance-kpi finance-kpi--orange">
                <span class="finance-kpi__icon">€</span>
                <div>
                    <small>{{ $dashboard === 'evolution' ? 'Versements au compte commun' : 'Dépenses moyennes / mois' }}</small>
                    <strong>{{ number_format($dashboard === 'evolution' ? $provisions : $averageMonthly, 0, ',', ' ') }} €</strong>
                    <em>{{ $dashboard === 'evolution' ? 'Depuis mon compte personnel' : ($month ? 'Sur le mois sélectionné' : 'Sur ' . $year) }}</em>
                </div>
            </article>
        </div>

        <div class="finance-grid finance-grid--top">
            <section class="finance-panel finance-panel--categories">
                <h2>Dépenses par catégorie <span>({{ $periodLabel }})</span></h2>
                <div class="category-chart-wrap">
                    <div class="category-donut" style="background: conic-gradient({{ $donutBackground }})">
                        <div><strong>{{ number_format($expenses, 0, ',', ' ') }} €</strong><small>Total net</small></div>
                    </div>
                    <div class="category-legend">
                        @forelse ($categories as $category)
                            <div>
                                <span class="category-dot" style="background: {{ $category['color'] }}"></span>
                                <span>{{ $category['name'] }}</span>
                                <b>{{ number_format($category['amount'], 0, ',', ' ') }} €</b>
                                <small>{{ $positiveExpenses > 0 ? number_format((max($category['amount'], 0) / $positiveExpenses) * 100, 0) : 0 }}%</small>
                            </div>
                        @empty
                            <p class="finance-dashboard-empty">Aucune dépense catégorisée pour cette période.</p>
                        @endforelse
                    </div>
                </div>
            </section>
            <section class="finance-panel">
                <h2>Évolution mensuelle <span>({{ $year }})</span></h2>
                <div class="finance-chart-legend">
                    <span class="legend-green">{{ $dashboard === 'evolution' ? 'Revenus personnels' : 'Revenus réels' }}</span>
                    <span class="legend-red">{{ $dashboard === 'evolution' ? 'Dépenses compte commun' : 'Dépenses nettes' }}</span>
                    @if ($dashboard !== 'evolution')
                        <span class="legend-blue">Solde net</span>
                    @endif
                </div>
                <div class="monthly-chart">
                    @foreach ($monthly as $monthData)
                        <div class="monthly-column">
                            <div class="monthly-bars">
                                <i class="bar-green" title="Revenus : {{ number_format($monthData['revenue'], 2, ',', ' ') }} €"
                                    style="height: {{ min(($monthData['revenue'] / $monthlyMax) * 100, 100) }}%"></i>
                                <i class="bar-red" title="Dépenses : {{ number_format($monthData['expense'], 2, ',', ' ') }} €"
                                    style="height: {{ min((max($monthData['expense'], 0) / $monthlyMax) * 100, 100) }}%"></i>
                                @if ($dashboard !== 'evolution')
                                    <i class="bar-blue" title="Solde : {{ number_format($monthData['savings'], 2, ',', ' ') }} €"
                                        style="height: {{ min((max($monthData['savings'], 0) / $monthlyMax) * 100, 100) }}%"></i>
                                @endif
                            </div>
                            <small>{{ $monthNames[$monthData['month']] }}</small>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        @if ($dashboard === 'moi')
            <div class="finance-grid finance-grid--bottom">
                <section class="finance-panel">
                    <h2>Revenus par catégorie <span>({{ $periodLabel }})</span></h2>
                    <div class="finance-dashboard-line"><span>Salaire</span><strong>{{ number_format($salary, 2, ',', ' ') }} €</strong></div>
                    <div class="finance-dashboard-line"><span>WEBSITE / revenus web</span><strong>{{ number_format($websiteRevenue, 2, ',', ' ') }} €</strong></div>
                    @if ($otherRevenues > 0)
                        <div class="finance-dashboard-line"><span>Autres revenus catégorisés</span><strong>{{ number_format($otherRevenues, 2, ',', ' ') }} €</strong></div>
                    @endif
                    <div class="finance-dashboard-total"><span>Total des revenus réels</span><strong>{{ number_format($revenues, 2, ',', ' ') }} €</strong></div>
                    <div class="finance-dashboard-line"><span>Versements au compte commun</span><strong>{{ number_format($provisions, 2, ',', ' ') }} €</strong></div>
                </section>
                <section class="finance-panel">
                    <h2>Dépenses personnelles par catégorie</h2>
                    @forelse ($categories as $category)
                        <div class="expense-row">
                            <span class="category-dot" style="background: {{ $category['color'] }}"></span>
                            <span>{{ $category['name'] }}
                                @if ($category['refunds'] > 0)
                                    <small class="finance-expense-detail">{{ number_format($category['spent'], 2, ',', ' ') }} € dépensés · {{ number_format($category['refunds'], 2, ',', ' ') }} € remboursés</small>
                                @endif
                            </span>
                            <div class="expense-track"><i style="width: {{ min((max($category['amount'], 0) / $maxCategory) * 100, 100) }}%; background: {{ $category['color'] }}"></i></div>
                            <b>{{ number_format($category['amount'], 2, ',', ' ') }} €</b>
                        </div>
                    @empty
                        <p class="finance-dashboard-empty">Aucune dépense catégorisée pour cette période.</p>
                    @endforelse
                </section>
            </div>
        @else
            <div class="finance-grid finance-grid--bottom">
                <section class="finance-panel">
                    <h2>{{ $dashboard === 'menage' ? 'Dépenses du compte commun' : 'Dépenses nettes par catégorie' }}</h2>
                    @forelse ($categories as $category)
                        <div class="expense-row">
                            <span class="category-dot" style="background: {{ $category['color'] }}"></span>
                            <span>{{ $category['name'] }}
                                @if ($category['refunds'] > 0)
                                    <small class="finance-expense-detail">{{ number_format($category['spent'], 2, ',', ' ') }} € dépensés · {{ number_format($category['refunds'], 2, ',', ' ') }} € remboursés</small>
                                @endif
                            </span>
                            <div class="expense-track"><i style="width: {{ min((max($category['amount'], 0) / $maxCategory) * 100, 100) }}%; background: {{ $category['color'] }}"></i></div>
                            <b>{{ number_format($category['amount'], 2, ',', ' ') }} €</b>
                        </div>
                    @empty
                        <p class="finance-dashboard-empty">Aucune dépense catégorisée pour cette période.</p>
                    @endforelse
                </section>
                <section class="finance-panel finance-table-panel">
                    <h2>Comparaison de période par catégorie</h2>
                    <table>
                        <thead><tr><th>Catégorie</th><th>{{ $month ? $monthNames[$month] . ' ' . ($year - 1) : $year - 1 }}</th><th>{{ $periodLabel }}</th><th>Évolution</th></tr></thead>
                        <tbody>
                            @forelse ($comparison as $category)
                                <tr>
                                    <td><span class="category-dot" style="background: {{ $category['color'] }}"></span>{{ $category['name'] }}</td>
                                    <td>{{ number_format($category['previous'], 0, ',', ' ') }} €</td>
                                    <td>{{ number_format($category['amount'], 0, ',', ' ') }} €</td>
                                    <td class="{{ ($category['evolution'] ?? 0) > 0 ? 'is-up' : 'is-down' }}">
                                        {{ $category['evolution'] === null ? 'n/a' : sprintf('%+.1f %%', $category['evolution']) }}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4">Aucune dépense catégorisée pour cette période.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </section>
            </div>
        @endif

        @if ($dashboard === 'evolution')
            <section class="finance-panel finance-evolution-panel">
                <h2>Évolution du salaire par mois</h2>
                @if ($salaryTimeline->isNotEmpty())
                    <div class="finance-salary-chart" role="img" aria-label="Évolution du salaire dans le temps">
                        @foreach ($salaryTimeline as $point)
                            <div class="finance-salary-point" title="{{ $point['period'] }} : {{ number_format($point['amount'], 2, ',', ' ') }} €">
                                <span style="height: {{ max(($point['amount'] / max((float) $salaryTimeline->max('amount'), 1)) * 100, 2) }}%"></span>
                                <small>{{ $point['period'] }}</small>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="finance-dashboard-empty">Aucun revenu de catégorie SALAIRE trouvé pour les comptes sélectionnés.</p>
                @endif
            </section>
            <section class="finance-panel finance-table-panel finance-evolution-panel">
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
            <section class="finance-panel finance-table-panel finance-evolution-panel">
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
            <section class="finance-panel finance-table-panel finance-evolution-panel">
                <h2>Évolution mensuelle des dépenses par catégorie ({{ $year }})</h2>
                <div class="finance-dashboard-table-wrap">
                    <table>
                        <thead><tr><th>Catégorie</th>@foreach ($monthNames as $monthName)<th>{{ $monthName }}</th>@endforeach<th>Total</th></tr></thead>
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
            <section class="finance-panel finance-table-panel finance-evolution-panel">
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
        @endif

        <footer class="finance-dashboard__footer">
            Montants en euros (€) · Les montants positifs dans une catégorie de dépense réduisent cette dépense, ils ne deviennent pas des revenus.
        </footer>
    </div>

    <script>
        document.getElementById('dashboard-filter-button')?.addEventListener('click', function() {
            document.getElementById('dashboard-month')?.classList.toggle('is-visible');
            document.querySelector('.finance-year-select-wrap .fa-calendar')?.classList.toggle('is-visible');
        });
    </script>
@endsection
