@extends('layouts.landlord.app')

@section('title', translate('tenant_owners'))

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">@translate('tenant_owners')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">@translate('home')</a></li>
                        <li class="breadcrumb-item active">@translate('tenant_owners')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">@translate('tenant_owner_management')</h3>
                            <div class="card-tools">
                                @can('create.tenant_owners')
                                    <button type="button" class="btn btn-success btn-sm open-create-modal" 
                                            data-modal-link="{{ route('landlord.tenant-owners.create') }}"
                                            data-modal-title="@translate('assign_user_to_tenant')">
                                        <i class="fas fa-plus"></i> @translate('assign_new_user')
                                    </button>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Filters -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <select class="form-control" id="tenant-filter">
                                        <option value="">@translate('all_tenants')</option>
                                        @foreach($tenants ?? [] as $tenant)
                                            <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control" id="role-filter">
                                        <option value="">@translate('all_roles')</option>
                                        <option value="owner">@translate('owner')</option>
                                        <option value="admin">@translate('admin')</option>
                                        <option value="manager">@translate('manager')</option>
                                        <option value="user">@translate('user')</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control" id="status-filter">
                                        <option value="">@translate('all_statuses')</option>
                                        <option value="active">@translate('active')</option>
                                        <option value="inactive">@translate('inactive')</option>
                                        <option value="suspended">@translate('suspended')</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="search-input" 
                                           placeholder="@translate('search_tenant_owners')">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-primary" id="search-btn">
                                        <i class="fas fa-search"></i> @translate('search')
                                    </button>
                                </div>
                            </div>

                            <!-- Statistics Cards -->
                            <div class="row mb-4">
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-info">
                                        <div class="inner">
                                            <h3 id="total-count">0</h3>
                                            <p>@translate('total_tenant_owners')</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-success">
                                        <div class="inner">
                                            <h3 id="active-count">0</h3>
                                            <p>@translate('active_tenant_owners')</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-user-check"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-warning">
                                        <div class="inner">
                                            <h3 id="super-admin-count">0</h3>
                                            <p>@translate('super_admins_count')</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-crown"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-danger">
                                        <div class="inner">
                                            <h3 id="suspended-count">0</h3>
                                            <p>@translate('suspended_tenant_owners')</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-user-times"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- DataTable -->
                            <div class="table-responsive">
                                <table id="tenant-owners-table" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>@translate('user')</th>
                                            <th>@translate('tenant')</th>
                                            <th>@translate('role')</th>
                                            <th>@translate('super_admin')</th>
                                            <th>@translate('status')</th>
                                            <th>@translate('created_at')</th>
                                            <th>@translate('actions')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Action Buttons Template -->
