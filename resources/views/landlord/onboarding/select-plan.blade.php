@extends('layouts.landlord.app')

@section('title', 'Choose Your Plan')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Progress Bar -->
            <div class="card border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Step 2 of 3</span>
                        <span class="text-muted">66% Complete</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 66%"></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-lg border-0">
                <div class="card-header bg-success text-white text-center py-4">
                    <h2 class="mb-0">
                        <i class="fas fa-gift me-2"></i>Choose Your Plan
                    </h2>
                    <p class="mb-0 opacity-75">Start with a FREE trial for {{ $brand->name }}</p>
                </div>
                
                <div class="card-body p-5">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="alert alert-success mb-4">
                        <i class="fas fa-star me-2"></i>
                        <strong>Great News!</strong> All plans come with a FREE trial period. No credit card required to start!
                    </div>

                    <form action="{{ route('onboarding.store-plan') }}" method="POST" id="planForm">
                        @csrf
                        
                        <div class="row">
                            @foreach($plans as $plan)
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100 plan-card {{ $plan->is_popular ? 'border-warning' : 'border-light' }}" 
                                         data-plan-id="{{ $plan->id }}">
                                        @if($plan->is_popular)
                                            <div class="card-header bg-warning text-dark text-center py-2">
                                                <small class="fw-bold">
                                                    <i class="fas fa-crown me-1"></i>MOST POPULAR
                                                </small>
                                            </div>
                                        @endif
                                        
                                        <div class="card-body text-center">
                                            <h4 class="card-title text-primary">{{ $plan->name }}</h4>
                                            <p class="text-muted">{{ $plan->description }}</p>
                                            
                                            <!-- Trial Info -->
                                            @php
                                                $trial = $plan->getTrialFor();
                                                $trialDays = $trial ? $trial->trial_days : 14;
                                            @endphp
                                            
                                            <div class="mb-3">
                                                <div class="display-6 text-success fw-bold">FREE</div>
                                                <small class="text-muted">{{ $trialDays }}-day trial</small>
                                            </div>
                                            
                                            <!-- Features -->
                                            @if($plan->features->count() > 0)
                                                <ul class="list-unstyled text-start">
                                                    @foreach($plan->features->take(5) as $feature)
                                                        <li class="mb-2">
                                                            <i class="fas fa-check text-success me-2"></i>
                                                            {{ $feature->name }}
                                                            @if($feature->feature_type === 'numeric' && !$feature->is_unlimited)
                                                                <small class="text-muted">({{ $feature->display_value }})</small>
                                                            @elseif($feature->is_unlimited)
                                                                <small class="text-success">(Unlimited)</small>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                    @if($plan->features->count() > 5)
                                                        <li class="text-muted">
                                                            <small>+ {{ $plan->features->count() - 5 }} more features</small>
                                                        </li>
                                                    @endif
                                                </ul>
                                            @endif
                                            
                                            <div class="form-check mt-3">
                                                <input class="form-check-input plan-radio" 
                                                       type="radio" 
                                                       name="plan_id" 
                                                       id="plan_{{ $plan->id }}" 
                                                       value="{{ $plan->id }}"
                                                       {{ $plan->is_popular ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold" for="plan_{{ $plan->id }}">
                                                    Select This Plan
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Currency and Billing Cycle Selection -->
                        <div class="row mt-4">
                            <div class="col-md-6 mb-3">
                                <label for="currency_id" class="form-label fw-bold">
                                    <i class="fas fa-dollar-sign text-primary me-2"></i>Preferred Currency
                                </label>
                                <select class="form-select @error('currency_id') is-invalid @enderror" 
                                        id="currency_id" 
                                        name="currency_id" 
                                        required>
                                    <option value="">Select currency</option>
                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency->id }}" 
                                                {{ old('currency_id') == $currency->id ? 'selected' : '' }}>
                                            {{ $currency->code }} - {{ $currency->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('currency_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="billing_cycle" class="form-label fw-bold">
                                    <i class="fas fa-calendar text-primary me-2"></i>Billing Cycle (after trial)
                                </label>
                                <select class="form-select @error('billing_cycle') is-invalid @enderror" 
                                        id="billing_cycle" 
                                        name="billing_cycle" 
                                        required>
                                    <option value="">Select billing cycle</option>
                                    <option value="monthly" {{ old('billing_cycle') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="quarterly" {{ old('billing_cycle') == 'quarterly' ? 'selected' : '' }}>Quarterly (Save 10%)</option>
                                    <option value="annually" {{ old('billing_cycle') == 'annually' ? 'selected' : '' }}>Annually (Save 20%)</option>
                                </select>
                                @error('billing_cycle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('onboarding.create-brand') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                            <button type="submit" class="btn btn-success btn-lg px-5" id="continueBtn" disabled>
                                <i class="fas fa-arrow-right me-2"></i>Start FREE Trial
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.plan-card {
    cursor: pointer;
    transition: all 0.3s ease;
}

.plan-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.plan-card.selected {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const planCards = document.querySelectorAll('.plan-card');
    const planRadios = document.querySelectorAll('.plan-radio');
    const continueBtn = document.getElementById('continueBtn');
    
    // Handle plan card clicks
    planCards.forEach(card => {
        card.addEventListener('click', function() {
            const planId = this.dataset.planId;
            const radio = document.getElementById(`plan_${planId}`);
            
            // Clear all selections
            planCards.forEach(c => c.classList.remove('selected'));
            planRadios.forEach(r => r.checked = false);
            
            // Select this plan
            this.classList.add('selected');
            radio.checked = true;
            
            // Enable continue button
            continueBtn.disabled = false;
        });
    });
    
    // Handle radio button changes
    planRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                continueBtn.disabled = false;
                
                // Update card selection
                planCards.forEach(c => c.classList.remove('selected'));
                const card = document.querySelector(`[data-plan-id="${this.value}"]`);
                if (card) {
                    card.classList.add('selected');
                }
            }
        });
    });
    
    // Check if a plan is already selected
    const selectedRadio = document.querySelector('.plan-radio:checked');
    if (selectedRadio) {
        continueBtn.disabled = false;
        const card = document.querySelector(`[data-plan-id="${selectedRadio.value}"]`);
        if (card) {
            card.classList.add('selected');
        }
    }
});
</script>
@endsection
