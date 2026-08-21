@extends('backend.layouts.html')

@section('content')
    <div class="finance-dashboard">
        <div class="finance-dashboard__header">
            <div>
                <h1>Vue d'ensemble</h1>
                <p>Aperçu financier de votre famille</p>
            </div>
            <form method="GET" action="{{ route('admin.finance.dashboard') }}" class="finance-year-filter">
                <label for="dashboard-year">Année</label>
                <select id="dashboard-year" name="year" onchange="this.form.submit()">
                    @foreach ($years as $availableYear)
                        <option value="{{ $availableYear }}" @selected((int) $availableYear === $year)>{{ $availableYear }}</option>
                    @endforeach
                    @if ($years->isEmpty())
                        <option value="{{ $year }}" selected>{{ $year }}</option>
                    @endif
                </select><span class="fa-regular fa-calendar"></span>
            </form>
        </div>
        @php
            $percentChange = function ($current, $previous) {
                return $previous > 0 ? (($current - $previous) / $previous) * 100 : null;
            };
            $averageMonthly = $expenses / 12;
            $savingRate = $revenues > 0 ? ($savings / $revenues) * 100 : 0;
            $maxCategory = max((float) ($categories->first()['amount'] ?? 0), 1);
            $donutStart = 0;
            $donutStops = [];
            foreach ($categories as $category) {
                $donutEnd = $donutStart + ($expenses > 0 ? ($category['amount'] / $expenses) * 100 : 0);
                $donutStops[] = $category['color'] . ' ' . $donutStart . '% ' . $donutEnd . '%';
                $donutStart = $donutEnd;
            }
            $donutBackground = $donutStops ? implode(', ', $donutStops) : '#e8edf4 0 100%';
        @endphp
        <div class="finance-kpis">
            <div class="finance-kpi finance-kpi--green"><span class="finance-kpi__icon">↗</span>
                <div><small>Revenus totaux</small><strong>{{ number_format($revenues, 0, ',', ' ') }}
                        €</strong><em>{{ $percentChange($revenues, $previousRevenues) !== null ? sprintf('%+.1f %% vs %s', $percentChange($revenues, $previousRevenues), $year - 1) : 'Données de référence indisponibles' }}</em>
                </div>
            </div>
            <div class="finance-kpi finance-kpi--red"><span class="finance-kpi__icon">↘</span>
                <div><small>Dépenses totales</small><strong>{{ number_format($expenses, 0, ',', ' ') }}
                        €</strong><em>{{ $percentChange($expenses, $previousExpenses) !== null ? sprintf('%+.1f %% vs %s', $percentChange($expenses, $previousExpenses), $year - 1) : 'Données de référence indisponibles' }}</em>
                </div>
            </div>
            <div class="finance-kpi finance-kpi--blue"><span class="finance-kpi__icon">●</span>
                <div><small>Épargne nette</small><strong>{{ number_format($savings, 0, ',', ' ') }}
                        €</strong><em>{{ $revenues > 0 ? sprintf('%+.1f %% des revenus', $savingRate) : 'Aucun revenu enregistré' }}</em>
                </div>
            </div>
            <div class="finance-kpi finance-kpi--purple"><span class="finance-kpi__icon">%</span>
                <div><small>Taux d'épargne</small><strong>{{ number_format($savingRate, 1, ',', ' ') }}
                        %</strong><em>{{ $transactionCount }} transaction(s) validée(s)</em></div>
            </div>
            <div class="finance-kpi finance-kpi--orange"><span class="finance-kpi__icon">€</span>
                <div><small>Dépense moyenne / mois</small><strong>{{ number_format($averageMonthly, 0, ',', ' ') }}
                        €</strong><em>Sur {{ $year }}</em></div>
            </div>
        </div>
        <div class="finance-grid finance-grid--top">
            <section class="finance-panel finance-panel--categories">
                <h2>Répartition des dépenses par catégorie <span>({{ $year }})</span></h2>
                <div class="category-chart-wrap">
                    <div class="category-donut" style="background: conic-gradient({{ $donutBackground }})">
                        <div><strong>{{ number_format($expenses, 0, ',', ' ') }} €</strong><small>Total</small></div>
                    </div>
                    <div class="category-legend">
                        @foreach ($categories as $category)
                            <div><span class="category-dot"
                                    style="background: {{ $category['color'] }}"></span><span>{{ $category['name'] }}</span><b>{{ number_format($category['amount'], 0, ',', ' ') }}
                                    €</b><small>{{ $expenses > 0 ? number_format(($category['amount'] / $expenses) * 100, 0) : 0 }}%</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
            <section class="finance-panel">
                <h2>Évolution mensuelle <span>({{ $year }})</span></h2>
                <div class="finance-chart-legend"><span class="legend-green">Revenus</span><span
                        class="legend-red">Dépenses</span><span class="legend-blue">Épargne</span></div>
                <div class="monthly-chart">
                    @foreach ($monthly as $month)
                        <div class="monthly-column">
                            <div class="monthly-bars"><i class="bar-green"
                                    style="height: {{ min(($month['revenue'] / max($revenues, 1)) * 100, 100) }}%"></i><i
                                    class="bar-red"
                                    style="height: {{ min(($month['expense'] / max($revenues, 1)) * 100, 100) }}%"></i><i
                                    class="bar-blue"
                                    style="height: {{ min((max($month['savings'], 0) / max($revenues, 1)) * 100, 100) }}%"></i>
                            </div><small>{{ date('M', mktime(0, 0, 0, $loop->iteration, 1)) }}</small>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
        <div class="finance-grid finance-grid--bottom">
            <section class="finance-panel">
                <h2>Dépenses par catégorie <span>({{ $year }})</span></h2>
                @foreach ($categories as $category)
                    <div class="expense-row"><span class="category-dot"
                            style="background: {{ $category['color'] }}"></span><span>{{ $category['name'] }}</span>
                        <div class="expense-track"><i
                                style="width: {{ min(($category['amount'] / $maxCategory) * 100, 100) }}%; background: {{ $category['color'] }}"></i>
                        </div><b>{{ number_format($category['amount'], 0, ',', ' ') }} €</b>
                    </div>
                @endforeach
            </section>
            <section class="finance-panel finance-table-panel">
                <h2>Comparaison annuelle par catégorie</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Catégorie</th>
                            <th>{{ $year - 1 }}</th>
                            <th>{{ $year }}</th>
                            <th>Évolution</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($comparison as $category)
                            <tr>
                                <td><span class="category-dot"
                                        style="background: {{ $category['color'] }}"></span>{{ $category['name'] }}</td>
                                <td>{{ number_format($category['previous'], 0, ',', ' ') }} €</td>
                                <td>{{ number_format($category['amount'], 0, ',', ' ') }} €</td>
                                <td class="{{ ($category['evolution'] ?? 0) > 0 ? 'is-up' : 'is-down' }}">
                                    {{ $category['evolution'] === null ? 'n/a' : sprintf('%+.1f %%', $category['evolution']) }}
                                </td>
                        </tr>@empty<tr>
                                <td colspan="4">Aucune dépense catégorisée pour cette année.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>
            <section class="finance-panel finance-table-panel">
                <h2>Budget vs Réel <span>({{ $year }})</span></h2>
                <table>
                    <thead>
                        <tr>
                            <th>Catégorie</th>
                            <th>Budget annuel</th>
                            <th>Dépensé</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td><span class="category-dot"
                                        style="background: {{ $category['color'] }}"></span>{{ $category['name'] }}</td>
                                <td>{{ number_format($category['amount'], 0, ',', ' ') }} €</td>
                                <td>{{ number_format($category['amount'], 0, ',', ' ') }} €</td>
                                <td>
                                    <div class="budget-progress"><i></i></div>100%
                                </td>
                        </tr>@empty<tr>
                                <td colspan="4">Aucune donnée disponible.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>
        </div>
        <footer class="finance-dashboard__footer">Toutes les valeurs sont en euros (€)</footer>
    </div>
@endsection
