@extends('layouts.guest.app', ['header' => false, 'footer' => false])
@section('content')
    @auth
        <div class="container lock-screen">
            <div class="lock-screen-wrapper">
                <div class="lock-screen-logo">
                    <a href="{{ route('home') }}"><b>{{ env('APP_NAME') }}</b></a>
                </div>
                <div class="lock-screen-name">{{ auth()->user()->name }}</div>
                <div class="lock-screen-item mt-4">
                    <div class="lock-screen-image">
                        <img src="{{ auth()->user()->avatar }}" alt="User Image">
                    </div>
                    <form class="lock-screen-credentials" id="unlockForm" action="{{ route('unlock.submit') }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="@translate('password')" required>
                            <div class="input-group-append d-grid">
                                <button type="submit" class="btn submit-btn icon-container">
                                    <i class="fas fa-arrow-right text-muted"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-status text-center font-weight-bold my-1 font-14 position-absolute"></div>
                    </form>
                </div>
                <div class="help-block text-center mt-5">
                    @translate('enter_your_password_to_retrieve_your_session')
                </div>
                <div class="text-center">
                    <a style="cursor: pointer" data-form="logout-form" class="logout-btn">
                        @translate('or_sign_in_as_a_different_user')

                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
        <div class="d-none">
            <img src="{{ asset('assets/shared/images/icons/animated/loaders/loader.gif') }}" class="loading-icon" width="20px"
                height="20px" />
            <i class="fas fa-arrow-right text-muted submit-icon"></i>
        </div>

        <audio id="bgMusic" loop>
            <source src="{{ asset('assets/shared/sounds/autumn-halloween-ambience-piano-jazz.mp3') }}" type="audio/mp3">
        </audio>

        <div class="audio-container">
            <img src="{{ asset('assets/shared/images/icons/cd.png') }}" width="30px" height="30px" alt="cd" />
            <span>Autumn halloween ambience piano jazz</span>
            <button id="audioControl" class="text-muted">
                <i class="fas fa-volume-up"></i>
            </button>
        </div>

    @endauth
@endsection
@section('js')
    <script src="{{ asset('assets/guest/js/auth/lock.js') }}"></script>
@endsection
