@extends('layouts.tenant.app')

@section('title', $title ?? translate('brands'))

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            @include('layouts.shared.filter-date')
            <div class="col-md-4 text-right">
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i>
                    @translate('brands_are_managed_by_landlord')
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table id="table" data-route="{{ route('tenant.brands.index') }}" class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">@translate('logo')</th>
                    <th scope="col">@translate('name')</th>
                    <th scope="col">@translate('slug')</th>
                    <th scope="col">@translate('description')</th>
                    <th scope="col">@translate('branches_count')</th>
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
<script>
$(document).ready(function() {
    // Initialize DataTable
    initializeDataTable();
});

function initializeDataTable() {
    $('#table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: $('#table').data('route'),
            type: 'GET',
            data: function(d) {
                d.from_date = $('input[name="from_date"]').val();
                d.to_date = $('input[name="to_date"]').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'logo', name: 'logo', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'slug', name: 'slug' },
            { data: 'description', name: 'description' },
            { data: 'branches_count', name: 'branches_count' },
            { data: 'status', name: 'status' },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/English.json'
        }
    });
}

// Refresh DataTable when date filters change
$('input[name="from_date"], input[name="to_date"]').on('change', function() {
    $('#table').DataTable().ajax.reload();
});
</script>
@endsection
