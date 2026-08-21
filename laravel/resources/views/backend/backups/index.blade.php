@extends('backend.layouts.html')
@section('content')
    <section class="admin-page admin-backups-page">
        <div class="admin-page-header">
            <div><span class="admin-eyebrow">Management</span>
                <h1 class="admin-page-title">Gestion des sauvegardes</h1>
                <p class="admin-page-subtitle">Créez, téléchargez ou restaurez les sauvegardes de votre application.</p>
            </div>
            <a id="create-new-backup-button" href="{{ url('admin/backup/create') }}" class="admin-primary-action"><i
                    class="fa fa-plus"></i><span>Nouvelle sauvegarde</span></a>
        </div>
        <div class="admin-panel">
            <div class="row">
                <div class="col-xs-12">
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    @if (count($backups))
                        <table class="table table-striped admin-data-table">
                            <thead>
                                <tr>
                                    <th>File</th>
                                    <th>Size</th>
                                    <th>Date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($backups as $backup)
                                    <tr>
                                        <td>{{ $backup['file_name'] }}</td>
                                        <td>{{ $backup['file_size'] }}</td>
                                        <td>
                                            {{ $backup['last_modified'] }}
                                        </td>
                                        <td class="text-right">
                                            <a class="btn btn-xs btn-warning"
                                                href="{{ url('admin/backup/restore/' . $backup['file_name']) }}"><i
                                                    class="fa fa-database"></i> Restore</a>
                                            <a class="btn btn-xs btn-primary"
                                                href="{{ url('admin/backup/download/' . $backup['file_name']) }}"><i
                                                    class="fa fa-cloud-download"></i> Download</a>
                                            <a class="btn btn-xs btn-danger" data-button-type="delete"
                                                href="{{ url('admin/backup/delete/' . $backup['file_name']) }}"><i
                                                    class="fa fa-trash"></i>
                                                Delete</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="well">
                            <h4>Aucune sauvegarde disponible</h4>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
