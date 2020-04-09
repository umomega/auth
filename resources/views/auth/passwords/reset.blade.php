@extends('foundation::layouts.auth')

@section('pageTitle', __('auth::passwords.reset_password'))

@section('content')
    <form method="POST" action="{{ route('reactor.password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">
        @include('foundation::fields.text', ['name' => 'email', 'type' => 'email', 'attributes' => ['required', 'autofocus']])
        @include('foundation::fields.password', ['name' => 'password', 'attributes' => ['required']])
        @include('foundation::fields.password', ['name' => 'password_confirmation', 'attributes' => ['required']])

        <button type="submit" name="submit" class="button is-primary is-uppercase is-fullwidth">
            {{ __('auth::passwords.reset_password') }}
        </button>
    </form>

    <div class="dialog-separator mt-md mb-md"></div>

    <div class="has-text-centered">
        <a class="auth-footer-link" href="{{ route('reactor.login') }}">
            <i class="fa fa-arrow-left"></i> {{ __('auth::auth.back_to_login') }}
        </a>
    </div>
@endsection
