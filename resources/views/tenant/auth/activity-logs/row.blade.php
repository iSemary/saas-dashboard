<div class="card">
    <div class="card-header">
        <div class="row">
            @include('layouts.shared.filter-date', ['classes' => 'col-8 col-md-8'])
        </div>
    </div>
    <div class="card-body">
        <table id="showTable" data-route="{{ route('tenant.activity-logs.row', $id) }}?object_type={{ $objectType }}"
            class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">@translate('event')</th>
                    <th scope="col">@translate('type')</th>
                    <th scope="col">@translate('type_id')</th>
                    <th scope="col">@translate('old_values')</th>
                    <th scope="col">@translate('new_values')</th>
                    <th scope="col">@translate('ip_address')</th>
                    <th scope="col">@translate('user_agent')</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
<script src="{{ asset('assets/shared/js/auth/activity-logs/row.js') }}"></script>

