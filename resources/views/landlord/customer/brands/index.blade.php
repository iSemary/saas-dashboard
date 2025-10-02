@extends('layouts.landlord.app')
@section('content')
    <div class="card">
        <div class="card-header">
            @include('layouts.shared.filter-date')
        </div>
        <div class="card-body">
            <table id="table" data-route="{{ route('landlord.brands-web.index') }}"
                class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">@translate('logo')</th>
                        <th scope="col">@translate('name')</th>
                        <th scope="col">@translate('slug')</th>
                        <th scope="col">@translate('description')</th>
                        <th scope="col">@translate('tenant')</th>
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
    <script src="{{ asset('assets/landlord/js/customer/brands/index.js') }}"></script>
@endsection