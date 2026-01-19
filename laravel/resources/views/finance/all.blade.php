@extends('layouts.html')
@section('content')
<section>
  <div class="form-group">
    <div class="col-12">
        <input type="hidden" name="catid" id="catid" value="{{$filter_categoryid}}" />
        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          {{ $filter_category }}
        </button>
        <div class="dropdown-menu">
          @foreach ($all_category as $category)
              <a class="dropdown-item" href=" {{url('admin/finance/all?category='.$category->id) }}">{{ $category->nom }}</a>
          @endforeach
        </div>
        <div class="btn-group">
          <input type="text" class="form-controller" id="search" name="search" />
        </div>
    </div>
  </div>
  <div class="form-group">
      <div class="col-xs-12">
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>Date</th>
                <th>Libelle</th>
                <th>Category</th>
                <th>Amount</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
      </div>
  </div>
</section>
@endsection
