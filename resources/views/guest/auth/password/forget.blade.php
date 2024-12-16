@extends('layouts.guest.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center">Forgot Password</h2>
                <div class="card">
                    <div class="card-body">
                        @if (!isset($tenant))
                            <form id="organizationForm" method="POST" action="{{ route('organization.check') }}"
                                class="login100-form validate-form flex-sb flex-w">
                                <span class="txt1 p-b-11">
                                    Organization Name
                                </span>
                                <div class="wrap-input100 validate-input m-b-36"
                                    data-validate="Organization Name is required">
                                    <input class="input100" type="text" name="organization_name" id="organizationName">
                                    <span class="focus-input100"></span>
                                </div>

                                <div class="container-login100-form-btn">
                                    <button type="submit" class="login100-form-btn">
                                        Next
                                    </button>
                                </div>
                            </form>
                        @endif
                        <form id="forgetPasswordForm" action="{{ route('password.forget.submit') }}" method="POST"
                            style="{{ isset($tenant) && !empty($tenant) ? 'display: block;' : 'display: none;' }}">
                            @csrf
                            <div class="wrap-input100 d-none validate-input m-b-36" data-validate="Organization name is required">
                                <input class="input100" type="text"
                                    value="{{ isset($tenant) && !empty($tenant) ? $tenant : '' }}"
                                    id="forgetPasswordOrganizationName" name="login_organization_name" readonly>
                                <span class="focus-input100"></span>
                            </div>
                            <div class="mb-3">
                                <div class="wrap-input100 validate-input m-b-36" data-validate="Email Address is required">
                                    <input type="email" id="email" name="email" class="input100"
                                        placeholder="Enter your email address" required>
                                    <span class="focus-input100"></span>
                                </div>
                            </div>
                            <button type="submit" class="login100-form-btn w-100">Send Password Reset Link</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('assets/guest/js/auth/password/forget.js') }}"></script>
@endsection
