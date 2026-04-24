@extends('layouts.tenant.app')

@section('content')
<div class="card">
    <div class="card-header">
        @include('layouts.shared.filter-date')
    </div>
    <div class="card-body">
        <table id="table" data-route="{{ route('tenant.brands.index') }}" class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">@translate('logo')</th>
                    <th scope="col">@translate('name')</th>
                    <th scope="col">@translate('slug')</th>
                    <th scope="col">@translate('description')</th>
                    <th scope="col">@translate('website')</th>
                    <th scope="col">@translate('email')</th>
                    <th scope="col">@translate('status')</th>
                    <th scope="col">@translate('created_at')</th>
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
<script src="{{ asset('assets/tenant/js/customer/brands/index.js') }}"></script>
@endsection
