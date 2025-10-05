@extends('layouts.tenant.app')

@section('title', $title ?? translate('login_attempts'))

@section('content')
<div class="card">
    <div class="card-header">
        @include('layouts.shared.filter-date')
    </div>
    <div class="card-body">
        <table id="showTable" data-route="{{ $route }}" class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">@translate('agent')</th>
                    <th scope="col">@translate('ip')</th>
                    <th scope="col">@translate('created_at')</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/shared/js/auth/login-attempts/index.js') }}"></script>
@endsection

