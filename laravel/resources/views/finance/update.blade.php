@extends('layouts.html')
@section('content')
<section>
  @include('layouts.success')
	@include('layouts.error')
  @if($current_transaction)
  <nav aria-label="Page navigation transactions">
    <ul class="pagination justify-content-end">
      <li class="page-item {{ $previous_transaction !== null ? "" : "disabled" }} ">
        <a class="page-link" href="{{ $previous_transaction !== null ? url('finance/show/'.$previous_transaction) : "#"  }}" aria-label="Previous">
          <span aria-hidden="true">&laquo;</span>
          <span class="sr-only">Previous</span>
        </a>
      </li>
      <li><button type="button" class="btn btn-info">
        Transaction(s) <span class="badge badge-light">{{$nb_transaction}}</span>
        </button>
      </li>
      <li class="page-item {{ $next_transaction !== null ? "" : "disabled" }} ">
        <a class="page-link" href="{{ $next_transaction !== null ? url('finance/show/'.$next_transaction) : "#" }}" aria-label="Next">
          <span aria-hidden="true">&raquo;</span>
          <span class="sr-only">Next</span>
        </a>
      </li>
    </ul>
  </nav>
  <div class="container">
      <div class="row justify-content-center">
          <div class="col-md-12">
              <div class="card">
                  <div class="card-header {{ $current_transaction->retrait ? "alert-danger" : "alert-success" }}" align="center">
                      <span class="pull-left">
                      {{ App\Display::dateDMY($current_transaction->date) }}
                      </span>
                      <span>
                      {{ $current_transaction->nom }}
                      </span>
                      <span class="pull-right">
                      <?php echo App\Display::transactionAmount($current_transaction); ?>
                      </span>
                  </div>

                  <div class="card-body">
                      <form method="POST" action="{{ route('finance.show') }}" id="TransactionUpdateForm">
                          @csrf

                          <div class="form-group row">
                              <label for="name" class="col-md-2 col-form-label text-md-left">{{ __('Details') }}</label>

                              <div class="col-md-8">
                                {{ $current_transaction->details }}
                              </div>
                          </div>

                          <div class="form-group row">
                              <label for="libelle" class="col-md-2 col-form-label text-md-left">{{ __('Libelle') }}</label>

                              <div class="col-md-8">
                                  <input id="libelle" type="text" class="form-control" name="libelle" value="{{ old('libelle') }}" required autofocus>

                                  @if ($errors->has('libelle'))
                                      <span class="help-block">
                                          <strong>{{ $errors->first('libelle') }}</strong>
                                      </span>
                                  @endif
                              </div>
                          </div>

                          <div class="form-group row">
                              <label for="catgeory" class="col-md-2 col-form-label text-md-left">{{ __('Categorie') }}</label>

                              <div class="col-md-8">
                                <input type="hidden" name="category_id" id="category_id" value="" />
                                <input type="hidden" name="record_id" value="{{ $current_transaction->id }}" />
                                <input type="hidden" name="offset" value="{{ $offset }}" />
                                @foreach ($all_category as $category)
                                      <button type="button" value="{{ $category->id }}" class="btn" style="background-color:{{ $category->getColor() }};color:white;font-size:14px;margin-top:3px;">{{ $category->nom }}</button>
                                @endforeach
                              </div>
                          </div>
                          <div class="form-group row mb-0">
                              <div class="col-md-6 offset-md-4">
                                <a class="btn btn-xs btn-danger" data-button-type="delete"
                                   href="{{ url('finance/delete/'.$current_transaction->id) }}"><i class="fa fa-trash-o"></i>
                                    Delete</a>
                              </div>
                          </div>

                      </form>
                  </div>
              </div>
          </div>
      </div>
  </div>
  @else
    <div class="alert alert-warning" role="alert">
      No transaction to update, please upload file transaction <a href="{{ url('finance/import')}}" class="alert-link">here</a>
    </div>
  @endif
</section>
@endsection
