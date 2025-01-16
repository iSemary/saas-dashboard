<form action="{{ isset($row) ? route('landlord.types.update', $row->id) : route('landlord.types.store') }}"
    id="{{ isset($row) ? 'editForm' : 'createForm' }}" method="POST" enctype="multipart/form-data">
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
        <label for="slug" class="form-label">@translate('slug')</label>
        <input type="text" name="slug" id="slug" class="form-control slug-input"
            value="{{ isset($row) ? $row->slug : '' }}" required>
    </div>

    <div class="form-group">
        <label for="description" class="form-label">@translate('description')</label>
        <textarea name="description" id="description" class="form-control">{{ isset($row) ? $row->description : '' }}</textarea>
    </div>

    <div class="form-group">
        <label for="icon" class="form-label">@translate('icon')</label>
        <input type="file" name="icon" id="icon" class="border-0 form-control upload-image" accept="image/*">
        <div class="preview-image-container mt-2">
            <img src="{{ isset($row) && $row->icon ? $row->icon : asset('assets/global/images/icons/defaults/image.png') }}"
                width="100px" height="100px" alt="Preview" class="preview-image" />
        </div>
    </div>
    
    <div class="form-group">
        <label for="priority" class="form-label">@translate('priority')</label>
        <input type="number" name="priority" id="priority" class="form-control"
            value="{{ isset($row) ? $row->priority : 0 }}">
    </div>

    <div class="form-group">
        <label for="status" class="form-label">@translate('status')</label>
        <select name="status" id="status" class="form-control">
            @foreach ($statusOptions as $status)
                <option value="{{ $status }}" {{ isset($row) && $row->status == $status ? 'selected' : '' }}>
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
            class="btn btn-{{ isset($row) ? 'primary' : 'success' }}">{{ isset($row) ? translate('update') : translate('create') }}</button>
    </div>
</form>
