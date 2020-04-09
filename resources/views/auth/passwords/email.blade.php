@extends('foundation::layouts.auth')

@section('pageTitle', __('auth::passwords.reset_password'))

@section('content')
    @if(session('status'))
        @include('foundation::elements.notification', ['type' => 'success', 'message' => session('status')])
    @endif

    <form method="POST" action="{{ route('reactor.password.email') }}">
        @csrf

        @include('foundation::fields.text', ['name' => 'email', 'type' => 'email', 'attributes' => ['required', 'autofocus']])

        <button type="submit" name="submit" class="button is-primary is-uppercase is-fullwidth">
            {{ __('auth::passwords.send_reset_link') }}
        </button>
    </form>

    <div class="dialog-separator mt-md mb-md"></div>

    <div class="has-text-centered">
        <a class="auth-footer-link" href="{{ route('reactor.login') }}">
            <i class="fa fa-arrow-left"></i> {{ __('auth::auth.back_to_login') }}
        </a>
    </div>
@endsection
