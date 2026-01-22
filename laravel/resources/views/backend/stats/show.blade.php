@extends('backend.layouts.html')

@section('content')
<section>
  <div class="form-group">
      <div class="row">
        <div class="col-6">
          @if(!empty($chart_1))
          <p>{!! $chart_title1 !!}</p>
          <canvas id="chart1"></canvas>
          @endif
        </div>
        <div class="col-6">
          @if(!empty($chart_2))
          <p>{!! $chart_title2 !!}</p>
          <canvas id="chart2"></canvas>
          @endif
        </div>
      </div>

      <div class="row mt-3">
        <div class="col-6">
          <p>
            <input type="radio" name="byperiod" id="period_month" value="month" {{ ($period == "month") ? 'checked' : '' }} />
            &nbsp;&nbsp;<label for="period_month">Monthly price</label>
          </p>
          <p>
            <input type="radio" name="byperiod" id="period_year" value="year" {{ ($period == "year") ? 'checked' : '' }} />
            &nbsp;&nbsp;<label for="period_year">Yearly price</label>
          </p>
        </div>

        <div class="col-6">
          @if($year)
          <div class="btn-group">
            <input type="hidden" id="year_filter" value="{{ $year['filter_year'] }}" />
            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              {{ $year['filter_year'] }}
            </button>
            <div class="dropdown-menu">
              @foreach ($year['list_years'] as $year_to_display)
                  <a class="dropdown-item" href="{{ url(Request::url() . '?year=' . $year_to_display->year) }}">
                    {{ $year_to_display->year }}
                  </a>
              @endforeach
            </div>
          </div>
          @endif
        </div>
      </div>
  </div>
</section>

@if(!empty($chart_1) || !empty($chart_2))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  @if(!empty($chart_1))
  const ctx1 = document.getElementById('chart1').getContext('2d');
  const chart1Config = {!! $chart_1 !!};
  new Chart(ctx1, chart1Config);
  @endif

  @if(!empty($chart_2))
  const ctx2 = document.getElementById('chart2').getContext('2d');
  const chart2Config = {!! $chart_2 !!};
  new Chart(ctx2, chart2Config);
  @endif
</script>
@endif

@endsection
