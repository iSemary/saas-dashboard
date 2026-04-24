@extends('layouts.tenant.app')

@section('title', $title ?? translate('permissions'))

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            @include('layouts.shared.filter-date')
            <div class="col-md-4 text-right">
                @if(isset($actionButtons))
                    @foreach($actionButtons as $button)
                        <a href="{{ $button['link'] ?? '#' }}" 
                           class="{{ $button['class'] ?? 'btn btn-primary' }}"
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
        <table id="table" data-route="{{ route('tenant.permissions.index') }}" class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">@translate('name')</th>
                    <th scope="col">@translate('guard_name')</th>
                    <th scope="col">@translate('resource')</th>
                    <th scope="col">@translate('action')</th>
                    <th scope="col">@translate('roles_count')</th>
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
    { data: "resource", name: "resource" },
    { data: "action", name: "action" },
    { data: "roles_count", name: "roles_count", orderable: false, searchable: false },
    { data: "created_at", name: "created_at" },
    { data: "actions", name: "actions", orderable: false, searchable: false },
];

filterTable({ route: tableRoute, tableID: tableID, cols: cols });
</script>
@endsection

