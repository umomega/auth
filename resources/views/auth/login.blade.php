@extends('foundation::layouts.auth')

@section('pageTitle', __('auth::auth.login'))

@section('content')
    <form method="POST" action="{{ route('reactor.login') }}">
        @csrf

        @include('foundation::fields.text', ['name' => 'email', 'type' => 'email', 'attributes' => ['required', 'autofocus']])
        @include('foundation::fields.password', ['name' => 'password', 'attributes' => ['required']])

        <div class="level">
            <div class="level-left">
                <label class="toggle">
                    <input class="toggle-checkbox" type="checkbox"  name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span class="toggle-outer">
                        <span class="toggle-switch"></span>
                        <span class="toggle-true">{{ __('auth::auth.remember') }}</span>
                        <span class="toggle-false">{{ __('auth::auth.remember') }}</span>
                    </span>
                </label>
            </div>
            <div class="level-right">
                <button type="submit" name="submit" class="button is-primary is-uppercase">
                    <span>{{ __('auth::auth.login') }}</span>
                    <span class="icon-flap">
                        <i class="fas fa-sign-in-alt"></i>
                    </span>
                </button>
            </div>
        </div>
    </form>

    <div class="dialog-separator mt-md mb-md"></div>

    <div class="has-text-centered">
        <a class="auth-footer-link" href="{{ route('reactor.password.request') }}">
            {{ __('auth::passwords.forgot_your_password') }}
        </a>
    </div>
@endsection
