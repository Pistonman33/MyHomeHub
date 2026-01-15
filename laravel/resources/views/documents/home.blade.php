@extends('layouts.html')
@section('content')
<section>
  <div class="row">
      <div class="col-xs-12">
          @if (count($documents))
              <table class="table table-striped table-bordered">
                  <thead>
                  <tr>
                      <th>File name</th>
                      <th>Extension</th>
                      <th>Size</th>
                      <th>Date</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($documents as $document)
                      <tr>
                          <td>{{ $document['name'] }}</td>
                          <td>{{ $document['extension'] }}</td>
                          <td>{{ $document['size'] }}</td>
                          <td>
                              {{ $document['last_modified'] }}
                          </td>
                          <td class="text-right">
                              <a class="btn btn-xs btn-primary"
                                 href="{{ $document['url'] }}"><i
                                      class="fa fa-cloud-download"></i> Download</a>
                          </td>
                      </tr>
                  @endforeach
                  </tbody>
              </table>
          @else
              <div class="well">
                  <h4>There are no documents</h4>
              </div>
          @endif
      </div>
  </div>
</section>
@endsection
