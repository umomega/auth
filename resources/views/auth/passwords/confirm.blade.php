@extends('foundation::layouts.auth')

@section('pageTitle', __('auth::passwords.confirm_password'))

@section('content')
    <p class="is-size-7 has-text-centered mb-md">{{ __('auth::passwords.please_confirm') }}</p>
    <form method="POST" action="{{ route('reactor.password.confirm') }}">
        @csrf

        @include('foundation::fields.password', ['name' => 'password', 'attributes' => ['required']])

        <button type="submit" name="submit" class="button is-primary is-uppercase is-fullwidth">
            {{ __('auth::passwords.confirm_password') }}
        </button>
    </form>

    <div class="dialog-separator mt-md mb-md"></div>

    <div class="has-text-centered">
        <a class="auth-footer-link" href="{{ route('reactor.password.request') }}">
            {{ __('auth::passwords.forgot_your_password') }}
        </a>
    </div>
@endsection

@section('contentOld')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Confirm Password') }}</div>

                <div class="card-body">
                    {{ __('Please confirm your password before continuing.') }}

                    <form method="POST" action="{{ route('reactor.password.confirm') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Confirm Password') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
