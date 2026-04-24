@extends('layouts.landlord.app')
@section('content')
    <div class="card">
        <div class="card-header">
            @include('layouts.shared.filter-date')
        </div>
        <div class="card-body">
            <table id="table" data-route="{{ route('landlord.payment-methods.index') }}"
                class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">@translate('name')</th>
                        <th scope="col">@translate('processor_type')</th>
                        <th scope="col">@translate('gateway_name')</th>
                        <th scope="col">@translate('supported_currencies')</th>
                        <th scope="col">@translate('is_global')</th>
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
    <script src="{{ asset('assets/landlord/js/payment/payment-methods/index.js') }}"></script>
@endsection
