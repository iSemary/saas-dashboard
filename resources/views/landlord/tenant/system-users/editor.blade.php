<form action="{{ isset($row) ? route('landlord.system-users.update', $row->id) : route('landlord.system-users.store') }}"
    id="{{ isset($row) ? 'editForm' : 'createForm' }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (isset($row))
        @method('PUT')
    @endif

    <div class="row">
        <div class="form-group col-6">
            <label for="name" class="form-label">@translate('name')</label>
            <input type="text" name="name" id="name" class="form-control"
                value="{{ isset($row) ? $row->name : '' }}" required>
        </div>
        <div class="form-group col-6">
            <label for="username" class="form-label">@translate('username')</label>
            <input type="text" name="username" id="username" class="form-control"
                value="{{ isset($row) ? $row->username : '' }}" required>
        </div>
        <div class="form-group col-6"
            data-loader-image="{{ asset('assets/global/images/icons/animated/loaders/loader.gif') }}"
            data-invalid-email-format-message="{{ translate('please_enter_a_valid_email_address') }}"
            data-id="{{ isset($row) ? $row->id : '' }}"
            data-email-check-route="{{ route('landlord.system-users.check-email') }}">
            <label for="email" class="form-label">@translate('email')</label>
            <input type="email" name="email" id="email" class="form-control email-checker"
                value="{{ isset($row) ? $row->email : '' }}" required>
        </div>
        <div class="form-group col-6">
            <label for="role_id" class="form-label">@translate('role')</label>
            <select name="role_id" id="role_id" class="form-control select2" disabled>
                <option value="">
                    @translate('landlord')
                </option>
            </select>
        </div>
        <div class="form-group col-6">
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
        <div class="form-group col-6">
            <label for="language_id" class="form-label">@translate('language')</label>
            <select class="select2 form-control" name="language_id" required>
                <option value="">@translate('select')</option>
                @foreach ($languages as $language)
                    <option value="{{ $language->id }}"
                        {{ isset($row) && $row->language_id == $language->id ? 'selected' : '' }}>
                        {{ $language->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-6 generate-password-container">
            <label for="password" class="form-label generate-password-field">@translate('password')</label>
            <input type="password" name="password" id="password" class="form-control generate-password-input"
                value="" required>
            <!-- Password strength indicator -->
            <div class="progress mt-2" style="height: 5px;">
                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
            </div>
            <!-- Password requirements list -->
            <ul class="requirement-list">
                <li class="length"><i class="fas fa-hourglass-end"></i> @translate('at_least_8_characters_length')</li>
                <li class="lowercase"><i class="fas fa-hourglass-end"></i> @translate('at_least_1_lowercase_letter')</li>
                <li class="uppercase"><i class="fas fa-hourglass-end"></i> @translate('at_least_1_uppercase_letter')</li>
                <li class="number"><i class="fas fa-hourglass-end"></i> @translate('at_least_1_number')</li>
                <li class="special"><i class="fas fa-hourglass-end"></i> @translate('at_least_1_special_character')</li>
            </ul>
        </div>
    </div>
    <div class="form-group">
        <label for="permissions" class="form-label">@translate('permissions')</label>
        <div class="card">
            <div class="card-body">
                @php
                    $groupedPermissions = [];
                    foreach ($permissions as $permission) {
                        $parts = explode('.', $permission->name);
                        $group = end($parts);
                        $action = $parts[0]; // Get the action part (view, create, etc.)

                        if (!isset($groupedPermissions[$group])) {
                            $groupedPermissions[$group] = [];
                        }
                        $groupedPermissions[$group][] = [
                            'action' => $action,
                            'permission' => $permission,
                        ];
                    }
                    ksort($groupedPermissions);
                @endphp

                @foreach ($groupedPermissions as $group => $groupPermissions)
                    <div class="mb-2">
                        <h5 class="mb-2"><b>{{ translate($group) }}</b></h5>
                        <div class="d-flex">
                            @foreach ($groupPermissions as $permissionData)
                                <div class="form-check mx-3">
                                    <input type="checkbox" name="permissions[]"
                                        id="permission_{{ $permissionData['permission']->id }}"
                                        class="form-check-input" value="{{ $permissionData['permission']->id }}"
                                        {{ isset($row) ? ($row->permissions->contains($permissionData['permission']) ? 'checked' : '') : 'checked' }}>
                                    <label for="permission_{{ $permissionData['permission']->id }}"
                                        class="form-check-label">
                                        {{ translate($permissionData['action']) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="form-status"></div>
    </div>

    <div class="form-group">
        <button type="submit"
            class="btn btn-{{ isset($row) ? 'primary' : 'success' }}">{{ isset($row) ? translate('update') : translate('create') }}</button>
    </div>
</form>

<script src="{{ asset('assets/landlord/js/tenant/system-users/editor.js') }}"></script>
