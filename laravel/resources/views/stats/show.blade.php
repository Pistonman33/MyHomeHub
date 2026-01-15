@extends('layouts.html')
@section('content')
<section>
  <div class="form-group">
      <div class="row">
        <div class="col-6">
          @if( !empty($chart_1))
          <p>{!! $chart_title1 !!}</p>
          <div id="chart1">
            {!! $chart_1->render() !!}
          </div>
          @endif
        </div>
        <div class="col-6">
          @if( !empty($chart_2))
          <p>{!! $chart_title2 !!}</p>
          <div id="chart2">
            {!! $chart_2->render() !!}
          </div>
          @endif
        </div>
      </div>
      <div class="row">
        <div class="col-6">
          <p><input type="radio" name="byperiod" id="period_month" value="month" <?php echo ($period == "month")? 'checked="checked"' :'';?> />&nbsp;&nbsp;<label for="period_month">Monthly price</label></p>
          <p><input type="radio" name="byperiod" id="period_year" value="year" <?php echo ($period == "year")? 'checked="checked"' :'';?>/>&nbsp;&nbsp;<label for="period_year">Yearly price</label></p>
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
                  <a class="dropdown-item" href=" {{url(Request::url().'?year='.$year_to_display->year) }}">{{ $year_to_display->year }}</a>
              @endforeach
            </div>
          </div>
          @endif
        </div>
      </div>
  </div>
</section>
@endsection
