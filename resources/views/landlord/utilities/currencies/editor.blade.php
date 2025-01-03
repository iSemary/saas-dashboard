<form action="{{ isset($row) ? route('landlord.currencies.update', $row->id) : route('landlord.currencies.store') }}"
    id="{{ isset($row) ? 'editForm' : 'createForm' }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (isset($row))
        @method('PUT')
    @endif

    <div class="form-group">
        <label for="code" class="form-label">@translate("code")</label>
        <input type="text" name="code" id="code" class="form-control"
            value="{{ isset($row) ? $row->code : '' }}" required>
    </div>

    <div class="form-group">
        <label for="name" class="form-label">@translate("name")</label>
        <input type="text" name="name" id="name" class="form-control"
            value="{{ isset($row) ? $row->name : '' }}" required>
    </div>

    <div class="form-group">
        <label for="symbol" class="form-label">@translate("symbol")</label>
        <input type="text" name="symbol" id="symbol" class="form-control"
            value="{{ isset($row) ? $row->symbol : '' }}">
    </div>

    <div class="form-group">
        <label for="decimal_places" class="form-label">@translate("decimal_places")</label>
        <input type="number" name="decimal_places" id="decimal_places" class="form-control"
            value="{{ isset($row) ? $row->decimal_places : '2' }}">
    </div>

    <div class="form-group">
        <label for="exchange_rate" class="form-label">@translate("exchange_rate")</label>
        <input type="text" name="exchange_rate" id="exchange_rate" class="form-control"
            value="{{ isset($row) ? $row->exchange_rate : '1' }}">
    </div>

    <div class="form-group">
        <label for="exchange_rate_last_updated" class="form-label">@translate("exchange_rate_last_updated")</label>
        <input type="datetime-local" name="exchange_rate_last_updated" id="exchange_rate_last_updated" class="form-control"
            value="{{ isset($row) ? $row->exchange_rate_last_updated : '' }}">
    </div>

    <div class="form-group">
        <label for="symbol_position" class="form-label">@translate("symbol_position")</label>
        <select name="symbol_position" id="symbol_position" class="form-control" required>
            <option value="left" {{ isset($row) && $row->symbol_position == 'left' ? 'selected' : '' }}>
                @translate("left")
            </option>
            <option value="right" {{ isset($row) && $row->symbol_position == 'right' ? 'selected' : '' }}>
                @translate("right")
            </option>
        </select>
    </div>

    <div class="form-group">
        <label class="checkbox-inline">
            <input type="checkbox" name="base_currency" class="form-toggle" {{ isset($row) && $row->base_currency ? 'checked' : '' }} data-toggle="toggle"> @translate('base_currency')
        </label>
    </div>

    <div class="form-group">
        <label for="priority" class="form-label">@translate("priority")</label>
        <input type="number" name="priority" id="priority" class="form-control"
            value="{{ isset($row) ? $row->priority : '1' }}">
    </div>

    <div class="form-group">
        <label for="note" class="form-label">@translate("note")</label>
        <textarea name="note" id="note" class="form-control">{{ isset($row) ? $row->note : '' }}</textarea>
    </div>

    <div class="form-group">
        <label class="checkbox-inline">
            <input type="checkbox" name="status" class="form-toggle" {{ isset($row) && $row->status ? 'checked' : '' }} data-toggle="toggle"> @translate('status')
        </label>
    </div>

    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="form-group">
        <button type="submit"
            class="btn btn-{{ isset($row) ? 'primary' : 'success' }}">{{ isset($row) ? translate('update') : translate('create') }}</button>
    </div>
</form>
