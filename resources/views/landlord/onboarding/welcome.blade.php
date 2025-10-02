@extends('layouts.landlord.app')

@section('title', 'Welcome to Your SaaS Journey')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-rocket text-primary" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h1 class="display-4 text-primary mb-3">Welcome, {{ $user->name }}! 🎉</h1>
                    <p class="lead text-muted mb-4">
                        Congratulations on joining our platform! We're excited to help you build and grow your business.
                    </p>
                    
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-building text-info mb-2" style="font-size: 2rem;"></i>
                                    <h5>Create Your Brand</h5>
                                    <p class="text-muted small">Set up your business identity</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-gift text-success mb-2" style="font-size: 2rem;"></i>
                                    <h5>Choose Your Plan</h5>
                                    <p class="text-muted small">Start with a free trial</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-puzzle-piece text-warning mb-2" style="font-size: 2rem;"></i>
                                    <h5>Select Modules</h5>
                                    <p class="text-muted small">Customize your experience</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>What's Next?</strong> We'll guide you through a simple 3-step process to get your account ready. 
                        This will only take a few minutes!
                    </div>
                    
                    <a href="{{ route('onboarding.create-brand') }}" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-arrow-right me-2"></i>Get Started
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
