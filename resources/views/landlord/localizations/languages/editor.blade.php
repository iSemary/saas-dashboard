<form action="{{ isset($row) ? route('landlord.languages.update', $row->id) : route('landlord.languages.store') }}"
    class="{{ isset($row) ? 'edit-form' : 'create-form' }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (isset($row))
        @method('PUT')
    @endif
    <div class="form-group">
        <label for="name" class="form-label">@translate("name")</label>
        <input type="text" name="name" id="name" class="form-control"
            value="{{ isset($row) ? $row->name : '' }}" required>
    </div>

    <div class="form-group">
        <label for="locale" class="form-label">@translate("locale")</label>
        <input type="text" name="locale" id="locale" class="form-control"
            value="{{ isset($row) ? $row->locale : '' }}" required>
    </div>

    <div class="form-group">
        <label for="direction" class="form-label">@translate('direction')</label>
        <select name="direction" id="direction" class="form-control">
            @foreach ($directionOptions as $directionOption)
                <option value="{{ $directionOption }}" {{ isset($row) && $row->direction == $directionOption ? 'selected' : '' }}>
                    @translate($directionOption)
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
