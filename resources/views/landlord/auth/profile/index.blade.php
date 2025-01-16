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
                <button class="nav-link active" id="general-tab" data-toggle="tab" data-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                    @translate('general')
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="security-tab" data-toggle="tab" data-target="#security" type="button" role="tab" aria-controls="security" aria-selected="false">
                    @translate('security')
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="preferences-tab" data-toggle="tab" data-target="#preferences" type="button" role="tab" aria-controls="preferences" aria-selected="false">
                    @translate('preferences')
                </button>
            </li>
        </ul>

        <div class="tab-content" id="profileTabsContent">
            <!-- General Tab -->
            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                <form action="{{ route('landlord.profile.update') }}" id="editForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">@translate('name')</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="{{ isset($user) ? $user->name : '' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="username" class="form-label">@translate('username')</label>
                                <input type="text" name="username" id="username" class="form-control"
                                    value="{{ isset($user) ? $user->username : '' }}" required>
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
                            <div class="form-group mb-3">
                                <label for="phone" class="form-label">@translate('phone')</label>
                                <input type="tel" name="phone" id="phone" class="form-control"
                                    value="{{ isset($user) ? $user->phone : '' }}">
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
                <form action="{{ route('landlord.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="current_password" class="form-label">@translate('current_password')</label>
                                <input type="password" name="current_password" id="current_password" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="new_password" class="form-label">@translate('new_password')</label>
                                <input type="password" name="new_password" id="new_password" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="new_password_confirmation" class="form-label">@translate('confirm_new_password')</label>
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">@translate('update_password')</button>
                    </div>
                </form>
            </div>

            <!-- Preferences Tab -->
            <div class="tab-pane fade" id="preferences" role="tabpanel" aria-labelledby="preferences-tab">
                <form action="{{ route('landlord.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="language" class="form-label">@translate('preferred_language')</label>
                                <select name="language" id="language" class="form-select">
                                    <option value="en" {{ isset($user) && $user->language == 'en' ? 'selected' : '' }}>English</option>
                                    <option value="es" {{ isset($user) && $user->language == 'es' ? 'selected' : '' }}>Spanish</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="country" class="form-label">@translate('country')</label>
                                <select name="country_id" id="country" class="form-select">
                                    
                                </select>
                            </div>
                        </div>
                    </div>

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