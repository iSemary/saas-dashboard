@extends('layouts.landlord.app')
@section('content')
    <div class="row">
        <!-- Overview Cards -->
        <div class="col-12 mb-4">
            <div class="row" id="overview-cards">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">@translate('total_transactions')</h6>
                                    <h3 class="mb-0" id="total-transactions">-</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-credit-card fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">@translate('success_rate')</h6>
                                    <h3 class="mb-0" id="success-rate">-</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">@translate('total_volume')</h6>
                                    <h3 class="mb-0" id="total-volume">-</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">@translate('net_volume')</h6>
                                    <h3 class="mb-0" id="net-volume">-</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-chart-line fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Period Selector -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="card-title mb-0">@translate('payment_analytics')</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary period-btn active" data-period="7d">7 @translate('days')</button>
                                    <button type="button" class="btn btn-outline-primary period-btn" data-period="30d">30 @translate('days')</button>
                                    <button type="button" class="btn btn-outline-primary period-btn" data-period="90d">90 @translate('days')</button>
                                    <button type="button" class="btn btn-outline-primary period-btn" data-period="1y">1 @translate('year')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction Trends Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">@translate('transaction_trends')</h6>
                </div>
                <div class="card-body">
                    <canvas id="transaction-trends-chart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Payment Method Performance -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">@translate('payment_method_performance')</h6>
                </div>
                <div class="card-body">
                    <canvas id="payment-method-chart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Currency Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">@translate('currency_distribution')</h6>
                </div>
                <div class="card-body">
                    <canvas id="currency-distribution-chart" height="150"></canvas>
                </div>
            </div>
        </div>

        <!-- System Health -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">@translate('system_health')</h6>
                </div>
                <div class="card-body">
                    <div id="system-health-status">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">@translate('recent_transactions')</h6>
                        <a href="{{ route('landlord.payment-transactions.index') }}" class="btn btn-sm btn-outline-primary">
                            @translate('view_all')
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm" id="recent-transactions-table">
                            <thead>
                                <tr>
                                    <th>@translate('transaction_id')</th>
                                    <th>@translate('amount')</th>
                                    <th>@translate('currency')</th>
                                    <th>@translate('payment_method')</th>
                                    <th>@translate('status')</th>
                                    <th>@translate('created_at')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Failure Analysis -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">@translate('failure_analysis')</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <h6>@translate('top_error_codes')</h6>
                            <div id="error-codes-list">
                                <div class="text-center">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <h6>@translate('failures_by_gateway')</h6>
                            <div id="gateway-failures-list">
                                <div class="text-center">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/shared/plugins/chart.js/chart.min.js') }}"></script>
    <script src="{{ asset('assets/landlord/js/payment/analytics/index.js') }}"></script>
@endsection
