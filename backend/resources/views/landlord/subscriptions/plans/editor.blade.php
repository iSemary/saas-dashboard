<form action="{{ isset($row) ? route('landlord.plans.update', $row->id) : route('landlord.plans.store') }}"
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
        <label for="slug" class="form-label">@translate('slug') <span class="text-danger">*</span></label>
        <input type="text" name="slug" id="slug" class="form-control slug-input"
            value="{{ isset($row) ? $row->slug : '' }}" required>
    </div>

    <div class="form-group">
        <label for="description" class="form-label">@translate('description')</label>
        <textarea name="description" id="description" class="form-control">{{ isset($row) ? $row->description : '' }}</textarea>
    </div>

    <div class="form-group">
        <label for="features_summary" class="form-label">@translate('features_summary')</label>
        <textarea name="features_summary" id="features_summary" class="form-control">{{ isset($row) ? $row->features_summary : '' }}</textarea>
    </div>

    <div class="form-group">
        <label for="sort_order" class="form-label">@translate('sort_order')</label>
        <input type="number" name="sort_order" id="sort_order" class="form-control"
            value="{{ isset($row) ? $row->sort_order : 0 }}">
    </div>

    <div class="form-group">
        <div class="form-check">
            <input type="checkbox" name="is_popular" id="is_popular" class="form-check-input" value="1"
                {{ isset($row) && $row->is_popular ? 'checked' : '' }}>
            <label for="is_popular" class="form-check-label">@translate('is_popular')</label>
        </div>
    </div>

    <div class="form-group">
        <div class="form-check">
            <input type="checkbox" name="is_custom" id="is_custom" class="form-check-input" value="1"
                {{ isset($row) && $row->is_custom ? 'checked' : '' }}>
            <label for="is_custom" class="form-check-label">@translate('is_custom')</label>
        </div>
    </div>

    <div class="form-group">
        <label for="status" class="form-label">@translate('status')</label>
        <select name="status" id="status" class="form-control select2">
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
