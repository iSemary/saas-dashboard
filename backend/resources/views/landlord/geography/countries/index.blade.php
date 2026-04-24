@extends('layouts.landlord.app')
@section('content')
    <div class="card">
        <div class="card-header">
            @include('layouts.shared.filter-date')
        </div>
        <div class="card-body">
            <table id="table" data-route="{{ route("landlord.countries.index") }}" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">@translate('name')</th>
                        <th scope="col">@translate('capital_province')</th>
                        <th scope="col">@translate('code')</th>
                        <th scope="col">@translate('region')</th>
                        <th scope="col">@translate('flag')</th>
                        <th scope="col">@translate('timezone')</th>
                        <th scope="col">@translate('phone_code')</th>
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
<script src="{{ asset("assets/landlord/js/geography/countries/index.js") }}"></script>
@endsection