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
                        <th scope="col">@lang('name')</th>
                        <th scope="col">@lang('code')</th>
                        <th scope="col">@lang('capital_city_id')</th>
                        <th scope="col">@lang('region')</th>
                        <th scope="col">@lang('flag')</th>
                        <th scope="col">@lang('phone_code')</th>
                        <th scope="col">@lang('action')</th>
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