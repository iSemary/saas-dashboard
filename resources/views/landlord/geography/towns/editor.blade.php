<form action="{{ isset($row) ? route('landlord.towns.update', $row->id) : route('landlord.towns.store') }}"
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
        <label for="postalcode" class="form-label">@translate('postalcode')</label>
        <input type="text" name="postalcode" id="postalcode" class="form-control"
            value="{{ isset($row) ? $row->postalcode : '' }}">
    </div>

    <div class="form-group">
        <label for="code" class="form-label">@translate('city') <span class="text-danger">*</span></label>
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
        <label for="latitude" class="form-label">@translate('latitude')</label>
        <input type="number" step="any" name="latitude" id="latitude" class="form-control"
            value="{{ isset($row) ? $row->latitude : '' }}">
    </div>

    <div class="form-group">
        <label for="longitude" class="form-label">@translate('longitude')</label>
        <input type="number" step="any" name="longitude" id="longitude" class="form-control"
            value="{{ isset($row) ? $row->longitude : '' }}">
    </div>

    <div class="form-group">
        <label for="area_km2" class="form-label">@translate('area_km2')</label>
        <input type="number" step="0.01" name="area_km2" id="area_km2" class="form-control"
            value="{{ isset($row) ? $row->area_km2 : '' }}">
    </div>

    <div class="form-group">
        <label for="population" class="form-label">@translate('population')</label>
        <input type="number" name="population" id="population" class="form-control"
            value="{{ isset($row) ? $row->population : '' }}">
    </div>

    <div class="form-group">
        <label for="elevation_m" class="form-label">@translate('elevation_m')</label>
        <input type="number" step="0.01" name="elevation_m" id="elevation_m" class="form-control"
            value="{{ isset($row) ? $row->elevation_m : '' }}">
    </div>

    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="form-group">
        <button type="submit"
            class="btn btn-{{ isset($row) ? 'primary' : 'success' }}">{{ isset($row) ? translate('update') : translate('create') }}</button>
    </div>
</form>
