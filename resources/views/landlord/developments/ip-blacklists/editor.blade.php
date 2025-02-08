<form
    action="{{ isset($row) ? route('landlord.development.ip-blacklists.update', $row->id) : route('landlord.development.ip-blacklists.store') }}"
    class="{{ isset($row) ? 'edit-form' : 'create-form' }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (isset($row))
        @method('PUT')
    @endif
    <div class="form-group">
        <label for="ip_address" class="form-label">@translate('ip_address') <span class="text-danger">*</span></label>
        <input type="text" name="ip_address" id="ip_address" class="form-control"
            value="{{ isset($row) ? $row->ip_address : '' }}" required>
    </div>

    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="form-group">
        <button type="submit"
            class="btn btn-{{ isset($row) ? 'primary' : 'success' }}">{{ isset($row) ? translate('update') : translate('create') }}</button>
    </div>
</form>
