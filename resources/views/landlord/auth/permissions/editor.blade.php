<form action="{{ isset($row) ? route('landlord.permissions.update', $row->id) : route('landlord.permissions.store') }}"
      class="{{ isset($row) ? 'edit-form' : 'create-form' }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (isset($row))
        @method('PUT')
    @endif
    <div class="form-group">
        <label for="name" class="form-label">@translate('name') <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control"
               value="{{ isset($row) ? $row->name : '' }}" required>
    </div>

    <div class="form-group">
        <label for="guard_name" class="form-label">@translate('guard_name') <span class="text-danger">*</span></label>
        <input type="text" name="guard_name" id="guard_name" class="form-control"
               value="{{ isset($row) ? $row->guard_name : '' }}" required>
    </div>

    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-{{ isset($row) ? 'primary' : 'success' }}">{{ isset($row) ? translate('update') : translate('create') }}</button>
    </div>
</form>
