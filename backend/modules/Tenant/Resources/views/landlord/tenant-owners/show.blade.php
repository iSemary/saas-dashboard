<div class="modal-header">
    <h4 class="modal-title">@translate('tenant_owner_details')</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">@translate('user_information')</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $tenantOwner->user->avatar ?? '/assets/shared/images/icons/user-default.png' }}" 
                             class="img-circle elevation-2" width="64" height="64" alt="User Image">
                        <div class="ml-3">
                            <h4 class="mb-0">{{ $tenantOwner->user->name ?? 'N/A' }}</h4>
                            <p class="text-muted mb-0">{{ $tenantOwner->user->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>@translate('username'):</strong></td>
                            <td>{{ $tenantOwner->user->username ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>@translate('country'):</strong></td>
                            <td>{{ $tenantOwner->user->country->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>@translate('language'):</strong></td>
                            <td>{{ $tenantOwner->user->language->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>@translate('email_verified_at'):</strong></td>
                            <td>
                                @if($tenantOwner->user->email_verified_at)
                                    <span class="badge badge-success">@translate('verified')</span>
                                    <br><small>{{ $tenantOwner->user->email_verified_at->format('Y-m-d H:i') }}</small>
                                @else
                                    <span class="badge badge-warning">@translate('not_verified')</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">@translate('tenant_information')</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>@translate('tenant_name'):</strong></td>
                            <td>{{ $tenantOwner->tenant->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>@translate('domain'):</strong></td>
                            <td>{{ $tenantOwner->tenant->domain ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>@translate('database'):</strong></td>
                            <td>{{ $tenantOwner->tenant->database ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>@translate('tenant_created'):</strong></td>
                            <td>{{ $tenantOwner->tenant->created_at ? $tenantOwner->tenant->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">@translate('assignment_details')</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary">
                                    <i class="fas fa-user-tag"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">@translate('role')</span>
                                    <span class="info-box-number">
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
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon {{ $tenantOwner->is_super_admin ? 'bg-warning' : 'bg-secondary' }}">
                                    <i class="fas fa-crown"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">@translate('super_admin')</span>
                                    <span class="info-box-number">
                                        @if($tenantOwner->is_super_admin)
                                            <span class="badge badge-warning">@translate('yes')</span>
                                        @else
                                            <span class="badge badge-secondary">@translate('no')</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon {{ $tenantOwner->status === 'active' ? 'bg-success' : ($tenantOwner->status === 'inactive' ? 'bg-warning' : 'bg-danger') }}">
                                    <i class="fas fa-{{ $tenantOwner->status === 'active' ? 'check' : ($tenantOwner->status === 'inactive' ? 'pause' : 'ban') }}"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">@translate('status')</span>
                                    <span class="info-box-number">
                                        @php
                                            $statusColors = [
                                                'active' => 'badge-success',
                                                'inactive' => 'badge-warning',
                                                'suspended' => 'badge-danger'
                                            ];
                                            $statusColor = $statusColors[$tenantOwner->status] ?? 'badge-secondary';
                                        @endphp
                                        <span class="badge {{ $statusColor }}">{{ ucfirst($tenantOwner->status) }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-calendar"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">@translate('assigned_date')</span>
                                    <span class="info-box-number">
                                        <small>{{ $tenantOwner->created_at ? $tenantOwner->created_at->format('Y-m-d') : 'N/A' }}</small>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($tenantOwner->permissions && count($tenantOwner->permissions) > 0)
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">@translate('permissions')</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($tenantOwner->permissions as $permission)
                            <div class="col-md-4 mb-2">
                                <span class="badge badge-light">
                                    <i class="fas fa-key"></i> {{ $permission }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">@translate('audit_information')</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>@translate('created_by'):</strong></td>
                                    <td>{{ $tenantOwner->creator->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@translate('created_at'):</strong></td>
                                    <td>{{ $tenantOwner->created_at ? $tenantOwner->created_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>@translate('updated_by'):</strong></td>
                                    <td>{{ $tenantOwner->updater->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>@translate('updated_at'):</strong></td>
                                    <td>{{ $tenantOwner->updated_at ? $tenantOwner->updated_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">@translate('close')</button>
    @can('update.tenant_owners')
        <button type="button" class="btn btn-primary" onclick="editTenantOwner({{ $tenantOwner->id }})">
            <i class="fas fa-edit"></i> @translate('edit')
        </button>
    @endcan
    @can('delete.tenant_owners')
        <button type="button" class="btn btn-danger" onclick="deleteTenantOwner({{ $tenantOwner->id }})">
            <i class="fas fa-trash"></i> @translate('delete')
        </button>
    @endcan
</div>

<script>
function editTenantOwner(id) {
    // Close current modal
    $('.modal').modal('hide');
    
    // Open edit modal
    $.get(`/landlord/tenant-owners/${id}/edit`, function(data) {
        $('body').append(data);
        $('#edit-tenant-owner-modal').modal('show');
    });
}

function deleteTenantOwner(id) {
    if (confirm('@translate("confirm_delete_tenant_owner")')) {
        $.ajax({
            url: `/landlord/tenant-owners/${id}`,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('.modal').modal('hide');
                    // Reload the main table
                    if (typeof window.reloadTenantOwnersTable === 'function') {
                        window.reloadTenantOwnersTable();
                    }
                } else {
                    toastr.error(response.message);
                }
            }
        });
    }
}
</script>
