@extends('layouts.tenant.app')

@section('title', $title ?? translate('brand_details'))

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tag"></i>
                    {{ $brand->name }}
                </h3>
                <div class="card-tools">
                    <span class="badge badge-{{ $brand->status === 'active' ? 'success' : 'secondary' }}">
                        @translate($brand->status)
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        @if($brand->logo)
                            <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" class="img-fluid rounded">
                        @else
                            <div class="text-center text-muted">
                                <i class="fas fa-image fa-3x"></i>
                                <p>@translate('no_logo_available')</p>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>@translate('name'):</strong></td>
                                <td>{{ $brand->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>@translate('slug'):</strong></td>
                                <td><code>{{ $brand->slug }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>@translate('description'):</strong></td>
                                <td>{{ $brand->description ?: translate('no_description') }}</td>
                            </tr>
                            <tr>
                                <td><strong>@translate('website'):</strong></td>
                                <td>
                                    @if($brand->website)
                                        <a href="{{ $brand->website }}" target="_blank">{{ $brand->website }}</a>
                                    @else
                                        @translate('not_provided')
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>@translate('email'):</strong></td>
                                <td>{{ $brand->email ?: translate('not_provided') }}</td>
                            </tr>
                            <tr>
                                <td><strong>@translate('phone'):</strong></td>
                                <td>{{ $brand->phone ?: translate('not_provided') }}</td>
                            </tr>
                            <tr>
                                <td><strong>@translate('address'):</strong></td>
                                <td>{{ $brand->address ?: translate('not_provided') }}</td>
                            </tr>
                            <tr>
                                <td><strong>@translate('created_at'):</strong></td>
                                <td>{{ $brand->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td><strong>@translate('updated_at'):</strong></td>
                                <td>{{ $brand->updated_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar"></i>
                    @translate('statistics')
                </h3>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-info">
                                <i class="fas fa-building"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">@translate('total_branches')</span>
                                <span class="info-box-number">{{ $brand->branches_count }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-success">
                                <i class="fas fa-check-circle"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">@translate('active_branches')</span>
                                <span class="info-box-number">{{ $brand->active_branches_count }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    @translate('information')
                </h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    @translate('brands_are_managed_by_landlord')
                </div>
                <p class="text-muted">
                    @translate('contact_landlord_to_modify_brand')
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-building"></i>
                    @translate('branches')
                </h3>
            </div>
            <div class="card-body">
                <table id="branchesTable" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">@translate('name')</th>
                            <th scope="col">@translate('code')</th>
                            <th scope="col">@translate('location')</th>
                            <th scope="col">@translate('manager')</th>
                            <th scope="col">@translate('status')</th>
                            <th scope="col">@translate('created_at')</th>
                            <th scope="col">@translate('action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($brand->branches as $branch)
                            <tr>
                                <td>{{ $branch->id }}</td>
                                <td>{{ $branch->name }}</td>
                                <td><code>{{ $branch->code }}</code></td>
                                <td>{{ $branch->city }}, {{ $branch->state }}</td>
                                <td>{{ $branch->manager_name }}</td>
                                <td>
                                    <span class="badge badge-{{ $branch->status === 'active' ? 'success' : 'secondary' }}">
                                        @translate($branch->status)
                                    </span>
                                </td>
                                <td>{{ $branch->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    <a href="{{ route('tenant.branches.show', $branch->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    @translate('no_branches_found')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize branches DataTable
    $('#branchesTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/English.json'
        }
    });
});
</script>
@endsection