<template id="action-buttons-template">
    <div class="btn-group" role="group">
        @can('update.tenant_owners')
            <button type="button" class="btn btn-sm btn-primary edit-btn" data-id="">
                <i class="fas fa-edit"></i>
            </button>
        @endcan
        
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                <i class="fas fa-cog"></i>
            </button>
            <div class="dropdown-menu">
                @can('update.tenant_owners')
                    <a class="dropdown-item promote-btn" href="#" data-id="">
                        <i class="fas fa-crown"></i> @translate('promote_to_super_admin')
                    </a>
                    <a class="dropdown-item demote-btn" href="#" data-id="">
                        <i class="fas fa-user"></i> @translate('demote_from_super_admin')
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item status-btn" href="#" data-id="" data-status="active">
                        <i class="fas fa-check"></i> @translate('activate')
                    </a>
                    <a class="dropdown-item status-btn" href="#" data-id="" data-status="inactive">
                        <i class="fas fa-pause"></i> @translate('deactivate')
                    </a>
                    <a class="dropdown-item status-btn" href="#" data-id="" data-status="suspended">
                        <i class="fas fa-ban"></i> @translate('suspend')
                    </a>
                @endcan
                @can('delete.tenant_owners')
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item delete-btn text-danger" href="#" data-id="">
                        <i class="fas fa-trash"></i> @translate('delete')
                    </a>
                @endcan
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let table = $('#tenant-owners-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('landlord.tenant-owners.index') }}",
            data: function(d) {
                d.tenant_id = $('#tenant-filter').val();
                d.role = $('#role-filter').val();
                d.status = $('#status-filter').val();
                d.search = $('#search-input').val();
            }
        },
        columns: [
            {
                data: 'user',
                name: 'user',
                render: function(data, type, row) {
                    return `
                        <div class="d-flex align-items-center">
                            <img src="${row.user.avatar || '/assets/shared/images/icons/user-default.png'}" 
                                 class="img-circle elevation-2" width="32" height="32" alt="User Image">
                            <div class="ml-2">
                                <div class="font-weight-bold">${row.user.name}</div>
                                <small class="text-muted">${row.user.email}</small>
                            </div>
                        </div>
                    `;
                }
            },
            {
                data: 'tenant',
                name: 'tenant',
                render: function(data, type, row) {
                    return `
                        <div>
                            <div class="font-weight-bold">${row.tenant.name}</div>
                            <small class="text-muted">${row.tenant.domain}</small>
                        </div>
                    `;
                }
            },
            {
                data: 'role',
                name: 'role',
                render: function(data, type, row) {
                    const roleColors = {
                        'owner': 'badge-primary',
                        'admin': 'badge-success',
                        'manager': 'badge-warning',
                        'user': 'badge-secondary'
                    };
                    return `<span class="badge ${roleColors[row.role] || 'badge-secondary'}">${row.role}</span>`;
                }
            },
            {
                data: 'is_super_admin',
                name: 'is_super_admin',
                render: function(data, type, row) {
                    return row.is_super_admin 
                        ? '<span class="badge badge-warning"><i class="fas fa-crown"></i> @translate("super_admin")</span>'
                        : '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'status',
                name: 'status',
                render: function(data, type, row) {
                    const statusColors = {
                        'active': 'badge-success',
                        'inactive': 'badge-warning',
                        'suspended': 'badge-danger'
                    };
                    return `<span class="badge ${statusColors[row.status] || 'badge-secondary'}">${row.status}</span>`;
                }
            },
            {
                data: 'created_at',
                name: 'created_at',
                render: function(data, type, row) {
                    return moment(row.created_at).format('YYYY-MM-DD HH:mm');
                }
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    let template = $('#action-buttons-template').html();
                    template = template.replace(/data-id=""/g, `data-id="${row.id}"`);
                    return template;
                }
            }
        ],
        order: [[5, 'desc']],
        pageLength: 25,
        responsive: true,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/English.json"
        }
    });

    // Load statistics
    loadStatistics();

    // Filter events
    $('#tenant-filter, #role-filter, #status-filter').on('change', function() {
        table.draw();
        loadStatistics();
    });

    $('#search-btn').on('click', function() {
        table.draw();
    });

    $('#search-input').on('keypress', function(e) {
        if (e.which === 13) {
            table.draw();
        }
    });

    // Action button events
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        // Open edit modal
        openEditModal(id);
    });

    $(document).on('click', '.promote-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        promoteToSuperAdmin(id);
    });

    $(document).on('click', '.demote-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        demoteFromSuperAdmin(id);
    });

    $(document).on('click', '.status-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const status = $(this).data('status');
        updateStatus(id, status);
    });

    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        deleteTenantOwner(id);
    });

    function loadStatistics() {
        $.get("{{ route('landlord.tenant-owners.stats') }}", function(data) {
            if (data.success) {
                $('#total-count').text(data.data.total);
                $('#active-count').text(data.data.active);
                $('#super-admin-count').text(data.data.super_admins);
                $('#suspended-count').text(data.data.suspended);
            }
        });
    }

    function openEditModal(id) {
        // Implementation for edit modal
        console.log('Edit tenant owner:', id);
    }

    function promoteToSuperAdmin(id) {
        if (confirm('@translate("confirm_promote_to_super_admin")')) {
            $.post(`/landlord/tenant-owners/${id}/promote`, {
                _token: $('meta[name="csrf-token"]').attr('content')
            }, function(data) {
                if (data.success) {
                    toastr.success(data.message);
                    table.draw();
                    loadStatistics();
                } else {
                    toastr.error(data.message);
                }
            });
        }
    }

    function demoteFromSuperAdmin(id) {
        if (confirm('@translate("confirm_demote_from_super_admin")')) {
            $.post(`/landlord/tenant-owners/${id}/demote`, {
                _token: $('meta[name="csrf-token"]').attr('content')
            }, function(data) {
                if (data.success) {
                    toastr.success(data.message);
                    table.draw();
                    loadStatistics();
                } else {
                    toastr.error(data.message);
                }
            });
        }
    }

    function updateStatus(id, status) {
        if (confirm(`@translate("confirm_update_status_to") ${status}?`)) {
            $.ajax({
                url: `/landlord/tenant-owners/${id}/status`,
                type: 'PUT',
                data: {
                    status: status,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data.success) {
                        toastr.success(data.message);
                        table.draw();
                        loadStatistics();
                    } else {
                        toastr.error(data.message);
                    }
                }
            });
        }
    }

    function deleteTenantOwner(id) {
        if (confirm('@translate("confirm_delete_tenant_owner")')) {
            $.ajax({
                url: `/landlord/tenant-owners/${id}`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if (data.success) {
                        toastr.success(data.message);
                        table.draw();
                        loadStatistics();
                    } else {
                        toastr.error(data.message);
                    }
                }
            });
        }
    }
});
</script>
@endpush

@push('styles')
<style>
.small-box .inner h3 {
    font-size: 2.2rem;
    font-weight: bold;
    margin: 0 0 10px 0;
    white-space: nowrap;
    padding: 0;
}

.small-box .inner p {
    font-size: 1rem;
}

.badge {
    font-size: 0.75em;
}

.img-circle {
    border-radius: 50%;
}
</style>
@endpush
