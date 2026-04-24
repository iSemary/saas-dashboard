<form action="{{ isset($brand) ? route('landlord.brands-web.update', $brand->id) : route('landlord.brands-web.store') }}"
    class="{{ isset($brand) ? 'edit-form' : 'create-form' }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (isset($brand))
        @method('PUT')
    @endif

    <div class="form-group">
        <label for="name" class="form-label">@translate('name') <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control emoji-input"
            value="{{ isset($brand) ? $brand->name : '' }}" required>
    </div>

    <div class="form-group">
        <label for="slug" class="form-label">@translate('slug') <span class="text-danger">*</span></label>
        <input type="text" name="slug" id="slug" class="form-control slug-input"
            value="{{ isset($brand) ? $brand->slug : '' }}" required>
    </div>

    <div class="form-group">
        <label for="description" class="form-label">@translate('description')</label>
        <textarea name="description" id="description" class="form-control emoji-input">{{ isset($brand) ? $brand->description : '' }}</textarea>
    </div>

    <div class="form-group">
        <label for="tenant_id" class="form-label">@translate('tenant') <span class="text-danger">*</span></label>
        <select name="tenant_id" id="tenant_id" class="form-control select2" required>
            <option value="">@translate('select_tenant')</option>
            @foreach (Modules\Tenant\Entities\Tenant::all() as $tenant)
                <option value="{{ $tenant->id }}"
                    {{ isset($brand) && $brand->tenant_id == $tenant->id ? 'selected' : '' }}>
                    {{ $tenant->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="logo" class="form-label">@translate('logo')</label>
        <input type="file" name="logo" id="logo" class="border-0 form-control upload-image" accept="image/*">
        <div class="preview-image-container mt-2">
            <img src="{{ isset($brand) && $brand->logo ? asset('storage/' . $brand->logo) : asset('assets/shared/images/icons/defaults/image.png') }}"
                width="100px" height="100px" alt="Preview" class="preview-image" />
        </div>
    </div>

    <div class="form-group">
        <label for="website" class="form-label">@translate('website')</label>
        <input type="url" name="website" id="website" class="form-control"
            value="{{ isset($brand) ? $brand->website : '' }}">
    </div>

    <div class="form-group">
        <label for="email" class="form-label">@translate('email')</label>
        <input type="email" name="email" id="email" class="form-control"
            value="{{ isset($brand) ? $brand->email : '' }}">
    </div>

    <div class="form-group">
        <label for="phone" class="form-label">@translate('phone')</label>
        <input type="text" name="phone" id="phone" class="form-control"
            value="{{ isset($brand) ? $brand->phone : '' }}">
    </div>

    <div class="form-group">
        <label for="address" class="form-label">@translate('address')</label>
        <textarea name="address" id="address" class="form-control">{{ isset($brand) ? $brand->address : '' }}</textarea>
    </div>

    <div class="form-group">
        <label for="status" class="form-label">@translate('status')</label>
        <select name="status" id="status" class="form-control select2">
            @foreach ($statusOptions as $status)
                <option value="{{ $status }}" {{ isset($brand) && $brand->status == $status ? 'selected' : '' }}>
                    @translate($status)
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="form-group">
        <button type="submit"
            class="btn btn-{{ isset($brand) ? 'primary' : 'success' }}">{{ isset($brand) ? translate('update') : translate('create') }}</button>
    </div>
</form>