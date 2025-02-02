@extends('layouts.landlord.app')
@section('content')
    <div class="card">
        <div class="card-header">
            @include('layouts.shared.filter-date')
        </div>
        <div class="card-body">
            <table id="table" data-route="{{ route('landlord.languages.index') }}" data-selectable="true"
                class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col"><input type="checkbox" name="select_all_rows" id="selectAllRows" /></th>
                        <th scope="col">#</th>
                        <th scope="col">@translate('name')</th>
                        <th scope="col">@translate('locale')</th>
                        <th scope="col">@translate('direction')</th>
                        <th scope="col">@translate('total_translations')</th>
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
    <script src="{{ asset('assets/landlord/js/localizations/languages/index.js') }}"></script>
@endsection
