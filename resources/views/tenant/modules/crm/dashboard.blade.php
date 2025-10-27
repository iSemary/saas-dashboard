@extends('layouts.tenant.app')

@section('title', translate('crm_dashboard'))

@section('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard/base.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard/crm.css') }}">
@endsection

@section('content')
<div class="container-fluid crm-dashboard">
    <!-- Welcome Section -->
    <div class="row mb-4 animate-on-scroll">
        <div class="col-12">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h2 class="heading-2 mb-0">@translate('welcome_to_crm_dashboard')</h2>
                    <div class="card-actions">
                        <button class="modern-btn modern-btn--ghost modern-btn--sm" data-tooltip="@translate('refresh_dashboard')">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="modern-card__body">
                    <p class="body-large text-muted mb-0">@translate('manage_your_customer_relationships_effectively')</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CRM Stats Cards -->
    <div class="stats-grid animate-on-scroll">
        <div class="stat-widget stat-widget--primary">
            <div class="stat-widget__header">
                <h3 class="stat-title">@translate('total_leads')</h3>
                <div class="stat-icon">
                    <i class="fas fa-user-friends"></i>
                </div>
            </div>
            <div class="stat-widget__content">
                <h3 class="stat-number">342</h3>
                <div class="stat-change stat-change--positive">
                    <i class="fas fa-arrow-up change-icon"></i>
                    <span>+15%</span>
                </div>
            </div>
            <div class="stat-widget__footer">
                <span class="stat-label">@translate('vs_last_month')</span>
                <a href="#" class="stat-link">@translate('view_details')</a>
            </div>
        </div>

        <div class="stat-widget stat-widget--success">
            <div class="stat-widget__header">
                <h3 class="stat-title">@translate('conversion_rate')</h3>
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div class="stat-widget__content">
                <h3 class="stat-number">23%</h3>
                <div class="stat-change stat-change--positive">
                    <i class="fas fa-arrow-up change-icon"></i>
                    <span>+5%</span>
                </div>
            </div>
            <div class="stat-widget__footer">
                <span class="stat-label">@translate('vs_last_month')</span>
                <a href="#" class="stat-link">@translate('view_details')</a>
            </div>
        </div>

        <div class="stat-widget stat-widget--warning">
            <div class="stat-widget__header">
                <h3 class="stat-title">@translate('active_deals')</h3>
                <div class="stat-icon">
                    <i class="fas fa-handshake"></i>
                </div>
            </div>
            <div class="stat-widget__content">
                <h3 class="stat-number">18</h3>
                <div class="stat-change stat-change--neutral">
                    <i class="fas fa-minus change-icon"></i>
                    <span>0%</span>
                </div>
            </div>
            <div class="stat-widget__footer">
                <span class="stat-label">@translate('vs_last_month')</span>
                <a href="#" class="stat-link">@translate('view_details')</a>
            </div>
        </div>

        <div class="stat-widget stat-widget--accent">
            <div class="stat-widget__header">
                <h3 class="stat-title">@translate('revenue')</h3>
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
            <div class="stat-widget__content">
                <h3 class="stat-number">$45K</h3>
                <div class="stat-change stat-change--positive">
                    <i class="fas fa-arrow-up change-icon"></i>
                    <span>+12%</span>
                </div>
            </div>
            <div class="stat-widget__footer">
                <span class="stat-label">@translate('vs_last_month')</span>
                <a href="#" class="stat-link">@translate('view_details')</a>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row animate-on-scroll">
        <div class="col-md-12">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="heading-4">@translate('quick_actions')</h3>
                </div>
                <div class="modern-card__body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="#" class="btn-app">
                                <i class="fas fa-user-plus"></i>
                                <span>@translate('add_lead')</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="#" class="btn-app">
                                <i class="fas fa-handshake"></i>
                                <span>@translate('new_deal')</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="#" class="btn-app">
                                <i class="fas fa-calendar"></i>
                                <span>@translate('schedule_meeting')</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="#" class="btn-app">
                                <i class="fas fa-chart-bar"></i>
                                <span>@translate('view_reports')</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize CRM dashboard
    if (window.modernDashboard) {
        window.modernDashboard.animateStats();
    }
});
</script>
@endsection
