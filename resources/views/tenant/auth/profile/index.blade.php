@extends('layouts.tenant.app')

@section('title', translate('my_profile'))

@section('styles')
<style>
.profile-card {
    box-shadow: 0 0 20px rgba(0,0,0,0.08);
    border-radius: 10px;
}
.tab-button {
    transition: all 0.3s ease;
}
.tab-button.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white !important;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card profile-card">
                <div class="card-header">
                    <h3 class="card-title">@translate('my_profile')</h3>
                </div>
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab">
                                <i class="fas fa-user mr-2"></i>@translate('general')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="security-tab" data-toggle="tab" href="#security" role="tab">
                                <i class="fas fa-lock mr-2"></i>@translate('security')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="preferences-tab" data-toggle="tab" href="#preferences" role="tab">
                                <i class="fas fa-sliders-h mr-2"></i>@translate('preferences')
                            </a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content mt-3">
                        <!-- General Tab -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <form id="generalForm" onsubmit="event.preventDefault(); updateProfile('general');">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="section" value="general">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">@translate('name') <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">@translate('email') <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">@translate('phone')</label>
                                            <input type="text" class="form-control" id="phone" name="phone" value="{{ $user->phone }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="username">@translate('username')</label>
                                            <input type="text" class="form-control" id="username" name="username" value="{{ $user->username }}">
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-2"></i>@translate('save_changes')
                                </button>
                            </form>
                        </div>

                        <!-- Security Tab -->
                        <div class="tab-pane fade" id="security" role="tabpanel">
                            <form id="securityForm" onsubmit="event.preventDefault(); updateProfile('security');">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="section" value="security">

                                <div class="form-group">
                                    <label for="current_password">@translate('current_password') <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>

                                <div class="form-group">
                                    <label for="new_password">@translate('new_password') <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                </div>

                                <div class="form-group">
                                    <label for="new_password_confirmation">@translate('confirm_password') <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key mr-2"></i>@translate('change_password')
                                </button>
                            </form>
                        </div>

                        <!-- Preferences Tab -->
                        <div class="tab-pane fade" id="preferences" role="tabpanel">
                            <form id="preferencesForm" onsubmit="event.preventDefault(); updateProfile('preferences');">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="section" value="preferences">

                                <div class="form-group">
                                    <label for="language_id">@translate('language')</label>
                                    <select class="form-control" id="language_id" name="language_id">
                                        @foreach($languages as $lang)
                                            <option value="{{ $lang->id }}" {{ $user->language_id == $lang->id ? 'selected' : '' }}>
                                                {{ $lang->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="timezone">@translate('timezone')</label>
                                    <select class="form-control" id="timezone" name="timezone">
                                        @foreach($timezones as $tz)
                                            <option value="{{ $tz->timezone }}" {{ $user->timezone == $tz->timezone ? 'selected' : '' }}>
                                                {{ $tz->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-2"></i>@translate('save_preferences')
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function updateProfile(type)
{
    const form = document.getElementById(`${type}Form`);
    const formData = new FormData(form);

    Swal.fire({
        title: '@translate("updating_profile")',
        text: '@translate("please_wait")',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('{{ route("tenant.profile.update") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-HTTP-Method-Override': 'PUT'
        }
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        if (data.status === 200)
        {
            Swal.fire({
                icon: 'success',
                title: '@translate("success")',
                text: data.message || '@translate("profile_updated_successfully")',
                timer: 2000,
                showConfirmButton: false
            });
            if (data.data && data.data.reload)
            {
                setTimeout(() => location.reload(), 2000);
            }
        }
        else
        {
            Swal.fire({
                icon: 'error',
                title: '@translate("error")',
                text: data.message || '@translate("error_updating_profile")'
            });
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: '@translate("error")',
            text: '@translate("an_error_occurred")'
        });
    });
}
</script>
@endsection
