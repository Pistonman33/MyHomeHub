@extends('backend.layouts.html')
@section('content')
    <section>
        @include('backend.layouts.success')
        @include('backend.layouts.error')

        <style>
            .transaction-card {

                border: none;
                border-left: 6px solid #e9ecef;
                border-radius: 12px;
                box-shadow: 0 .15rem .5rem rgba(0, 0, 0, .08);
                transition: .15s;
            }

            .transaction-card:hover {

                transform: translateY(-2px);
                box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .12);

            }

            .transaction-card.quick-paypal {
                border-left-color: #0d6efd;
            }

            .transaction-card.quick-amazon {
                border-left-color: #fd7e14;
            }

            .category-pill {
                min-width: 110px;
                margin: 0.2rem;
                transition: transform .15s ease;
            }

            .category-pill:hover {
                transform: translateY(-1px);
            }

            .quick-group-badge {
                text-transform: uppercase;
                letter-spacing: .04em;
                font-size: .72rem;
            }

            .transaction-amount {
                min-width: 170px;
                text-align: right;
                flex-shrink: 0;
            }

            .transaction-amount .amount {
                font-size: 1.45rem;
                font-weight: 700;
                line-height: 1;
                white-space: nowrap;
            }

            .transaction-details {
                flex: 1;
                min-width: 0;
            }

            .transaction-title {
                font-weight: 600;
            }

            .category-list {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }

            .category-pill {
                margin: 0;
                white-space: nowrap;
                flex: 0 0 auto;
            }
        </style>

        @if (!empty($display_transactions))
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                <div>
                    <h4 class="mb-1">Transactions à valider</h4>
                    <p class="text-muted mb-0">{{ $nb_transaction }} en attente • 4 transactions affichées par lot</p>
                </div>
                <div class="btn-group mt-2 mt-md-0">
                    @if ($previous_offset !== null)
                        <a class="btn btn-outline-secondary" href="{{ url('admin/finance/show/' . $previous_offset) }}">←
                            Précédent</a>
                    @endif
                    @if ($next_offset !== null)
                        <a class="btn btn-outline-secondary" href="{{ url('admin/finance/show/' . $next_offset) }}">Suivant
                            →</a>
                    @endif
                </div>
            </div>

            @php
                $grouped_transactions = collect($display_transactions)->groupBy('group');
            @endphp

            @foreach (['paypal', 'amazon', 'other'] as $group_key)
                @php $rows = $grouped_transactions->get($group_key, collect()); @endphp
                @if ($rows->isNotEmpty())
                    <div class="mb-4">
                        @if ($group_key === 'paypal')
                            <h5 class="mb-3"><span class="badge badge-primary quick-group-badge">PayPal</span> <small
                                    class="text-muted">{{ $rows->count() }}
                                    transaction{{ $rows->count() > 1 ? 's' : '' }}</small></h5>
                        @elseif($group_key === 'amazon')
                            <h5 class="mb-3"><span class="badge badge-warning quick-group-badge">Amazon</span> <small
                                    class="text-muted">{{ $rows->count() }}
                                    transaction{{ $rows->count() > 1 ? 's' : '' }}</small></h5>
                        @else
                            <h5 class="mb-3">Autres transactions</h5>
                        @endif
                        <div class="row">
                            @foreach ($rows as $row)
                                @php $transaction = $row['transaction']; @endphp
                                <div class="col-lg-6 mb-3">
                                    <div
                                        class="card transaction-card {{ $row['group'] === 'paypal' ? 'quick-paypal' : ($row['group'] === 'amazon' ? 'quick-amazon' : '') }}">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">

                                                <div class="transaction-details">

                                                    <div class="text-muted small">
                                                        {{ App\Models\Display::dateDMY($transaction->date) }}
                                                    </div>

                                                    <div class="transaction-title">
                                                        {{ $transaction->details ?: $transaction->libelle }}
                                                    </div>

                                                </div>

                                                <div class="transaction-amount">

                                                    <div class="text-muted small">
                                                        Montant
                                                    </div>

                                                    <div
                                                        class="amount {{ $transaction->retrait ? 'text-danger' : 'text-success' }}">
                                                        {{ App\Models\Display::transactionAmount($transaction) }}
                                                    </div>

                                                </div>

                                            </div>

                                            <div class="mb-2">
                                                @if ($row['group'] !== 'other')
                                                    <span
                                                        class="badge badge-light text-dark">{{ $row['group_label'] }}</span>
                                                @endif
                                                <span class="badge badge-secondary">{{ $transaction->nom }}</span>
                                            </div>

                                            <form method="POST" action="{{ route('admin.finance.show') }}">
                                                @csrf
                                                <input type="hidden" name="record_id" value="{{ $transaction->id }}" />
                                                <input type="hidden" name="offset" value="{{ $offset }}" />

                                                <div class="form-group mb-2">
                                                    <label class="small text-uppercase text-muted">Libellé</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        name="libelle"
                                                        value="{{ old('libelle', $row['suggested_label']) }}" required>
                                                </div>

                                                <div class="form-group mb-0">
                                                    <label class="small text-uppercase text-muted">Catégorie</label>
                                                    <div class="category-list">
                                                        @foreach ($all_category as $category)
                                                            <button type="submit" name="category_id"
                                                                value="{{ $category->id }}"
                                                                class="btn btn-sm category-pill"
                                                                style="background-color:{{ $category->getColor() }};color:white;">
                                                                {{ $category->nom }}
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        @else
            <div class="alert alert-warning" role="alert">
                No transaction to update, please upload file transaction <a href="{{ url('admin/finance/import') }}"
                    class="alert-link">here</a>
            </div>
        @endif
    </section>
@endsection
