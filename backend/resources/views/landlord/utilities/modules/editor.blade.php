<form action="{{ isset($row) ? route('landlord.modules.update', $row->id) : route('landlord.modules.store') }}"
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
        <label for="module_key" class="form-label">@translate('module_key') <span class="text-danger">*</span></label>
        <input type="text" name="module_key" id="module_key" class="form-control snake-input"
            value="{{ isset($row) ? $row->module_key : '' }}" required>
    </div>

    <div class="form-group">
        <label for="route" class="form-label">@translate('route') <span class="text-danger">*</span></label>
        <input type="text" name="route" id="route" class="form-control"
            value="{{ isset($row) ? $row->route : '' }}" required>
    </div>

    <div class="form-group">
        <label for="description" class="form-label">@translate('description')</label>
        <textarea name="description" id="description" class="form-control">{{ isset($row) ? $row->description : '' }}</textarea>
    </div>

    <div class="form-group">
        <label for="slogan" class="form-label">@translate('slogan')</label>
        <textarea name="slogan" id="slogan" class="form-control">{{ isset($row) ? $row->slogan : '' }}</textarea>
    </div>

    <div class="form-group">
        <label for="icon" class="form-label">@translate('icon')</label><br />
        <input type="file" name="icon" id="icon" class="">
        @if (isset($row) && $row->icon)
            <img src="{{ asset('path/to/icons/' . $row->icon) }}" alt="@translate('icon')" width="50">
        @endif
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
