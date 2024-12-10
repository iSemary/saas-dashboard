@extends('layouts.guest.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center">Two-Factor Authentication</h2>
                <div class="card">
                    <div class="card-body">
                        <form id="twoFactorValidateForm" action="{{ route('2fa.check') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="otp" class="form-label">Enter OTP</label>
                                <div class="otp-inputs">
                                    <input class="otp-input" type="number" min="0" max="9" required>
                                    <input class="otp-input" type="number" min="0" max="9" required>
                                    <input class="otp-input" type="number" min="0" max="9" required>
                                    <input class="otp-input" type="number" min="0" max="9" required>
                                    <input class="otp-input" type="number" min="0" max="9" required>
                                    <input class="otp-input" type="number" min="0" max="9" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Validate OTP</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('assets/guest/js/auth/2fa/validate.js') }}"></script>
@endsection
