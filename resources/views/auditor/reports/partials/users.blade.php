<!-- Users Report -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-semibold">
            <i class="fas fa-users me-2"></i>Users Summary
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 bg-primary bg-opacity-10">
                    <div class="card-body text-center">
                        <h3 class="fw-bold text-primary">{{ $summary['total_users'] }}</h3>
                        <p class="text-muted mb-0">Total Users</p>
                    </div>
                </div>
            </div>
            @foreach($summary['by_status'] as $status => $count)
            <div class="col-md-3">
                <div class="card border-0 bg-light">
                    <div class="card-body text-center">
                        <h3 class="fw-bold text-dark">{{ $count }}</h3>
                        <p class="text-muted mb-0">{{ ucfirst($status) }} Users</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Users Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="usersTable">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Region</th>
                        <th>Court</th>
                        <th>Status</th>
                        <th>Assets Assigned</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? 'N/A' }}</td>
                        <td>{{ $user->role->name ?? 'N/A' }}</td>
                        <td>{{ $user->region->name ?? 'N/A' }}</td>
                        <td>{{ $user->court->name ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td>{{ $user->assignedAssets->count() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No users found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>