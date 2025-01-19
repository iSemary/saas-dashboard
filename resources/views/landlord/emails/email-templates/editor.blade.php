<form
    action="{{ isset($row) ? route('landlord.email-templates.update', $row->id) : route('landlord.email-templates.store') }}"
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
        <label for="subject" class="form-label">@translate('subject')</label>
        <input type="text" name="subject" id="subject" class="form-control"
            value="{{ isset($row) ? $row->subject : '' }}" required>
    </div>

    <div class="form-group">
        <label for="body" class="form-label">@translate('body')</label>
        <textarea name="body" id="ckInput" class="form-control ckeditor">{{ isset($row) ? $row->body : '' }}</textarea>
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
