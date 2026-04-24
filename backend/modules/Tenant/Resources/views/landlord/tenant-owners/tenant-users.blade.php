<div class="modal-header">
    <h4 class="modal-title">@translate('tenant_users') - {{ $tenant->name ?? 'N/A' }}</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="info-box">
                <span class="info-box-icon bg-primary">
                    <i class="fas fa-building"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">@translate('tenant')</span>
                    <span class="info-box-number">{{ $tenant->name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="info-box">
                <span class="info-box-icon bg-success">
                    <i class="fas fa-users"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">@translate('total_users')</span>
                    <span class="info-box-number" id="total-users-count">{{ $tenantOwners->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    @if($tenantOwners->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>@translate('user')</th>
                        <th>@translate('role')</th>
                        <th>@translate('super_admin')</th>
                        <th>@translate('status')</th>
                        <th>@translate('assigned_date')</th>
                        <th>@translate('actions')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tenantOwners as $tenantOwner)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $tenantOwner->user->avatar ?? '/assets/shared/images/icons/user-default.png' }}" 
                                         class="img-circle elevation-2" width="32" height="32" alt="User Image">
                                    <div class="ml-2">
                                        <div class="font-weight-bold">{{ $tenantOwner->user->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $tenantOwner->user->email ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $roleColors = [
                                        'owner' => 'badge-primary',
                                        'admin' => 'badge-success',
                                        'manager' => 'badge-warning',
                                        'user' => 'badge-secondary'
                                    ];
                                    $roleColor = $roleColors[$tenantOwner->role] ?? 'badge-secondary';
                                @endphp
                                <span class="badge {{ $roleColor }}">{{ ucfirst($tenantOwner->role) }}</span>
                            </td>
                            <td>
                                @if($tenantOwner->is_super_admin)
                                    <span class="badge badge-warning">
                                        <i class="fas fa-crown"></i> @translate('yes')
                                    </span>
                                @else
                                    <span class="text-muted">@translate('no')</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'active' => 'badge-success',
                                        'inactive' => 'badge-warning',
                                        'suspended' => 'badge-danger'
                                    ];
                                    $statusColor = $statusColors[$tenantOwner->status] ?? 'badge-secondary';
                                @endphp
                                <span class="badge {{ $statusColor }}">{{ ucfirst($tenantOwner->status) }}</span>
                            </td>
                            <td>
                                <small>{{ $tenantOwner->created_at ? $tenantOwner->created_at->format('Y-m-d H:i') : 'N/A' }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @can('read.tenant_owners')
                                        <button type="button" class="btn btn-sm btn-info view-tenant-owner-btn" 
                                                data-id="{{ $tenantOwner->id }}" title="@translate('view_details')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    @endcan
                                    @can('update.tenant_owners')
                                        <button type="button" class="btn btn-sm btn-primary edit-tenant-owner-btn" 
                                                data-id="{{ $tenantOwner->id }}" title="@translate('edit')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endcan
                                    @can('update.tenant_owners')
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" 
                                                    data-toggle="dropdown" title="@translate('more_actions')">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @if(!$tenantOwner->is_super_admin)
                                                    <a class="dropdown-item promote-btn" href="#" data-id="{{ $tenantOwner->id }}">
                                                        <i class="fas fa-crown"></i> @translate('promote_to_super_admin')
                                                    </a>
                                                @else
                                                    <a class="dropdown-item demote-btn" href="#" data-id="{{ $tenantOwner->id }}">
                                                        <i class="fas fa-user"></i> @translate('demote_from_super_admin')
                                                    </a>
                                                @endif
                                                <div class="dropdown-divider"></div>
                                                @if($tenantOwner->status !== 'active')
                                                    <a class="dropdown-item status-btn" href="#" data-id="{{ $tenantOwner->id }}" data-status="active">
                                                        <i class="fas fa-check"></i> @translate('activate')
                                                    </a>
                                                @endif
                                                @if($tenantOwner->status !== 'inactive')
                                                    <a class="dropdown-item status-btn" href="#" data-id="{{ $tenantOwner->id }}" data-status="inactive">
                                                        <i class="fas fa-pause"></i> @translate('deactivate')
                                                    </a>
                                                @endif
                                                @if($tenantOwner->status !== 'suspended')
                                                    <a class="dropdown-item status-btn" href="#" data-id="{{ $tenantOwner->id }}" data-status="suspended">
                                                        <i class="fas fa-ban"></i> @translate('suspend')
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-4">
            <i class="fas fa-users fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">@translate('no_users_assigned')</h5>
            <p class="text-muted">@translate('no_users_assigned_description')</p>
            @can('create.tenant_owners')
                <button type="button" class="btn btn-primary open-create-modal" 
                        data-modal-link="{{ route('landlord.tenant-owners.create') }}"
                        data-modal-title="@translate('assign_user_to_tenant')">
                    <i class="fas fa-plus"></i> @translate('assign_new_user')
                </button>
            @endcan
        </div>
    @endif
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">@translate('close')</button>
    @can('create.tenant_owners')
        <button type="button" class="btn btn-success open-create-modal" 
                data-modal-link="{{ route('landlord.tenant-owners.create') }}"
                data-modal-title="@translate('assign_user_to_tenant')">
            <i class="fas fa-plus"></i> @translate('assign_new_user')
        </button>
    @endcan
</div>

<script>
$(document).ready(function() {
    // View tenant owner details
    $(document).on('click', '.view-tenant-owner-btn', function() {
        const id = $(this).data('id');
        viewTenantOwner(id);
    });

    // Edit tenant owner
    $(document).on('click', '.edit-tenant-owner-btn', function() {
        const id = $(this).data('id');
        editTenantOwner(id);
    });

    // Promote to super admin
    $(document).on('click', '.promote-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        promoteToSuperAdmin(id);
    });

    // Demote from super admin
    $(document).on('click', '.demote-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        demoteFromSuperAdmin(id);
    });

    // Update status
    $(document).on('click', '.status-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const status = $(this).data('status');
        updateStatus(id, status);
    });

    function viewTenantOwner(id) {
        // Close current modal
        $('.modal').modal('hide');
        
        // Open view modal
        $.get(`/landlord/tenant-owners/${id}`, function(data) {
            $('body').append(data);
            $('#view-tenant-owner-modal').modal('show');
        });
    }

    function editTenantOwner(id) {
        // Close current modal
        $('.modal').modal('hide');
        
        // Open edit modal
        $.get(`/landlord/tenant-owners/${id}/edit`, function(data) {
            $('body').append(data);
            $('#edit-tenant-owner-modal').modal('show');
        });
    }

    function promoteToSuperAdmin(id) {
        if (confirm('@translate("confirm_promote_to_super_admin")')) {
            $.post(`/landlord/tenant-owners/${id}/promote`, {
                _token: $('meta[name="csrf-token"]').attr('content')
            }, function(data) {
                if (data.success) {
                    toastr.success(data.message);
                    // Reload the modal content
                    location.reload();
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
                    // Reload the modal content
                    location.reload();
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
                        // Reload the modal content
                        location.reload();
                    } else {
                        toastr.error(data.message);
                    }
                }
            });
        }
    }
});
</script>
