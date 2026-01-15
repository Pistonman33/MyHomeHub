@extends('layouts.html')
@section('content')
<section>
  <div class="container">
      <div class="row justify-content-center">
          <div class="col-md-8">
              <div class="card">
                  <div class="card-header">Home Charges</div>

                  <div class="card-body">
                      <form method="POST" action="{{ route('charge.save') }}">
                          @csrf

                          <div class="form-group row">
                              <label for="date" class="col-md-4 col-form-label text-md-right">{{ __('Date') }}</label>

                              <div class="col-md-3">
                                  <input id="name" type="text" class="form-control{{ $errors->has('date') ? ' is-invalid' : '' }}" name="date" value="{{ old('date',date("01/m/Y", strtotime("-1 month", time()))) }}" required autofocus>

                                  @if ($errors->has('date'))
                                      <span class="invalid-feedback">
                                          <strong>{{ $errors->first('date') }}</strong>
                                      </span>
                                  @endif
                              </div>
                          </div>

                          <div class="form-group row">
                              <label for="gaz" class="col-md-4 col-form-label text-md-right">{{ __('Gaz') }}</label>

                              <div class="col-md-3">
                                  <input type="hidden" id="previous_gaz" value="{{ $previous_charge->gaz_conso}}" />
                                  <input id="gaz" type="text" class="form-control{{ $errors->has('gaz') ? ' is-invalid' : '' }}" name="gaz" value="{{ old('gaz') }}" required placeholder="{{ $previous_charge->gaz_conso}}">
                                    <span class="help-feedback" id="gaz_eval">
                                    </span>
                                  @if ($errors->has('gaz'))
                                      <span class="invalid-feedback">
                                          <strong>{{ $errors->first('gaz') }}</strong>
                                      </span>
                                  @endif
                              </div>
                              <label class="col-md-2 col-form-label text-md-left">m<sup>3</sup></label>

                          </div>

                          <div class="form-group row">
                              <input type="hidden" id="previous_elec" value="{{ $previous_charge->elec_conso}}" />
                              <label for="elec" class="col-md-4 col-form-label text-md-right">{{ __('Elec') }}</label>

                              <div class="col-md-3">
                                  <input id="elec" type="text" class="form-control{{ $errors->has('elec') ? ' is-invalid' : '' }}" name="elec" value="{{ old('elec') }}" required placeholder="{{ $previous_charge->elec_conso}}">
                                  <span class="help-feedback" id="elec_eval">
                                  </span>

                                  @if ($errors->has('elec'))
                                      <span class="invalid-feedback">
                                          <strong>{{ $errors->first('elec') }}</strong>
                                      </span>
                                  @endif
                              </div>
                              <label class="col-md-2 col-form-label text-md-left">kwh</label>
                          </div>

                          <div class="form-group row">
                              <input type="hidden" id="previous_water" value="{{ $previous_charge->eau_conso}}" />
                              <label for="water" class="col-md-4 col-form-label text-md-right">{{ __('Water') }}</label>

                              <div class="col-md-3">
                                  <input id="water" type="text" class="form-control{{ $errors->has('water') ? ' is-invalid' : '' }}" name="water" required placeholder="{{ $previous_charge->eau_conso}}">
                                  <span class="help-feedback" id="water_eval">
                                  </span>

                                  @if ($errors->has('water'))
                                      <span class="invalid-feedback">
                                          <strong>{{ $errors->first('water') }}</strong>
                                      </span>
                                  @endif
                              </div>
                              <label class="col-md-2 col-form-label text-md-left">m<sup>3</sup></label>
                          </div>

                          <div class="form-group row mb-0">
                              <div class="col-md-6 offset-md-4">
                                  <button type="submit" class="btn btn-primary">
                                      {{ __('Save') }}
                                  </button>
                              </div>
                          </div>
                      </form>
                  </div>
              </div>
          </div>
      </div>
  </div>
</section>
@endsection
