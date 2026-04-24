@extends('layouts.landlord.app')
@section('content')
    <div class="card">
        <div class="card-header">
            @include('layouts.shared.filter-date')
        </div>
        <div class="card-body">
            <table id="table" data-route="{{ route('landlord.emails.index') }}" data-selectable="true"
                class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col"><input type="checkbox" name="select_all_rows" id="selectAllRows" /></th>
                        <th scope="col">#</th>
                        <th scope="col">@translate('template')</th>
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
    <script src="{{ asset('assets/landlord/js/emails/index.js') }}"></script>
@endsection
