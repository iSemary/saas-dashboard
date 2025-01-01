<form action="{{ isset($row) ? route('landlord.countries.update', $row->id) : route('landlord.countries.store') }}"
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
        <label for="code" class="form-label">Code</label>
        <input type="text" name="code" id="code" class="form-control"
            value="{{ isset($row) ? $row->code : '' }}" required>
    </div>

    @if (isset($row))
        <div class="form-group">
            <label for="capital_city_id" class="form-label">Capital City ID</label>
            <input type="number" name="capital_city_id" id="capital_city_id" class="form-control" value=""
                required>
        </div>
    @endif

    <div class="form-group">
        <label for="region" class="form-label">Region</label>
        <input type="text" name="region" id="region" class="form-control"
            value="{{ isset($row) ? $row->region : '' }}" required>
    </div>

    <div class="form-group">
        <label for="flag" class="form-label">Flag</label>
        <input type="file" name="flag" id="flag" class="border-0 form-control upload-image" accept="image/*">
    </div>

    <div class="form-group">
        <img src="{{ isset($row) && $row->flag ? $row->flag : 'https://placehold.co/100x100/EEE/31343C' }}"
            width="100px" height="100px" alt="Preview" class="preview-image" />
    </div>

    <div class="form-group">
        <label for="phone_code" class="form-label">Phone Code</label>
        <input type="text" name="phone_code" id="phone_code" class="form-control"
            value="{{ isset($row) ? $row->phone_code : '' }}" required>
    </div>

    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="form-group">
        <button type="submit"
            class="btn btn-{{ isset($row) ? 'primary' : 'success' }}">{{ isset($row) ? 'Update' : 'Create' }}</button>
    </div>
</form>
