@extends('layouts.html')
@section('content')
<section>
  <div class="container-fluid">
    <div class="row">
      <div class="col-8">
        @include('finance.transactions_list')
      </div>
      <div class="col-4">
        <div class="form-group">
        <h1>Revenus/Depenses</h1>
        </div>
        <div class="form-group">
        {!! $revenu_depense_charts->render() !!}
        </div>
        <div class="form-group">
        <h1>Revenus par Catégorie</h1>
        </div>
        <div class="form-group">
        {!! $revenu_categories_charts->render() !!}
        </div>
        <div class="form-group">
        <h1>Dépenses par Catégorie</h1>
        </div>
        {!! $depenses_categories_charts->render() !!}
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
