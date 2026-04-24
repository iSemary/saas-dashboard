@extends('layouts.landlord.app')
@section('content')
    <div class="card">
        <div class="card-header">
            @include('layouts.shared.filter-date')
        </div>
        <div class="card-body">
            <table id="table" data-route="{{ route('landlord.plans.index') }}"
                class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">@translate('name')</th>
                        <th scope="col">@translate('slug')</th>
                        <th scope="col">@translate('description')</th>
                        <th scope="col">@translate('sort_order')</th>
                        <th scope="col">@translate('popular')</th>
                        <th scope="col">@translate('subscriptions')</th>
                        <th scope="col">@translate('active_subscriptions')</th>
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
    <script src="{{ asset('assets/landlord/js/subscriptions/plans/index.js') }}"></script>
@endsection
