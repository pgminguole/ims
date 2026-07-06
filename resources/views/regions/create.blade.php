@extends('layouts.app')

@section('title', 'Regions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Regions</h5>
                    <a href="{{ route('regions.create') }}" class="btn btn-primary btn-sm float-right">
                        <i class="fas fa-plus"></i> Add Region
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($regions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Description</th>
                                        <th>Courts</th>
                                        <th>Locations</th>
                                        <th>Assets</th>
                                        <th>Users</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($regions as $region)
                                        <tr>
                                            <td>{{ $region->name }}</td>
                                            <td><code>{{ $region->code }}</code></td>
                                            <td>{{ Str::limit($region->description, 50) }}</td>
                                            <td>{{ $region->courts_count }}</td>
                                            <td>{{ $region->locations_count }}</td>
                                            <td>{{ $region->assets_count }}</td>
                                            <td>{{ $region->users_count }}</td>
                                            <td>
                                                <span class="badge badge-{{ $region->is_active ? 'success' : 'secondary' }}">
                                                    {{ $region->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('regions.edit', $region) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Edit Region">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            {{ $regions->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <p class="mb-0">No regions found. <a href="{{ route('regions.create') }}">Create the first region</a>.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection