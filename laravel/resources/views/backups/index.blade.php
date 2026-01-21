@extends('layouts.html')
@section('content')
<section>
  <div class="row">
      <div class="col-xs-12">
          <a id="create-new-backup-button" href="{{ url('admin/backup/create') }}" class="btn btn-primary pull-right"
             style="margin-bottom:2em;"><i
                  class="fa fa-plus"></i> Create New Backup
          </a>
      </div>
  </div>
  <div class="row">
      <div class="col-xs-12">
          @if (count($backups))

              <table class="table table-striped table-bordered">
                  <thead>
                  <tr>
                      <th>File</th>
                      <th>Size</th>
                      <th>Date</th>
                      <th></th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($backups as $backup)
                      <tr>
                          <td>{{ $backup['file_name'] }}</td>
                          <td>{{ $backup['file_size'] }}</td>
                          <td>
                              {{ $backup['last_modified'] }}
                          </td>
                          <td class="text-right">
                            <a class="btn btn-xs btn-warning"
                               href="{{ url('admin/backup/restore/'.$backup['file_name']) }}"><i
                                    class="fa fa-database"></i> Restore</a>
                              <a class="btn btn-xs btn-primary"
                                 href="{{ url('admin/backup/download/'.$backup['file_name']) }}"><i
                                      class="fa fa-cloud-download"></i> Download</a>
                              <a class="btn btn-xs btn-danger" data-button-type="delete"
                                 href="{{ url('admin/backup/delete/'.$backup['file_name']) }}"><i class="fa fa-trash"></i>
                                  Delete</a>
                          </td>
                      </tr>
                  @endforeach
                  </tbody>
              </table>
          @else
              <div class="well">
                  <h4>There are no backups</h4>
              </div>
          @endif
      </div>
  </div>
</section>
@endsection
