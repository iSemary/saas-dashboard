    <div class="card">
        <div class="card-header">
            @include('layouts.shared.filter-date')
        </div>
        <div class="card-body">
            <table id="showTable" data-route="{{ $route }}" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">@translate('agent')</th>
                        <th scope="col">@translate('ip')</th>
                        <th scope="col">@translate('created_at')</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <script src="{{ asset('assets/global/js/auth/login-attempts/index.js') }}"></script>
