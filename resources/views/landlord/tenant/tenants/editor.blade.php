<form action="{{ isset($row) ? route('landlord.tenants.update', $row->id) : route('landlord.tenants.store') }}"
    class="{{ isset($row) ? 'edit-form' : 'create-form' }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (isset($row))
        @method('PUT')
    @endif
    <div class="form-group">
        <label for="name" class="form-label">@translate('name')</label>
        <input type="text" name="name" id="name" class="form-control"
            value="{{ isset($row) ? $row->name : '' }}" required>
    </div>

    <div class="form-group">
        <label for="domain" class="form-label">@translate('domain')</label>
        <input type="text" name="domain" id="domain" class="form-control"
            value="{{ isset($row) ? $row->domain : '' }}" required>
    </div>

    <div class="form-group">
        <label for="database" class="form-label">@translate('database')</label>
        <input type="text" name="database" id="database" class="form-control"
            value="{{ isset($row) ? $row->database : '' }}" required>
    </div>

    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="form-group">
        <button type="submit"
            class="btn btn-{{ isset($row) ? 'primary' : 'success' }}">{{ isset($row) ? translate('update') : translate('create') }}</button>
    </div>
</form>
