@extends('layouts.errors.app')
@section('content')
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-ban"></i>
        </div>
        
        <div class="error-code">403</div>
        
        <h1 class="error-title">@translate('error_pages.access_denied')</h1>
        
        <p class="error-message">
            {{ $exception->getMessage() !== null ? $exception->getMessage() : translate('error_pages.access_denied_message') }}
        </p>
        
        <div class="error-actions">
            <a href="/" class="btn btn-primary">
                <i class="fas fa-home"></i>
                @translate('error_pages.back_to_homepage')
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                @translate('error_pages.go_back')
            </a>
        </div>
    </div>
@endsection
