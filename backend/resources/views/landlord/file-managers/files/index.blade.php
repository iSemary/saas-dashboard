@extends('layouts.landlord.app')
@section('content')
    <div class="card">
        <div class="card-header">
            @include('layouts.shared.filter-date')
        </div>
        <div class="card-body">
            <table id="table" data-route="{{ route('landlord.development.files.index') }}"
                class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">@translate('folder')</th>
                        <th scope="col">@translate('hash_name')</th>
                        <th scope="col">@translate('checksum')</th>
                        <th scope="col">@translate('original_name')</th>
                        <th scope="col">@translate('mime_type')</th>
                        <th scope="col">@translate('host')</th>
                        <th scope="col">@translate('status')</th>
                        <th scope="col">@translate('access_level')</th>
                        <th scope="col">@translate('size')</th>
                        <th scope="col">@translate('metadata')</th>
                        <th scope="col">@translate('is_encrypted')</th>
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
    <script src="{{ asset('assets/landlord/js/file-managers/files/index.js') }}"></script>
@endsection
