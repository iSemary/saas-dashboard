<form action="{{ isset($row) ? route('landlord.categories.update', $row->id) : route('landlord.categories.store') }}"
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
        <input type="text" name="slug" id="slug" class="form-control"
            value="{{ isset($row) ? $row->slug : '' }}" required>
    </div>

    <div class="form-group">
        <label for="description" class="form-label">@translate('description')</label>
        <textarea name="description" id="description" class="form-control">{{ isset($row) ? $row->description : '' }}</textarea>
    </div>

    <div class="form-group">
        <label for="parent_id" class="form-label">@translate('parent_category')</label>
        <select name="parent_id" id="parent_id" class="form-control select2">
            <option value="">@translate('select_parent_category')</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    {{ isset($row) && $row->parent_id == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="icon" class="form-label">@translate('icon')</label><br/>
        <input type="file" name="icon" id="icon" class="">
        @if (isset($row) && $row->icon)
            <img src="{{ asset('path/to/icons/' . $row->icon) }}" alt="@translate('icon')" width="50">
        @endif
    </div>

    <div class="form-group">
        <label for="type" class="form-label">@translate('type')</label>
        <input type="number" name="type" id="type" class="form-control"
            value="{{ isset($row) ? $row->type : '' }}">
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
