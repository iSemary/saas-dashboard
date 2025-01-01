@extends('layouts.landlord.app')
@section('content')
    <div class="card">
        <div class="card-header">
            @include('layouts.shared.filter-date')
        </div>
        <div class="card-body">
            <table id="table" data-route="{{ route("landlord.translations.index") }}" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">@lang('language_id')</th>
                        <th scope="col">@lang('translation_key')</th>
                        <th scope="col">@lang('translation_value')</th>
                        <th scope="col">@lang('translation_context')</th>
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
<script src="{{ asset("assets/landlord/js/localizations/translations/index.js") }}"></script>
@endsection