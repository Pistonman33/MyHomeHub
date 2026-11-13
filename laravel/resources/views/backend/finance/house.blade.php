@extends('backend.layouts.html')

@section('content')
    @php
        $formatAmount = fn ($amount) => number_format($amount, 2, ',', ' ') . ' €';
        $progress = $mortgage['total_installments'] > 0
            ? ($mortgage['paid_installments'] / $mortgage['total_installments']) * 100
            : 0;
    @endphp

    <div class="finance-dashboard">
        <header class="finance-dashboard__header">
            <div>
                <h1>Dashboard Maison</h1>
                <p>Suivi estimatif de votre prêt immobilier</p>
            </div>
        </header>

        <nav class="finance-dashboard-tabs" aria-label="Vues du dashboard financier">
            <a href="{{ route('admin.finance.dashboard', ['view' => 'menage']) }}">Ménage</a>
            <a href="{{ route('admin.finance.dashboard', ['view' => 'moi']) }}">Moi</a>
            <a href="{{ route('admin.finance.dashboard', ['view' => 'evolution']) }}">Évolution</a>
            <a href="{{ route('admin.finance.dashboard', ['view' => 'maison']) }}" class="is-active" aria-current="page">Maison</a>
        </nav>

        <section class="finance-house-summary">
            <article class="finance-house-card finance-house-card--highlight">
                <small>Capital restant dû estimé</small>
                <strong>{{ $formatAmount($mortgage['remaining_principal']) }}</strong>
                <span>Après l'échéance n° {{ $mortgage['paid_installments'] }}</span>
            </article>
            <article class="finance-house-card">
                <small>Échéances payées</small>
                <strong>{{ $mortgage['paid_installments'] }} / {{ $mortgage['total_installments'] }}</strong>
                <div class="finance-house-progress" role="progressbar"
                    aria-label="Progression du remboursement" aria-valuenow="{{ round($progress, 1) }}"
                    aria-valuemin="0" aria-valuemax="100">
                    <i style="width: {{ min(max($progress, 0), 100) }}%"></i>
                </div>
                <span>{{ number_format($progress, 1, ',', ' ') }} % de la durée</span>
            </article>
            <article class="finance-house-card">
                <small>Mensualités déjà payées</small>
                <strong>{{ $formatAmount($mortgage['amount_paid']) }}</strong>
                <span>Dont {{ $formatAmount($mortgage['principal_paid']) }} de capital</span>
            </article>
            <article class="finance-house-card">
                <small>Reste à payer estimé</small>
                <strong>{{ $formatAmount($mortgage['remaining_payments']) }}</strong>
                <span>Capital et intérêts futurs, hors assurance et frais</span>
            </article>
            <article class="finance-house-card">
                <small>Intérêts déjà payés</small>
                <strong>{{ $formatAmount($mortgage['interest_paid']) }}</strong>
                <span>Estimation selon le taux mensuel fourni</span>
            </article>
            <article class="finance-house-card">
                <small>Fin prévisionnelle du prêt</small>
                <strong>{{ $mortgage['payoff_date']->format('d/m/Y') }}</strong>
                <span>{{ $mortgage['remaining_installments'] }} échéances restantes</span>
            </article>
        </section>

        <div class="finance-grid finance-grid--bottom finance-house-details">
            <section class="finance-panel">
                <h2>Paramètres du crédit</h2>
                <div class="finance-dashboard-line"><span>Capital emprunté</span><strong>{{ $formatAmount($mortgage['principal']) }}</strong></div>
                <div class="finance-dashboard-line"><span>Mensualité contractuelle communiquée</span><strong>{{ $formatAmount($mortgage['monthly_payment']) }}</strong></div>
                <div class="finance-dashboard-line"><span>Taux annuel communiqué</span><strong>{{ number_format($annualRate, 2, ',', ' ') }} %</strong></div>
                <div class="finance-dashboard-line"><span>Taux mensuel utilisé pour le calcul</span><strong>{{ number_format($monthlyRatePercent, 3, ',', ' ') }} %</strong></div>
                <div class="finance-dashboard-line"><span>Durée initiale</span><strong>25 ans</strong></div>
                <div class="finance-dashboard-line"><span>Première échéance</span><strong>{{ $mortgage['first_payment_date']->format('d/m/Y') }}</strong></div>
                @if ($mortgage['next_payment_date'])
                    <div class="finance-dashboard-line"><span>Prochaine échéance estimée</span><strong>{{ $mortgage['next_payment_date']->format('d/m/Y') }}</strong></div>
                @endif
                <div class="finance-dashboard-line"><span>Intérêts totaux estimés sur la durée</span><strong>{{ $formatAmount($mortgage['total_interest']) }}</strong></div>
                <div class="finance-dashboard-line"><span>Intérêts futurs estimés</span><strong>{{ $formatAmount($mortgage['total_interest'] - $mortgage['interest_paid']) }}</strong></div>
            </section>
            <aside class="finance-house-rate-notice">
                <strong>À propos de l'estimation</strong>
                <p>Le tableau utilise le taux mensuel fourni de 0,168 % et une mensualité de 1 125,28 €. Ces données concordent avec 300 échéances.</p>
                <p>Le taux annuel communiqué de 2,03 % correspond à environ 0,1692 % par mois s'il est divisé par 12, ce qui donnerait une mensualité théorique proche de 1 127,09 €. Le calcul ne peut donc pas remplacer le décompte officiel de la banque.</p>
                <p>L'assurance, les frais, les changements de taux et les remboursements anticipés ne sont pas inclus.</p>
            </aside>
        </div>

        <section class="finance-panel finance-house-schedule">
            <div class="finance-house-schedule__heading">
                <div>
                    <h2>Tableau d'amortissement mensuel</h2>
                    <p>Les échéances déjà dues sont surlignées. Les montants sont estimatifs et arrondis au centime.</p>
                </div>
            </div>
            <div class="finance-dashboard-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Échéance</th>
                            <th>Date</th>
                            <th>Mensualité</th>
                            <th>Capital remboursé</th>
                            <th>Intérêts</th>
                            <th>Capital restant dû</th>
                            <th>État</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mortgage['schedule'] as $installment)
                            <tr class="{{ $installment['paid'] ? 'is-paid' : '' }}">
                                <td>{{ $installment['number'] }}</td>
                                <td>{{ $installment['date']->format('d/m/Y') }}</td>
                                <td>{{ $formatAmount($installment['payment']) }}</td>
                                <td>{{ $formatAmount($installment['principal']) }}</td>
                                <td>{{ $formatAmount($installment['interest']) }}</td>
                                <td>{{ $formatAmount($installment['balance']) }}</td>
                                <td>{{ $installment['paid'] ? 'Payée' : 'À venir' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
