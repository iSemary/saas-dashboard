<form action="{{ isset($row) ? route('landlord.provinces.update', $row->id) : route('landlord.provinces.store') }}"
    id="{{ isset($row) ? 'editForm' : 'createForm' }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (isset($row))
        @method('PUT')
    @endif

    <div class="form-group">
        <label for="country_id" class="form-label">@translate('country')</label>
        <select class="select2 form-control" name="country_id" required>
            <option value="">@translate('select')</option>
            @foreach ($countries as $country)
                <option value="{{ $country->id }}"
                    {{ isset($row) && $row->country_id == $country->id ? 'selected' : '' }}>{{ $country->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="name" class="form-label">@translate('name')</label>
        <input type="text" name="name" id="name" class="form-control"
            value="{{ isset($row) ? $row->name : '' }}" required>
    </div>

    <div class="form-group">
        <label class="checkbox-inline">
            <input type="checkbox" name="is_capital" class="form-toggle" {{ isset($row) && $row->is_capital ? 'checked' : '' }} data-toggle="toggle"> @translate('is_capital')
        </label>
    </div>

    <div class="form-group">
        <label for="flag" class="form-label">@translate('flag')</label>
        <input type="file" name="flag" id="flag" class="border-0 form-control upload-image" accept="image/*">
    </div>

    <div class="form-group">
        <img src="{{ isset($row) && $row->flag ? $row->flag : 'https://placehold.co/100x100/EEE/31343C' }}"
            width="100px" height="100px" alt="Preview" class="preview-image" />
    </div>

    <div class="form-group">
        <label for="phone_code" class="form-label">@translate('phone_code')</label>
        <input type="text" name="phone_code" id="phone_code" class="form-control"
            value="{{ isset($row) ? $row->phone_code : '' }}" required>
    </div>

    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="form-group">
        <button type="submit"
            class="btn btn-{{ isset($row) ? 'primary' : 'success' }}">{{ isset($row) ? translate('update') : translate('create') }}</button>
    </div>
</form>
