@extends('layouts.landlord.app')
@section('content')
    <div class="card">
        <div class="card-header">
            @include('layouts.shared.filter-date')
        </div>
        <div class="card-body">
            <table id="table" data-route="{{ route("landlord.languages.index") }}" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">@lang('name')</th>
                        <th scope="col">@lang('locale')</th>
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
<script src="{{ asset("assets/landlord/js/localizations/languages/index.js") }}"></script>
@endsection