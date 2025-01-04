@extends('layouts.guest.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center">Reset Password</h2>
                <div class="card">
                    <div class="card-body">
                        <form id="resetPasswordForm" action="{{ route('password.reset.submit') }}" method="POST">
                            @csrf
                            <input type="hidden" id="token" name="token" value="{{ $token ?? '' }}" required>
                            <div class="mb-3">
                                <div class="wrap-input100 validate-input m-b-36" data-validate="Password is required">
                                    <input type="password" id="password" name="password" class="input100"
                                        placeholder="Enter new password" required>
                                    <span class="focus-input100"></span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="wrap-input100 validate-input m-b-36" data-validate="Password confirmation is required">
                                    <input type="password" id="passwordConfirmation" name="password_confirmation"
                                        class="input100" placeholder="Confirm your new password" required>
                                    <span class="focus-input100"></span>
                                </div>
                            </div>

                            <button type="submit" class="login100-form-btn w-100">Reset Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('assets/guest/js/auth/password/reset.js') }}"></script>
@endsection
