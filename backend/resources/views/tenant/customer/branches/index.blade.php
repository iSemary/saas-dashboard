@extends('layouts.tenant.app')

@section('title', $title ?? translate('branches'))

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
        <table id="table" data-route="{{ route('tenant.branches.index') }}" class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">@translate('name')</th>
                    <th scope="col">@translate('code')</th>
                    <th scope="col">@translate('brand')</th>
                    <th scope="col">@translate('location')</th>
                    <th scope="col">@translate('working_hours')</th>
                    <th scope="col">@translate('manager')</th>
                    <th scope="col">@translate('status')</th>
                    <th scope="col">@translate('created_at')</th>
                    <th scope="col">@translate('action')</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        <!-- Bulk Actions -->
        <div id="bulkActions" class="mt-3" style="display: none;">
            <button type="button" class="btn btn-success btn-sm" onclick="bulkActivate()">
                <i class="fas fa-check mr-2"></i>@translate('activate_selected')
            </button>
            <button type="button" class="btn btn-warning btn-sm" onclick="bulkDeactivate()">
                <i class="fas fa-times mr-2"></i>@translate('deactivate_selected')
            </button>
            <button type="button" class="btn btn-danger btn-sm" onclick="bulkDelete()">
                <i class="fas fa-trash mr-2"></i>@translate('delete_selected')
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    initializeDataTable();
    
    // Handle bulk actions
    $('#selectAll').on('change', function() {
        $('.row-checkbox').prop('checked', this.checked);
        toggleBulkActions();
    });
    
    $('.row-checkbox').on('change', function() {
        toggleBulkActions();
    });
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
            { data: 'name', name: 'name' },
            { data: 'code', name: 'code' },
            { data: 'brand_name', name: 'brand_name' },
            { data: 'location', name: 'location' },
            { data: 'working_hours', name: 'working_hours', orderable: false, searchable: false },
            { data: 'manager', name: 'manager' },
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

function toggleBulkActions() {
    const checkedBoxes = $('.row-checkbox:checked').length;
    if (checkedBoxes > 0) {
        $('#bulkActions').show();
    } else {
        $('#bulkActions').hide();
    }
}

function bulkActivate() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        Swal.fire({
            title: translate('no_selection'),
            text: translate('please_select_branches_to_activate'),
            icon: 'warning'
        });
        return;
    }
    
    Swal.fire({
        title: translate('activate_branches'),
        text: translate('are_you_sure_you_want_to_activate_selected_branches'),
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: translate('yes_activate'),
        cancelButtonText: translate('cancel')
    }).then((result) => {
        if (result.isConfirmed) {
            // Implement bulk activate logic
            console.log('Activating branches:', selectedIds);
        }
    });
}

function bulkDeactivate() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        Swal.fire({
            title: translate('no_selection'),
            text: translate('please_select_branches_to_deactivate'),
            icon: 'warning'
        });
        return;
    }
    
    Swal.fire({
        title: translate('deactivate_branches'),
        text: translate('are_you_sure_you_want_to_deactivate_selected_branches'),
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: translate('yes_deactivate'),
        cancelButtonText: translate('cancel')
    }).then((result) => {
        if (result.isConfirmed) {
            // Implement bulk deactivate logic
            console.log('Deactivating branches:', selectedIds);
        }
    });
}

function bulkDelete() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        Swal.fire({
            title: translate('no_selection'),
            text: translate('please_select_branches_to_delete'),
            icon: 'warning'
        });
        return;
    }
    
    Swal.fire({
        title: translate('delete_branches'),
        text: translate('are_you_sure_you_want_to_delete_selected_branches'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: translate('yes_delete'),
        cancelButtonText: translate('cancel'),
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            // Implement bulk delete logic
            console.log('Deleting branches:', selectedIds);
        }
    });
}

function getSelectedIds() {
    const selectedIds = [];
    $('.row-checkbox:checked').each(function() {
        selectedIds.push($(this).val());
    });
    return selectedIds;
}

// Refresh DataTable when date filters change
$('input[name="from_date"], input[name="to_date"]').on('change', function() {
    $('#table').DataTable().ajax.reload();
});
</script>
@endsection
