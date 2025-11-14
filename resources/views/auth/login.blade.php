@extends('auth.layouts.app')

@section('title', 'Sign in to your portal')

@section('content')
    <span class="text-center text-white font-extrabold mb-3">Login to Portal</span>

    <div class="card">
        <div class="card-body p-4">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
    
                <div>
                    <x-input-label for="email" class="text-hurricane" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full form-control" type="email" name="email"
                        :value="old('email')" required autofocus autocomplete="email" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" class="text-hurricane" :value="__('Password')" />

                    <x-text-input id="password" class="block mt-1 w-full form-control" type="password" name="password"
                        required autocomplete="current-password" />

                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger" />
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center text-hurricane">
                        <input id="remember_me" type="checkbox" class="" name="remember">
                        <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="d-flex items-center justify-end flex-column mt-4">
                 {{--    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif --}}

                    <x-primary-button class="mt-3 btn btn-primary">
                        {{ __('Log in') }}
                    </x-primary-button>
{{--                    <span class="text-center mt-1">OR</span>--}}
{{--                    <a href="{{ route('auth.sso') }}" class="mt-1 btn btn-secondary"> Login with jOauth</a>--}}
                </div>
            </form>
        </div>
    </div>
@endsection
