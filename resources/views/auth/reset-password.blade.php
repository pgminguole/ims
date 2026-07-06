@extends('auth.layouts.app')

@section('title', 'Reset your password.')


@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mt-5">
                <div class="card-header">
                    <h5 class="text-center">Password reset</h5>
                    <small class="text-info">Hi {{ Auth::user()->first_name }}, Kindly reset your password.</small> 
                    <small class="text-muted"> Choose a strong password to proceed</small>
                </div>
                <form method="POST" action="{{ route('dashboard.account.reset-password') }}">
                    @csrf   

                    <div class="card-body">
                        <div class="form-group mb-3 col-md-12">
                            <label for="current-password" class="control-label">Current password*</label>
                            <input type="password" name="current_password" class="form-control round   @error('current_password') is-invalid @enderror" id="current-password" placeholder="Current password" required autocomplete="current-password">
                            @error('current_password')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <hr>
                        <div class="form-group mb-3 col-md-12">
                            <label for="new-password" class="control-label">New password*</label>
                            <input type="password" name="new_password" class="form-control round   @error('new_password') is-invalid @enderror" id="new-password" placeholder="New Password" required autocomplete="new-password">
                            @error('new_password')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3 col-md-12">
                            <label for="new_password_confirmation" class="control-label">Confirm new password*</label>
                            <input type="password" name="new_password_confirmation" class="form-control round   @error('new_password_confirmation') is-invalid @enderror" id="new_password_confirmation" placeholder="Confirm new password" required>
                            @error('new_password_confirmation')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3 col-md-12 mt-4">
                            <button type="submit" class="btn btn-primary btn-round register_btn auth-btn has-spinner float-right" > <i class="fas fa-save"></i> Reset password</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
