<form action="{{ isset($row) ? route('landlord.languages.update', $row->id) : route('landlord.languages.store') }}"
    id="{{ isset($row) ? 'editForm' : 'createForm' }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (isset($row))
        @method('PUT')
    @endif
    <div class="form-group">
        <label for="name" class="form-label">Name</label>
        <input type="text" name="name" id="name" class="form-control"
            value="{{ isset($row) ? $row->name : '' }}" required>
    </div>

    <div class="form-group">
        <label for="locale" class="form-label">Key</label>
        <input type="text" name="locale" id="locale" class="form-control"
            value="{{ isset($row) ? $row->locale : '' }}" required>
    </div>

    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="form-group">
        <button type="submit"
            class="btn btn-{{ isset($row) ? 'primary' : 'success' }}">{{ isset($row) ? 'Update' : 'Create' }}</button>
    </div>
</form>
