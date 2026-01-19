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
        <canvas id="revenueDepenseChart"></canvas>
        </div>
        <div class="form-group">
        <h1>Revenus par Catégorie</h1>
        </div>
        <div class="form-group">
        <canvas id="revenueCategoriesChart"></canvas>
        </div>
        <div class="form-group">
        <h1>Dépenses par Catégorie</h1>
        </div>
        <canvas id="depensesCategoriesChart"></canvas>
        </div>
      </div>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const revenueDepenseData = @json($revenu_depense_charts);

new Chart(document.getElementById('revenueDepenseChart'), {
    type: 'bar',
    data: {
        labels: revenueDepenseData.labels,
        datasets: revenueDepenseData.datasets
    },
});

const revenueCat = @json($revenu_categories_charts);

new Chart(document.getElementById('revenueCategoriesChart'), {
    type: 'pie',
    data: {
        labels: revenueCat.labels,
        datasets: [{
            data: revenueCat.data,
            backgroundColor: revenueCat.colors,
        }]
    },
});

const depensesCat = @json($depenses_categories_charts);

new Chart(document.getElementById('depensesCategoriesChart'), {
    type: 'pie',
    data: {
        labels: depensesCat.labels,
        datasets: [{
            data: depensesCat.data,
            backgroundColor: depensesCat.colors,
        }]
    },
});
</script>

</section>
@endsection
