<form action="{{ isset($row) ? route('landlord.streets.update', $row->id) : route('landlord.streets.store') }}"
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
        <label for="town" class="form-label">@translate('town')</label>
        <select class="select2 form-control" name="town_id" required>
            <option value="">@translate('select')</option>
            @foreach ($towns as $town)
                <option value="{{ $town->id }}"
                    {{ isset($row) && $row->town_id == $town->id ? 'selected' : '' }}>{{ $town->name }}
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
