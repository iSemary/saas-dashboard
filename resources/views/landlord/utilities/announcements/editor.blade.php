<form
    action="{{ isset($row) ? route('landlord.announcements.update', $row->id) : route('landlord.announcements.store') }}"
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
        <label for="description" class="form-label">@translate('description')</label>
        <textarea name="description" id="description" class="form-control">{{ isset($row) ? $row->description : '' }}</textarea>
    </div>

    <div class="form-group">
        <label for="body" class="form-label">@translate('body')</label>
        <textarea name="body" id="ckInput" class="form-control ckeditor">{{ isset($row) ? $row->body : '' }}</textarea>
    </div>

    <div class="form-group">
        <label for="start_at" class="form-label">@translate('start_at')</label>
        <input type="datetime-local" name="start_at" id="start_at" class="form-control"
            value="{{ isset($row) ? $row->start_at : '' }}">
    </div>

    <div class="form-group">
        <label for="end_at" class="form-label">@translate('end_at')</label>
        <input type="datetime-local" name="end_at" id="end_at" class="form-control"
            value="{{ isset($row) ? $row->end_at : '' }}">
    </div>

    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="form-group">
        <button type="submit"
            class="btn btn-{{ isset($row) ? 'primary' : 'success' }}">{{ isset($row) ? translate('update') : translate('create') }}</button>
    </div>
</form>
