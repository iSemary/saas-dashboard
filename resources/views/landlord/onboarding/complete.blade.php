@extends('layouts.landlord.app')

@section('title', 'Setup Complete!')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-5">
                    <!-- Success Animation -->
                    <div class="mb-4">
                        <div class="success-checkmark">
                            <div class="check-icon">
                                <span class="icon-line line-tip"></span>
                                <span class="icon-line line-long"></span>
                                <div class="icon-circle"></div>
                                <div class="icon-fix"></div>
                            </div>
                        </div>
                    </div>
                    
                    <h1 class="display-4 text-success mb-3">🎉 Congratulations!</h1>
                    <h3 class="text-primary mb-4">Your account is ready to go!</h3>
                    
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card bg-success text-white border-0 h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle mb-2" style="font-size: 2rem;"></i>
                                    <h6>Brand Created</h6>
                                    <small>Your business identity is set up</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-success text-white border-0 h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-gift mb-2" style="font-size: 2rem;"></i>
                                    <h6>Free Trial Started</h6>
                                    <small>Enjoy full access for 14 days</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-success text-white border-0 h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-puzzle-piece mb-2" style="font-size: 2rem;"></i>
                                    <h6>Modules Configured</h6>
                                    <small>Your workspace is customized</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mb-4">
                        <h5 class="alert-heading">
                            <i class="fas fa-rocket me-2"></i>What's Next?
                        </h5>
                        <ul class="list-unstyled mb-0 text-start">
                            <li class="mb-2">
                                <i class="fas fa-arrow-right text-primary me-2"></i>
                                Explore your personalized dashboard
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-arrow-right text-primary me-2"></i>
                                Invite team members to collaborate
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-arrow-right text-primary me-2"></i>
                                Configure your selected modules
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-arrow-right text-primary me-2"></i>
                                Import your existing data
                            </li>
                        </ul>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-question-circle text-primary mb-2" style="font-size: 2rem;"></i>
                                    <h6>Need Help?</h6>
                                    <p class="text-muted small mb-3">Check out our comprehensive guides and tutorials</p>
                                    <a href="#" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-book me-1"></i>View Documentation
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-headset text-info mb-2" style="font-size: 2rem;"></i>
                                    <h6>Get Support</h6>
                                    <p class="text-muted small mb-3">Our team is here to help you succeed</p>
                                    <a href="#" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-comments me-1"></i>Contact Support
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <form action="{{ route('onboarding.redirect-dashboard') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg px-5">
                            <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                        </button>
                    </form>
                    
                    <p class="text-muted mt-3">
                        <small>
                            <i class="fas fa-clock me-1"></i>
                            Your free trial expires in 14 days. We'll send you reminders before it ends.
                        </small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.success-checkmark {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: block;
    stroke-width: 2;
    stroke: #4bb71b;
    stroke-miterlimit: 10;
    margin: 10% auto;
    box-shadow: inset 0px 0px 0px #4bb71b;
    animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
    position: relative;
}

.success-checkmark .check-icon {
    width: 56px;
    height: 56px;
    position: absolute;
    left: 12px;
    top: 12px;
    border-radius: 50%;
    display: block;
    stroke-width: 2;
    stroke: #fff;
    stroke-miterlimit: 10;
    margin: 10% auto;
    box-shadow: inset 0px 0px 0px #4bb71b;
}

.success-checkmark .check-icon .icon-line {
    height: 3px;
    background-color: #4bb71b;
    display: block;
    border-radius: 2px;
    position: absolute;
    z-index: 10;
}

.success-checkmark .check-icon .icon-line.line-tip {
    top: 46%;
    left: 14px;
    width: 25px;
    transform: rotate(45deg);
    animation: icon-line-tip .75s;
}

.success-checkmark .check-icon .icon-line.line-long {
    top: 38%;
    right: 8px;
    width: 47px;
    transform: rotate(-45deg);
    animation: icon-line-long .75s;
}

.success-checkmark .check-icon .icon-circle {
    top: -4px;
    left: -4px;
    z-index: 10;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    position: absolute;
    box-sizing: content-box;
    border: 4px solid #4bb71b;
}

.success-checkmark .check-icon .icon-fix {
    top: 8px;
    width: 5px;
    left: 26px;
    z-index: 1;
    height: 85px;
    position: absolute;
    transform: rotate(-45deg);
    background-color: #fff;
}

@keyframes icon-line-tip {
    0% { width: 0; left: 1px; top: 19px; }
    54% { width: 0; left: 1px; top: 19px; }
    70% { width: 50px; left: -8px; top: 37px; }
    84% { width: 17px; left: 21px; top: 48px; }
    100% { width: 25px; left: 14px; top: 45px; }
}

@keyframes icon-line-long {
    0% { width: 0; right: 46px; top: 54px; }
    65% { width: 0; right: 46px; top: 54px; }
    84% { width: 55px; right: 0px; top: 35px; }
    100% { width: 47px; right: 8px; top: 38px; }
}

@keyframes fill {
    100% { box-shadow: inset 0px 0px 0px 60px #4bb71b; }
}

@keyframes scale {
    0%, 100% { transform: none; }
    50% { transform: scale3d(1.1, 1.1, 1); }
}
</style>
@endsection
