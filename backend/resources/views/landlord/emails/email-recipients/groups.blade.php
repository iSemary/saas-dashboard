<form
    action="{{ route('landlord.email-recipients.assignGroups', $id) }}"
    class="{{ 'edit-form' }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (isset($row))
        @method('PUT')
    @endif

    
    <div class="form-group">
        <label for="groups" class="form-label">@translate('groups')</label>
        <select name="groups[]" multiple id="groups" class="form-control select2">
            @foreach ($groups as $group)
                <option value="{{ $group->id }}" {{ in_array($group->id, $recipientGroups) ? 'selected' : '' }}>
                    {{ $group->name }}
                </option>
            @endforeach
        </select>
    </div>
    
    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="form-group">
        <button type="submit"
            class="btn btn-{{ 'primary' }}">{{  translate('update') }}</button>
    </div>
</form>
