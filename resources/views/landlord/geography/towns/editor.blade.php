<form action="{{ isset($row) ? route('landlord.towns.update', $row->id) : route('landlord.towns.store') }}"
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
        <label for="code" class="form-label">@translate('city')</label>
        <select class="select2 form-control" name="city_id" required>
            <option value="">@translate('select')</option>
            @foreach ($cities as $city)
                <option value="{{ $city->id }}"
                    {{ isset($row) && $row->city_id == $city->id ? 'selected' : '' }}>{{ $city->name }}
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
