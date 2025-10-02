@extends('layouts.landlord.app')

@section('title', 'Create Your Brand')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Progress Bar -->
            <div class="card border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Step 1 of 3</span>
                        <span class="text-muted">33% Complete</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 33%"></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h2 class="mb-0">
                        <i class="fas fa-building me-2"></i>Create Your Brand
                    </h2>
                    <p class="mb-0 opacity-75">Tell us about your business</p>
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

                    <form action="{{ route('onboarding.store-brand') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="name" class="form-label fw-bold">
                                    <i class="fas fa-tag text-primary me-2"></i>Brand Name *
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="Enter your brand name"
                                       required>
                                <div class="form-text">This will be the main identifier for your business</div>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-12 mb-4">
                                <label for="description" class="form-label fw-bold">
                                    <i class="fas fa-align-left text-primary me-2"></i>Description
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="4" 
                                          placeholder="Describe what your business does...">{{ old('description') }}</textarea>
                                <div class="form-text">Help others understand your business (optional)</div>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="country_code" class="form-label fw-bold">
                                    <i class="fas fa-globe text-primary me-2"></i>Primary Country *
                                </label>
                                <select class="form-select form-select-lg @error('country_code') is-invalid @enderror" 
                                        id="country_code" 
                                        name="country_code" 
                                        required>
                                    <option value="">Select your country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->code }}" 
                                                {{ old('country_code') == $country->code ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">This helps us provide localized features</div>
                                @error('country_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="industry" class="form-label fw-bold">
                                    <i class="fas fa-industry text-primary me-2"></i>Industry
                                </label>
                                <select class="form-select form-select-lg @error('industry') is-invalid @enderror" 
                                        id="industry" 
                                        name="industry">
                                    <option value="">Select industry (optional)</option>
                                    <option value="technology" {{ old('industry') == 'technology' ? 'selected' : '' }}>Technology</option>
                                    <option value="healthcare" {{ old('industry') == 'healthcare' ? 'selected' : '' }}>Healthcare</option>
                                    <option value="finance" {{ old('industry') == 'finance' ? 'selected' : '' }}>Finance</option>
                                    <option value="education" {{ old('industry') == 'education' ? 'selected' : '' }}>Education</option>
                                    <option value="retail" {{ old('industry') == 'retail' ? 'selected' : '' }}>Retail</option>
                                    <option value="manufacturing" {{ old('industry') == 'manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                    <option value="services" {{ old('industry') == 'services' ? 'selected' : '' }}>Services</option>
                                    <option value="other" {{ old('industry') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('industry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('onboarding.welcome') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-arrow-right me-2"></i>Continue to Plan Selection
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
