@extends('layouts.landlord.app')

@section('title', 'Select Your Modules')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Progress Bar -->
            <div class="card border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Step 3 of 3</span>
                        <span class="text-muted">100% Complete</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-lg border-0">
                <div class="card-header bg-warning text-dark text-center py-4">
                    <h2 class="mb-0">
                        <i class="fas fa-puzzle-piece me-2"></i>Customize Your Experience
                    </h2>
                    <p class="mb-0">Select the modules that best fit {{ $brand->name }}'s needs</p>
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

                    <div class="alert alert-info mb-4">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Pro Tip:</strong> You can always add or remove modules later from your dashboard settings.
                    </div>

                    <form action="{{ route('onboarding.store-modules') }}" method="POST" id="moduleForm">
                        @csrf
                        
                        <div class="row">
                            @foreach($availableModules as $key => $module)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 module-card border-2" data-module="{{ $key }}">
                                        <div class="card-body text-center">
                                            <div class="mb-3">
                                                <i class="{{ $module['icon'] }} text-primary" style="font-size: 3rem;"></i>
                                            </div>
                                            
                                            <h5 class="card-title text-primary">{{ $module['name'] }}</h5>
                                            <p class="text-muted mb-3">{{ $module['description'] }}</p>
                                            
                                            <!-- Features List -->
                                            <ul class="list-unstyled text-start small">
                                                @foreach($module['features'] as $feature)
                                                    <li class="mb-1">
                                                        <i class="fas fa-check text-success me-2"></i>{{ $feature }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                            
                                            <div class="form-check mt-3">
                                                <input class="form-check-input module-checkbox" 
                                                       type="checkbox" 
                                                       name="modules[]" 
                                                       id="module_{{ $key }}" 
                                                       value="{{ $key }}">
                                                <label class="form-check-label fw-bold" for="module_{{ $key }}">
                                                    Select This Module
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Selected Modules Summary -->
                        <div class="card bg-light mt-4" id="selectedSummary" style="display: none;">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-check-circle text-success me-2"></i>Selected Modules
                                </h6>
                                <div id="selectedModulesList" class="d-flex flex-wrap gap-2"></div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('onboarding.select-plan') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                            <button type="submit" class="btn btn-warning btn-lg px-5" id="completeBtn" disabled>
                                <i class="fas fa-rocket me-2"></i>Complete Setup
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.module-card {
    cursor: pointer;
    transition: all 0.3s ease;
}

.module-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.module-card.selected {
    border-color: #ffc107 !important;
    background-color: #fff8e1;
    box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.25);
}

.module-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    background-color: #ffc107;
    color: #000;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const moduleCards = document.querySelectorAll('.module-card');
    const moduleCheckboxes = document.querySelectorAll('.module-checkbox');
    const completeBtn = document.getElementById('completeBtn');
    const selectedSummary = document.getElementById('selectedSummary');
    const selectedModulesList = document.getElementById('selectedModulesList');
    
    const moduleNames = {
        'hr': 'Human Resources',
        'crm': 'CRM',
        'surveys': 'Surveys & Feedback',
        'inventory': 'Inventory Management',
        'accounting': 'Accounting & Finance',
        'project_management': 'Project Management'
    };
    
    function updateSelectedModules() {
        const selectedModules = Array.from(moduleCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
        
        if (selectedModules.length > 0) {
            completeBtn.disabled = false;
            selectedSummary.style.display = 'block';
            
            selectedModulesList.innerHTML = selectedModules
                .map(module => `<span class="module-badge">${moduleNames[module]}</span>`)
                .join('');
        } else {
            completeBtn.disabled = true;
            selectedSummary.style.display = 'none';
        }
    }
    
    // Handle module card clicks
    moduleCards.forEach(card => {
        card.addEventListener('click', function() {
            const moduleKey = this.dataset.module;
            const checkbox = document.getElementById(`module_${moduleKey}`);
            
            // Toggle selection
            checkbox.checked = !checkbox.checked;
            
            // Update card appearance
            if (checkbox.checked) {
                this.classList.add('selected');
            } else {
                this.classList.remove('selected');
            }
            
            updateSelectedModules();
        });
    });
    
    // Handle checkbox changes
    moduleCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const moduleKey = this.value;
            const card = document.querySelector(`[data-module="${moduleKey}"]`);
            
            if (this.checked) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
            
            updateSelectedModules();
        });
    });
    
    // Pre-select popular modules (CRM and HR)
    ['crm', 'hr'].forEach(module => {
        const checkbox = document.getElementById(`module_${module}`);
        const card = document.querySelector(`[data-module="${module}"]`);
        
        if (checkbox && card) {
            checkbox.checked = true;
            card.classList.add('selected');
        }
    });
    
    // Initial update
    updateSelectedModules();
});
</script>
@endsection
