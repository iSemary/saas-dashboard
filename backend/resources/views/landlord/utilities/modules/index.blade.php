@extends('layouts.landlord.app')
@section('content')
    <div class="card">
        <div class="card-header">
            @include('layouts.shared.filter-date')
        </div>
        <div class="card-body">
            <table id="table" data-route="{{ route("landlord.modules.index") }}" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">@translate('name')</th>
                        <th scope="col">@translate('module_key')</th>
                        <th scope="col">@translate('description')</th>
                        <th scope="col">@translate('route')</th>
                        <th scope="col">@translate('icon')</th>
                        <th scope="col">@translate('slogan')</th>
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
@section("scripts")
<script src="{{ asset("assets/landlord/js/utilities/modules/index.js") }}"></script>
@endsection