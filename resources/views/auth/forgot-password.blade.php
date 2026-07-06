@extends('auth.layouts.app')

@section('title', 'Reset your password.')


@section('content')

    <span class="text-center text-white mb-2">
        Forgot your password? No problem. <br>
        Just let us know your email address and we will email you <br> a
        password reset link that will allow you to choose a new one.'
    </span>
    <div class="card">
        <div class="card-body p-4">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4 text-success" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 form-control" type="email" name="email"
                        :value="old('email')" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger" />
                </div>
                <div class="d-flex items-center justify-end flex-column mt-4">


                    <x-primary-button class="mt-3 btn btn-primary">
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
@endsection
