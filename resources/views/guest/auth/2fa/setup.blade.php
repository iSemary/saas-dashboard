@extends('layouts.guest.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center">Two-Factor Authentication Setup</h2>
                <div class="card">
                    <div class="card-body">
                        <form id="twoFactorSetupForm" action="{{ route('2fa.verify') }}" method="POST">
                            @csrf
                            <input type="hidden" id="secretKey" name="secret_key" value="{{ $secretKey ?? '' }}" required>
                            <!-- QR Code -->
                            <div class="mb-3">
                                <label for="qr_code" class="form-label">Scan the QR Code</label>
                                <div class="text-center">
                                    {!! $qrCode !!}
                                </div>
                            </div>

                            <!-- OTP Input -->
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

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary w-100">Verify OTP</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section("js")
<script src="{{ asset("assets/guest/js/auth/2fa/setup.js") }}"></script>
@endsection