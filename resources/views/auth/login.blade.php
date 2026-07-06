@extends('auth.layouts.app')

@section('title', 'Sign In')

@section('content')
    <div class="auth-title">Sign In</div>
    <div class="auth-subtitle">Welcome back! Please enter your details.</div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="form-label">{{ __('Email Address') }}</label>
            <input id="email" class="form-control-custom @error('email') is-invalid @enderror" 
                   type="email" name="email" value="{{ old('email') }}" 
                   placeholder="Enter your email" required autofocus autocomplete="email">
            @error('email')
                <div class="invalid-feedback text-danger mt-1 small">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <div class="position-relative">
                <input id="password" class="form-control-custom @error('password') is-invalid @enderror" 
                       type="password" name="password" 
                       placeholder="••••••••" required autocomplete="current-password">
                <button type="button" class="password-toggle" onclick="togglePassword()">
                    <i class="fa fa-eye" id="toggleIcon"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback text-danger mt-1 small">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Remember Me -->
        <div class="form-group d-flex align-items-center mb-1">
            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500" name="remember">
            <label for="remember_me" class="ms-2 text-sm text-muted" style="font-size: 0.875rem;">{{ __('Keep me signed in') }}</label>
        </div>

        <button type="submit" class="btn-submit">
            {{ __('Sign In') }}
        </button>

        <div class="text-center mt-3">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="small text-decoration-none text-muted">Forgot your password?</a>
            @endif
        </div>
    </form>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
@endsection
