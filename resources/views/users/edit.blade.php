@extends('layouts.app')

@section('content')
<div class="container-fluid p-xl-5">
    <div class="row mb-4">
        <div class="col">
            <h1 class="text-heading mb-0">Edit User</h1>
            <p class="text-muted">Update user information</p>
        </div>
        <div class="col-auto">
            <div class="btn-group-compact">
                <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary btn-compact">
                    <i class="fas fa-eye"></i> View
                </a>
                <a href="{{ route('users') }}" class="btn btn-outline-secondary btn-compact">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('POST')
        
        <!-- Basic Information -->
        <div class="asset-form-section">
            <div class="section-header">Basic Information</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">First Name *</label>
                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                           name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Last Name *</label>
                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                           name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Username </label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror" 
                           name="username" value="{{ old('username', $user->username) }}" >
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           name="email" value="{{ old('email', $user->email) }}" >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                           name="phone" value="{{ old('phone', $user->phone) }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Security & Access -->
        <div class="asset-form-section">
            <div class="section-header">Security & Access</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           name="password" placeholder="Leave blank to keep current password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                           name="password_confirmation">
                </div>

                 <div class="form-group">
                    <label class="form-label">Role/User Type *</label>
                    <select class="form-select @error('role_id') is-invalid @enderror" name="role_id" required>
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

               
            </div>
        </div>

        <!-- Location & Assignment -->
        <div class="asset-form-section">
            <div class="section-header">Location & Assignment</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Region</label>
                    <select class="form-select @error('region_id') is-invalid @enderror" name="region_id" id="region_id">
                        <option value="">Select Region</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ old('region_id', $user->court->region_id ?? '') == $region->id ? 'selected' : '' }}>
                                {{ $region->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('region_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Court</label>
                    <select class="form-select @error('court_id') is-invalid @enderror" name="court_id" id="court_id">
                        <option value="">Select Court</option>
                        @foreach($courts as $court)
                            <option value="{{ $court->id }}" {{ old('court_id', $user->court_id) == $court->id ? 'selected' : '' }}>
                                {{ $court->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('court_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                 <div class="form-group">
                    <label class="form-label">Location </label>
                    <select class="form-select @error('location_id') is-invalid @enderror" name="location_id">
                        <option value="">Select Location</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('location_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Status & Settings -->
        <div class="asset-form-section">
            <div class="section-header">Status & Settings</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Status *</label>
                    <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                        <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Require Password Reset</label>
                    <select class="form-select @error('require_password_reset') is-invalid @enderror" name="require_password_reset">
                        <option value="0" {{ old('require_password_reset', $user->require_password_reset) == '0' ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('require_password_reset', $user->require_password_reset) == '1' ? 'selected' : '' }}>Yes</option>
                    </select>
                    @error('require_password_reset')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Account Expiry Date</label>
                    <input type="date" class="form-control @error('expire_date') is-invalid @enderror" 
                           name="expire_date" value="{{ old('expire_date', $user->expire_date?->format('Y-m-d')) }}">
                    @error('expire_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Invited By</label>
                    <select class="form-select @error('invited_by') is-invalid @enderror" name="invited_by">
                        <option value="">Select User</option>
                        @foreach($allUsers as $inviter)
                            <option value="{{ $inviter->id }}" {{ old('invited_by', $user->invited_by) == $inviter->id ? 'selected' : '' }}>
                                {{ $inviter->full_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('invited_by')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary btn-compact">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary btn-compact">
                <i class="fas fa-save"></i> Update User
            </button>
        </div>
    </form>
</div>
@endsection