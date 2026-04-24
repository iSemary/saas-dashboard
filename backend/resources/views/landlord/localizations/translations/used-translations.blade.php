<div>
    @translate('total'): {{ count($keys) }} @translate('keys')
</div>
<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <th>@translate('key')</th>
            <th>@translate('exists')</th>
            <th>@translate('file')</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($keys as $key)
            <tr>
                <td>{{ $key['key'] }}</td>
                <td>{!! $key['exists']
                    ? '<span class="text-success">' . translate('yes') . '</span>'
                    : '<span class="text-danger">' . translate('no') . '</span>' !!}
                </td>
                <td>{{ $key['file'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
