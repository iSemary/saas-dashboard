@extends('layouts.guest.app')
@section('content')
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100 p-4">
                @if (!isset($tenant))
                    <form id="organizationForm" method="POST" action="{{ route('organization.check') }}"
                        class="login100-form validate-form flex-sb flex-w">
                        <span class="login100-form-title p-b-32">
                            Enter Organization Name
                        </span>
                        <span class="txt1 p-b-11">
                            Organization Name
                        </span>
                        <div class="wrap-input100 validate-input m-b-36" data-validate="Organization Name is required">
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
                <form id="loginForm" action="{{ route('login.submit') }}" class="login100-form validate-form flex-sb flex-w"
                    method="POST" style="{{ isset($tenant) && !empty($tenant) ? 'display: block;' : 'display: none;' }}">
                    <span class="login100-form-title p-b-32">
                        Account Login
                    </span>
                    <span class="txt1 p-b-11">
                        Organization
                    </span>
                    <div class="wrap-input100 validate-input m-b-36" data-validate="Organization name is required">
                        <input class="input100" type="text"
                            value="{{ isset($tenant) && !empty($tenant) ? $tenant : '' }}" id="loginOrganizationName"
                            name="login_organization_name" readonly>
                        <span class="focus-input100"></span>
                    </div>
                    <span class="txt1 p-b-11">
                        Username
                    </span>
                    <div class="wrap-input100 validate-input m-b-36" data-validate="Username is required">
                        <input class="input100" type="text" id="username" name="username">
                        <span class="focus-input100"></span>
                    </div>
                    <span class="txt1 p-b-11">
                        Password
                    </span>
                    <div class="wrap-input100 validate-input m-b-12" data-validate="Password is required">
                        <span class="btn-show-pass">
                            <i class="fa fa-eye"></i>
                        </span>
                        <input class="input100" type="password" id="password" name="password">
                        <span class="focus-input100"></span>
                    </div>
                    <div class="flex-sb-m w-full p-b-48">
                        <div class="contact100-form-checkbox">
                            <input class="input-checkbox100" id="ckb1" type="checkbox" id="rememberMe"
                                name="remember_me">
                            <label class="label-checkbox100" for="ckb1">
                                Remember me
                            </label>
                        </div>

                        <div>
                            <a href="{{ route("password.forget.show") }}" class="txt3">
                                Forgot Password?
                            </a>
                        </div>
                    </div>
                    <div class="container-login100-form-btn">
                        <button type="submit" class="login100-form-btn">
                            Login
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('assets/guest/js/login.js') }}"></script>
@endsection
