<form action="{{ isset($row) ? route('tenant.brands.update', $row->id) : route('tenant.brands.store') }}"
    class="{{ isset($row) ? 'edit-form' : 'create-form' }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (isset($row))
        @method('PUT')
    @endif

    <div class="form-group">
        <label for="name" class="form-label">@translate('name') <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control emoji-input"
            value="{{ isset($row) ? $row->name : '' }}" required>
    </div>

    <div class="form-group">
        <label for="slug" class="form-label">@translate('slug') <span class="text-danger">*</span></label>
        <input type="text" name="slug" id="slug" class="form-control slug-input"
            value="{{ isset($row) ? $row->slug : '' }}" required>
    </div>

    <div class="form-group">
        <label for="description" class="form-label">@translate('description')</label>
        <textarea name="description" id="description" class="form-control emoji-input">{{ isset($row) ? $row->description : '' }}</textarea>
    </div>

    <div class="form-group">
        <label for="website" class="form-label">@translate('website')</label>
        <input type="url" name="website" id="website" class="form-control"
            value="{{ isset($row) ? $row->website : '' }}" placeholder="https://example.com">
    </div>

    <div class="form-group">
        <label for="email" class="form-label">@translate('email')</label>
        <input type="email" name="email" id="email" class="form-control"
            value="{{ isset($row) ? $row->email : '' }}" placeholder="contact@example.com">
    </div>

    <div class="form-group">
        <label for="phone" class="form-label">@translate('phone')</label>
        <input type="tel" name="phone" id="phone" class="form-control"
            value="{{ isset($row) ? $row->phone : '' }}" placeholder="+1234567890">
    </div>

    <div class="form-group">
        <label for="address" class="form-label">@translate('address')</label>
        <textarea name="address" id="address" class="form-control" rows="3" placeholder="@translate('enter_address')">{{ isset($row) ? $row->address : '' }}</textarea>
    </div>

    <div class="form-group">
        <label for="logo" class="form-label">@translate('logo')</label>
        <input type="file" name="logo" id="logo" class="border-0 form-control upload-image" accept="image/*">
        <div class="preview-image-container mt-2">
            <img src="{{ isset($row) && $row->logo ? $row->logo_url : asset('assets/shared/images/placeholder-brand.png') }}"
                width="100px" height="100px" alt="Preview" class="preview-image" />
        </div>
    </div>

    <div class="form-group">
        <label for="status" class="form-label">@translate('status')</label>
        <select name="status" id="status" class="form-control select2">
            <option value="active" {{ (isset($row) ? $row->status : 'active') == 'active' ? 'selected' : '' }}>@translate('active')</option>
            <option value="inactive" {{ (isset($row) ? $row->status : 'active') == 'inactive' ? 'selected' : '' }}>@translate('inactive')</option>
        </select>
    </div>

    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="form-group">
        <button type="submit"
            class="btn btn-{{ isset($row) ? 'primary' : 'success' }}">{{ isset($row) ? translate('update') : translate('create') }}</button>
    </div>
</form>

@section('scripts')
<script src="{{ asset('assets/tenant/js/customer/brands/editor.js') }}"></script>
@endsection
