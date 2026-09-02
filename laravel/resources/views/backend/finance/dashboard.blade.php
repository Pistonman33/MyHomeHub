@extends('backend.layouts.html')

@section('content')
    <div class="finance-dashboard">
        <div class="finance-dashboard__header">
            <div>
                <h1>Overview</h1>
                <p>Your family financial overview</p>
            </div>
            <form method="GET" action="{{ route('admin.finance.dashboard') }}" class="finance-year-filter">
                <label for="dashboard-year">Year</label>
                <select id="dashboard-year" name="year" onchange="this.form.submit()">
                    @foreach ($years as $availableYear)
                        <option value="{{ $availableYear }}" @selected((int) $availableYear === $year)>{{ $availableYear }}</option>
                    @endforeach
                    @if ($years->isEmpty())
                        <option value="{{ $year }}" selected>{{ $year }}</option>
                    @endif
                </select><span class="fa-regular fa-calendar"></span>
                <button type="button" class="finance-dashboard-filter-button" id="dashboard-filter-button">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                <select id="dashboard-month" name="month" class="finance-dashboard-month-select" aria-label="Mois">
                    <option value="">Full year</option>
                    @foreach (range(1, 12) as $monthNumber)
                        <option value="{{ $monthNumber }}" @selected((int) $monthNumber === (int) ($month ?? 0))>
                            {{ DateTime::createFromFormat('!m', $monthNumber)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        @php
            $percentChange = function ($current, $previous) {
                return $previous > 0 ? (($current - $previous) / $previous) * 100 : null;
            };
            $averageMonthly = $expenses / ($month ? 1 : 12);
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
                        €</strong><em>{{ $percentChange($revenues, $previousRevenues) !== null ? sprintf('%+.1f %% vs %s', $percentChange($revenues, $previousRevenues), $year - 1) : 'No baseline data available' }}</em>
                </div>
            </div>
            <div class="finance-kpi finance-kpi--red"><span class="finance-kpi__icon">↘</span>
                <div><small>Dépenses totales</small><strong>{{ number_format($expenses, 0, ',', ' ') }}
                        €</strong><em>{{ $percentChange($expenses, $previousExpenses) !== null ? sprintf('%+.1f %% vs %s', $percentChange($expenses, $previousExpenses), $year - 1) : 'No baseline data available' }}</em>
                </div>
            </div>
            <div class="finance-kpi finance-kpi--blue"><span class="finance-kpi__icon">●</span>
                <div><small>Épargne nette</small><strong>{{ number_format($savings, 0, ',', ' ') }}
                        €</strong><em>{{ $revenues > 0 ? sprintf('%+.1f %% of revenue', $savingRate) : 'No revenue recorded' }}</em>
                </div>
            </div>
            <div class="finance-kpi finance-kpi--purple"><span class="finance-kpi__icon">%</span>
                <div><small>Taux d'épargne</small><strong>{{ number_format($savingRate, 1, ',', ' ') }}
                        %</strong><em>{{ $transactionCount }} validated transaction(s)</em></div>
            </div>
            <div class="finance-kpi finance-kpi--orange"><span class="finance-kpi__icon">€</span>
                <div><small>Average monthly spending</small><strong>{{ number_format($averageMonthly, 0, ',', ' ') }}
                        €</strong><em>Sur {{ $year }}</em></div>
            </div>
        </div>
        <div class="finance-grid finance-grid--top">
            <section class="finance-panel finance-panel--categories">
                <h2>Spending by category <span>({{ $year }})</span></h2>
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
                        class="legend-red">Expenses</span><span class="legend-blue">Savings</span></div>
                <div class="monthly-chart">
                    @foreach ($monthly as $monthData)
                        <div class="monthly-column">
                            <div class="monthly-bars"><i class="bar-green"
                                    style="height: {{ min(($monthData['revenue'] / max($revenues, 1)) * 100, 100) }}%"></i><i
                                    class="bar-red"
                                    style="height: {{ min(($monthData['expense'] / max($revenues, 1)) * 100, 100) }}%"></i><i
                                    class="bar-blue"
                                    style="height: {{ min((max($monthData['savings'], 0) / max($revenues, 1)) * 100, 100) }}%"></i>
                            </div><small>{{ date('M', mktime(0, 0, 0, $monthData['month'], 1)) }}</small>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
        <div class="finance-grid finance-grid--bottom">
            <section class="finance-panel">
                <h2>Expenses by category <span>({{ $year }})</span></h2>
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
                <h2>Yearly comparison by category</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>{{ $year - 1 }}</th>
                            <th>{{ $year }}</th>
                            <th>Change</th>
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
                                <td colspan="4">No categorized expenses for this year.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>
            <section class="finance-panel finance-table-panel">
                <h2>Budget vs Actual <span>({{ $year }})</span></h2>
                <table>
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Budget annuel</th>
                            <th>Spent</th>
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
                                <td colspan="4">No data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>
        </div>
        <footer class="finance-dashboard__footer">All values are in euros (€)</footer>
    </div>
    <script>
        document.getElementById('dashboard-filter-button')?.addEventListener('click', function() {
            document.getElementById('dashboard-month')?.classList.toggle('is-visible');
        });
        document.getElementById('dashboard-month')?.addEventListener('change', function() {
            this.form.submit();
        });
    </script>
@endsection
