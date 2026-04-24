@extends('layouts.landlord.app')
@section('content')
    <div class="card">
        <div class="card-header">
            @include('layouts.shared.filter-date')
        </div>
        <div class="card-body">
            <table id="table" data-route="{{ route('landlord.subscriptions.index') }}"
                class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">@translate('subscription_id')</th>
                        <th scope="col">@translate('brand')</th>
                        <th scope="col">@translate('user')</th>
                        <th scope="col">@translate('plan')</th>
                        <th scope="col">@translate('price')</th>
                        <th scope="col">@translate('billing_cycle')</th>
                        <th scope="col">@translate('next_billing')</th>
                        <th scope="col">@translate('days_remaining')</th>
                        <th scope="col">@translate('status')</th>
                        <th scope="col">@translate('action')</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/landlord/js/subscriptions/subscriptions/index.js') }}"></script>
@endsection
