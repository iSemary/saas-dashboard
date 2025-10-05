@extends('layouts.tenant.app')

@section('title', $title ?? translate('users'))

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
        <table id="table" data-route="{{ route('tenant.users.index') }}" class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">
                        <input type="checkbox" id="selectAll">
                    </th>
                    <th scope="col">#</th>
                    <th scope="col">@translate('name')</th>
                    <th scope="col">@translate('email')</th>
                    <th scope="col">@translate('username')</th>
                    <th scope="col">@translate('roles')</th>
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
                <i class="fas fa-ban mr-2"></i>@translate('deactivate_selected')
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
let tableID = "#table";
let tableRoute = $(tableID).attr("data-route");
let selectedUsers = [];

let cols = [
    { 
        data: null, 
        orderable: false, 
        searchable: false,
        render: function(data, type, row) {
            return `<input type="checkbox" class="user-checkbox" value="${row.id}">`;
        }
    },
    { data: "id", name: "id" },
    { data: "name", name: "name" },
    { data: "email", name: "email" },
    { data: "username", name: "username" },
    { data: "roles", name: "roles", orderable: false, searchable: false },
    { data: "status", name: "status" },
    { data: "created_at", name: "created_at" },
    { data: "actions", name: "actions", orderable: false, searchable: false },
];

filterTable({ route: tableRoute, tableID: tableID, cols: cols });

// Select all functionality
$(document).on('change', '#selectAll', function() {
    $('.user-checkbox').prop('checked', this.checked);
    updateSelectedUsers();
});

$(document).on('change', '.user-checkbox', function() {
    updateSelectedUsers();
});

function updateSelectedUsers()
{
    selectedUsers = [];
    $('.user-checkbox:checked').each(function() {
        selectedUsers.push($(this).val());
    });
    
    if (selectedUsers.length > 0)
    {
        $('#bulkActions').show();
    }
    else
    {
        $('#bulkActions').hide();
    }
}

function bulkActivate()
{
    if (selectedUsers.length === 0)
    {
        Swal.fire('@translate("error")', '@translate("please_select_users")', 'error');
        return;
    }

    Swal.fire({
        title: '@translate("activate_users")',
        text: '@translate("are_you_sure_activate_users")',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '@translate("yes_activate")',
        cancelButtonText: '@translate("cancel")'
    }).then((result) => {
        if (result.isConfirmed)
        {
            $.ajax({
                url: '{{ route("tenant.users.bulk-activate") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    user_ids: selectedUsers
                },
                success: function(response) {
                    Swal.fire('@translate("success")', response.message, 'success');
                    $(tableID).DataTable().ajax.reload();
                    selectedUsers = [];
                    $('#bulkActions').hide();
                },
                error: function(xhr) {
                    Swal.fire('@translate("error")', xhr.responseJSON?.message || '@translate("an_error_occurred")', 'error');
                }
            });
        }
    });
}

function bulkDeactivate()
{
    if (selectedUsers.length === 0)
    {
        Swal.fire('@translate("error")', '@translate("please_select_users")', 'error');
        return;
    }

    Swal.fire({
        title: '@translate("deactivate_users")',
        text: '@translate("are_you_sure_deactivate_users")',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '@translate("yes_deactivate")',
        cancelButtonText: '@translate("cancel")'
    }).then((result) => {
        if (result.isConfirmed)
        {
            $.ajax({
                url: '{{ route("tenant.users.bulk-deactivate") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    user_ids: selectedUsers
                },
                success: function(response) {
                    Swal.fire('@translate("success")', response.message, 'success');
                    $(tableID).DataTable().ajax.reload();
                    selectedUsers = [];
                    $('#bulkActions').hide();
                },
                error: function(xhr) {
                    Swal.fire('@translate("error")', xhr.responseJSON?.message || '@translate("an_error_occurred")', 'error');
                }
            });
        }
    });
}

function bulkDelete()
{
    if (selectedUsers.length === 0)
    {
        Swal.fire('@translate("error")', '@translate("please_select_users")', 'error');
        return;
    }

    Swal.fire({
        title: '@translate("delete_users")',
        text: '@translate("are_you_sure_delete_users")',
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: '@translate("yes_delete")',
        cancelButtonText: '@translate("cancel")',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed)
        {
            $.ajax({
                url: '{{ route("tenant.users.bulk-delete") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    user_ids: selectedUsers
                },
                success: function(response) {
                    Swal.fire('@translate("success")', response.message, 'success');
                    $(tableID).DataTable().ajax.reload();
                    selectedUsers = [];
                    $('#bulkActions').hide();
                },
                error: function(xhr) {
                    Swal.fire('@translate("error")', xhr.responseJSON?.message || '@translate("an_error_occurred")', 'error');
                }
            });
        }
    });
}
</script>
@endsection

