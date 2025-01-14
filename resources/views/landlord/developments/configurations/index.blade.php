@extends('layouts.landlord.app')
@section('content')
    <div class="card">
        <div class="card-header">
            @include('layouts.shared.filter-date')
        </div>
        <div class="card-body">
            <table id="table" data-route="{{ route("landlord.development.configurations.index") }}" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">@translate('key')</th>
                        <th scope="col">@translate('value')</th>
                        <th scope="col">@translate('description')</th>
                        <th scope="col">@translate('type')</th>
                        <th scope="col">@translate('is_encrypted')</th>
                        <th scope="col">@translate('is_system')</th>
                        <th scope="col">@translate('is_visible')</th>
                        <th scope="col">@translate('action')</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section("scripts")
<script src="{{ asset("assets/landlord/js/developments/configurations/index.js") }}"></script>
@endsection