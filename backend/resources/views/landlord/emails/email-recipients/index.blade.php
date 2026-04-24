@extends('layouts.landlord.app')
@section('content')
    <div class="card">
        <div class="card-header">
            @include('layouts.shared.filter-date')
        </div>
        <div class="card-body">
            <table id="table" data-route="{{ route("landlord.email-recipients.index") }}" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">@translate('email')</th>
                        <th scope="col">@translate('name')</th>
                        <th scope="col">@translate('total_metadata')</th>
                        <th scope="col">@translate('groups')</th>
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
<script src="{{ asset("assets/landlord/js/emails/email-recipients/index.js") }}"></script>
@endsection