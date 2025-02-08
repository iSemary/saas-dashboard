<form action="{{ isset($row) ? route('landlord.countries.update', $row->id) : route('landlord.countries.store') }}"
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
        <label for="code" class="form-label">@translate('code') <span class="text-danger">*</span></label>
        <input type="text" name="code" id="code" class="form-control"
            value="{{ isset($row) ? $row->code : '' }}" required>
    </div>

    <div class="form-group">
        <label for="region" class="form-label">@translate('region') <span class="text-danger">*</span></label>
        <input type="text" name="region" id="region" class="form-control"
            value="{{ isset($row) ? $row->region : '' }}" required>
    </div>

    <div class="form-group">
        <label for="flag" class="form-label">@translate('flag')</label>
        <input type="file" name="flag" id="flag" class="border-0 form-control upload-image" accept="image/*">
        <div class="preview-image-container mt-2">
            <img src="{{ isset($row) && $row->flag ? $row->flag : asset('assets/shared/images/icons/defaults/image.png') }}"
                width="100px" height="100px" alt="Preview" class="preview-image" />
        </div>
    </div>

    <div class="form-group">
        <label for="phone_code" class="form-label">@translate('phone_code') <span class="text-danger">*</span></label>
        <input type="text" name="phone_code" id="phone_code" class="form-control"
            value="{{ isset($row) ? $row->phone_code : '' }}" required>
    </div>


    <div class="form-group">
        <label for="timezone" class="form-label">@translate('timezone') <span class="text-danger">*</span></label>
        <input type="text" name="timezone" id="timezone" class="form-control"
            value="{{ isset($row) ? $row->timezone : '' }}" required>
    </div>

    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="form-group">
        <button type="submit"
            class="btn btn-{{ isset($row) ? 'primary' : 'success' }}">{{ isset($row) ? translate('update') : translate('create') }}</button>
    </div>
</form>
