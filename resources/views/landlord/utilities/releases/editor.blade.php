<form action="{{ isset($row) ? route('landlord.releases.update', $row->id) : route('landlord.releases.store') }}"
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
        <label for="name" class="form-label">@translate('object_model')</label>
        <select name="object_model" id="object_model" class="select2 form-control">
            @foreach ($modelOptions as $model)
                <option value="{{ $model }}" {{ isset($row) && $row->object_model == $model ? 'selected' : '' }}>
                    @translate($model)
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="object_id" class="form-label">@translate('object_id')</label>
        <input type="text" name="object_id" id="object_id" class="form-control"
            value="{{ isset($row) ? $row->object_id : '' }}" required>
    </div>

    <div class="form-group">
        <label for="version" class="form-label">@translate('version')</label>
        <input type="text" name="version" id="version" class="form-control"
            value="{{ isset($row) ? $row->version : '' }}" required>
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
        <label for="body" class="form-label">@translate('body')</label>
        <textarea name="body" id="body" class="form-control">{{ isset($row) ? $row->body : '' }}</textarea>
    </div>

    <div class="form-group">
        <label for="release_date" class="form-label">@translate('release_date')</label>
        <input type="datetime-local" name="release_date" id="release_date" class="form-control"
            value="{{ isset($row) ? $row->release_date : '' }}">
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
