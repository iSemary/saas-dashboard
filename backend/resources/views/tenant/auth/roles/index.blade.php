@extends('layouts.tenant.app')

@section('title', $title ?? translate('roles'))

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            @include('layouts.shared.filter-date')
            <div class="col-md-4 text-right">
                @if(isset($actionButtons))
                    @foreach($actionButtons as $button)
                        <a href="{{ $button['link'] ?? '#' }}" 
                           class="{{ $button['class'] ?? 'btn btn-primary' }} {{ $button['class'] ?? '' }}"
                           @if(isset($button['attr']))
                               @foreach($button['attr'] as $attrKey => $attrValue)
                                   {{ $attrKey }}="{{ $attrValue }}"
                               @endforeach
                           @endif>
                            <i class="fas fa-plus"></i> {{ $button['text'] }}
                        </a>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    <div class="card-body">
        <table id="table" data-route="{{ route('tenant.roles.index') }}" class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">@translate('name')</th>
                    <th scope="col">@translate('guard_name')</th>
                    <th scope="col">@translate('permissions_count')</th>
                    <th scope="col">@translate('users_count')</th>
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
<script>
let tableID = "#table";
let tableRoute = $(tableID).attr("data-route");
let cols = [
    { data: "id", name: "id" },
    { data: "name", name: "name" },
    { data: "guard_name", name: "guard_name" },
    { data: "permissions_count", name: "permissions_count", orderable: false, searchable: false },
    { data: "users_count", name: "users_count", orderable: false, searchable: false },
    { data: "created_at", name: "created_at" },
    { data: "actions", name: "actions", orderable: false, searchable: false },
];

filterTable({ route: tableRoute, tableID: tableID, cols: cols });
</script>
@endsection

