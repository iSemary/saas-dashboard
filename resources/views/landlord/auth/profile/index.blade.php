@extends('layouts.landlord.app')
@section('content')
    <div class="card">
        <div class="card-header">
            @translate('my_account')
        </div>
        <div class="card-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-dark active" id="general-tab" data-toggle="tab" data-target="#general"
                        type="button" role="tab" aria-controls="general" aria-selected="true">
                        @translate('general')
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-dark" id="security-tab" data-toggle="tab" data-target="#security"
                        type="button" role="tab" aria-controls="security" aria-selected="false">
                        @translate('security')
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-dark" id="preferences-tab" data-toggle="tab" data-target="#preferences"
                        type="button" role="tab" aria-controls="preferences" aria-selected="false">
                        @translate('preferences')
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="profileTabsContent">
                <!-- General Tab -->
                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                    <form action="{{ route('landlord.profile.update') }}" class="edit-form" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="type" value="general">

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="name" class="form-label">@translate('name')</label>
                                            <input type="text" name="name" id="name" class="form-control"
                                                value="{{ isset($user) ? $user->name : '' }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="username" class="form-label">@translate('username')</label>
                                            <input type="text" name="username" id="username" class="form-control"
                                                value="{{ isset($user) ? $user->username : '' }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="row">
                                                <div class="col-12">
                                                    <label for="avatar" class="form-label">@translate('avatar')</label>
                                                </div>
                                            </div>
                                            <input type="file" name="avatar" id="avatar"
                                                class="border-0 form-control upload-image" accept="image/*">
                                        </div>
                                        <div class="col-6">
                                            <div class="text-revert">
                                                @if ($user->avatar)
                                                    <button type="button" class="btn text-danger btn-sm mt-2"
                                                        title="@translate('remove_avatar')" id="removeAvatar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                            <div class="preview-image-container mt-2">
                                                <img src="{{ $user->avatar }}" width="150px" height="150px" alt="Preview"
                                                    class="preview-image view-image" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">@translate('email')</label>
                                    <input type="email" name="email" id="email" class="form-control"
                                        value="{{ isset($user) ? $user->email : '' }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3 w-100">
                                    <label for="phone" class="form-label">@translate('phone')</label><br />
                                    <input type="tel" name="phone" id="phone"
                                        class="form-control intl-tel-input" required
                                        value="{{ isset($user) && $user->phone ? '+' . $user->phone : '' }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="address" class="form-label">@translate('address')</label>
                                    <input name="address" id="address" class="form-control"
                                        value="{{ isset($user) ? $user->address : '' }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="gender" class="form-label">@translate('gender')</label>
                                    <select name="gender" id="gender" class="form-control select2">
                                        <option value="">@translate('select')</option>
                                        <option value="male"
                                            {{ isset($user) && $user->gender == 'male' ? 'selected' : '' }}>
                                            @translate('male')</option>
                                        <option value="female"
                                            {{ isset($user) && $user->gender == 'female' ? 'selected' : '' }}>
                                            @translate('female')</option>
                                        <option value="other"
                                            {{ isset($user) && $user->gender == 'other' ? 'selected' : '' }}>
                                            @translate('other')</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="translationValue" class="form-label">@translate('language')</label>
                                    <select class="form-control select2" name="language_id" required>
                                        <option value="">@translate('select')</option>
                                        @foreach ($languages as $language)
                                            <option value="{{ $language->id }}"
                                                {{ isset($user) && $user->language_id == $language->id ? 'selected' : '' }}>
                                                {{ $language->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="country_id" class="form-label">@translate('country')</label>
                                    <select class="select2 form-control" name="country_id" required>
                                        <option value="">@translate('select')</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}"
                                                {{ isset($user) && $user->country_id == $country->id ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-status mb-3"></div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">@translate('update')</button>
                        </div>
                    </form>
                </div>

                <!-- Security Tab -->
                <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                    <form action="{{ route('landlord.profile.update') }}" class="edit-form" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="type" value="security">

                        2fa

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="current_password" class="form-label">@translate('current_password')</label>
                                    <input type="password" name="current_password" id="current_password"
                                        class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="new_password" class="form-label">@translate('new_password')</label>
                                    <input type="password" name="new_password" id="new_password" class="form-control"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="new_password_confirmation" class="form-label">@translate('confirm_new_password')</label>
                                    <input type="password" name="new_password_confirmation"
                                        id="new_password_confirmation" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-status mb-3"></div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">@translate('update_password')</button>
                        </div>
                    </form>
                </div>

                <!-- Preferences Tab -->
                <div class="tab-pane fade" id="preferences" role="tabpanel" aria-labelledby="preferences-tab">
                    <form action="{{ route('landlord.profile.update') }}" class="edit-form" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="type" value="preferences">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="theme_mode" class="form-label">@translate('theme_mode')</label>
                                    <select name="theme_mode" id="theme_mode" class="form-control select2">
                                        <option value="">@translate('select')</option>
                                        <option
                                            value="1"{{ isset($user) && $user->theme_mode == '1' ? 'selected' : '' }}>
                                            @translate('light')</option>
                                        <option
                                            value="2"{{ isset($user) && $user->theme_mode == '2' ? 'selected' : '' }}>
                                            @translate('dark')</option>
                                        <option
                                            value="3"{{ isset($user) && $user->theme_mode == '3' ? 'selected' : '' }}>
                                            @translate('system')</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-status mb-3"></div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">@translate('update_preferences')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/landlord/js/auth/profile/index.js') }}"></script>
@endsection
