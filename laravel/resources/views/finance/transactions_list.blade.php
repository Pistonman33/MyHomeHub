<div class="form-group">
  <div class="btn-group">
    <a class="btn btn-secondary" href="{{ route('finance.all') }}" role="button">More...</a>
  </div>
  @if($year)
  <div class="btn-group">
    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      {{ $year['filter_year'] }}
    </button>
    <div class="dropdown-menu">
      @foreach ($year['list_years'] as $year_to_display)
          <a class="dropdown-item" href=" {{url('finance?year='.$year_to_display->year) }}">{{ $year_to_display->year }}</a>
      @endforeach
    </div>
  </div>
  @endif
  @if($year && $month && $transactions)
  <div class="btn-group">
    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      {{ App\Display::monthTextPrefixe($month['filter_month'])}}
    </button>
    <div class="dropdown-menu">
      @foreach ($month['list_months'] as $month_to_display)
          <a class="dropdown-item" href=" {{url('finance?year='.$year['filter_year'].'&month='.$month_to_display->month) }}">{{ App\Display::monthTextPrefixe($month_to_display->month)}}</a>
      @endforeach
    </div>
  </div>
  <div class="btn-group">
    &nbsp;&nbsp;<h1>Transactions: <span class="badge badge-warning">{{count($transactions)}}</span></h1>
  </div>
</div>
<div class="form-group">
  <ul class="list-group list-group-flush list-group-striped">
    @foreach ($transactions as $transaction)
    <li class="list-group-item row d-flex transaction">
        <div class="col-1 text-center">
          <div class="transaction_date">
            <span class="transaction_date_day">{{ App\Display::day($transaction->date) }}</span>
            <span class="transaction_date_month">{{ App\Display::monthTextPrefixe(date("n",strtotime($transaction->date)))}}</span>
          </div>
        </div>
      <div class="col-6 text-left align-self-center libelle">
        {{$transaction->libelle}}
      </div>
      <div class="col-3 text-right align-self-center">
        <span class="badge badge-info" style="background-color:{{ App\Categorie::getColorById($transaction->fk_id_categorie) }};color:white;font-size:14px;margin-top:3px;">{{$transaction->nom}}</span>
      </div>
      <div class="col-2 text-right align-self-center amount <?php echo $transaction->retrait ? "": "orange"; ?>">
        <?php echo App\Display::transactionAmount($transaction); ?>
      </div>
    </li>
    @endforeach
  </ul>
</div>
  @endif
