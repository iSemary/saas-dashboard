@extends('layouts.tenant.app')

@section('title', translate('settings'))

@section('styles')
<style>
.settings-card {
    box-shadow: 0 0 20px rgba(0,0,0,0.08);
    border-radius: 10px;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card settings-card">
                <div class="card-header">
                    <h3 class="card-title">@translate('settings')</h3>
                </div>
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="notifications-tab" data-toggle="tab" href="#notifications">
                                <i class="fas fa-bell mr-2"></i>@translate('notifications')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="appearance-tab" data-toggle="tab" href="#appearance">
                                <i class="fas fa-palette mr-2"></i>@translate('appearance')
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="privacy-tab" data-toggle="tab" href="#privacy">
                                <i class="fas fa-user-secret mr-2"></i>@translate('privacy')
                            </a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content mt-3">
                        <!-- Notifications Tab -->
                        <div class="tab-pane fade show active" id="notifications">
                            <form id="notificationsForm" onsubmit="event.preventDefault(); updateSettings('notifications');">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="section" value="notifications">

                                <div class="custom-control custom-switch mb-3">
                                    <input type="checkbox" class="custom-control-input" id="notifications_email" name="notifications_email" value="1" {{ ($settings['notifications_email'] ?? true) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="notifications_email">@translate('email_notifications')</label>
                                </div>

                                <div class="custom-control custom-switch mb-3">
                                    <input type="checkbox" class="custom-control-input" id="notifications_push" name="notifications_push" value="1" {{ ($settings['notifications_push'] ?? true) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="notifications_push">@translate('push_notifications')</label>
                                </div>

                                <div class="custom-control custom-switch mb-3">
                                    <input type="checkbox" class="custom-control-input" id="notifications_sms" name="notifications_sms" value="1" {{ ($settings['notifications_sms'] ?? false) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="notifications_sms">@translate('sms_notifications')</label>
                                </div>

                                <button type="submit" class="btn btn-primary mt-3">
                                    <i class="fas fa-save mr-2"></i>@translate('save_settings')
                                </button>
                            </form>
                        </div>

                        <!-- Appearance Tab -->
                        <div class="tab-pane fade" id="appearance">
                            <form id="appearanceForm" onsubmit="event.preventDefault(); updateSettings('appearance');">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="section" value="appearance">

                                <div class="form-group">
                                    <label>@translate('theme_mode')</label>
                                    <div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" class="custom-control-input" id="theme_light" name="theme_mode" value="light" {{ ($settings['theme_mode'] ?? 'light') == 'light' ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="theme_light">@translate('light')</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" class="custom-control-input" id="theme_dark" name="theme_mode" value="dark" {{ ($settings['theme_mode'] ?? 'light') == 'dark' ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="theme_dark">@translate('dark')</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="language_id">@translate('language')</label>
                                    <select class="form-control" id="language_id" name="language_id">
                                        @foreach($languages as $lang)
                                            <option value="{{ $lang->id }}" {{ ($settings['language_id'] ?? null) == $lang->id ? 'selected' : '' }}>
                                                {{ $lang->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary mt-3">
                                    <i class="fas fa-save mr-2"></i>@translate('save_settings')
                                </button>
                            </form>
                        </div>

                        <!-- Privacy Tab -->
                        <div class="tab-pane fade" id="privacy">
                            <form id="privacyForm" onsubmit="event.preventDefault(); updateSettings('privacy');">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="section" value="privacy">

                                <div class="custom-control custom-switch mb-3">
                                    <input type="checkbox" class="custom-control-input" id="allow_data_analytics" name="allow_data_analytics" value="1" {{ ($settings['allow_data_analytics'] ?? false) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="allow_data_analytics">@translate('allow_data_analytics')</label>
                                </div>

                                <button type="submit" class="btn btn-primary mt-3">
                                    <i class="fas fa-save mr-2"></i>@translate('save_settings')
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
function updateSettings(type)
{
    const form = document.getElementById(`${type}Form`);
    const formData = new FormData(form);

    Swal.fire({
        title: '@translate("updating_settings")',
        text: '@translate("please_wait")',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('{{ route("tenant.settings.update") }}', {
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
                text: data.message || '@translate("settings_updated_successfully")',
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
                text: data.message || '@translate("error_updating_settings")'
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
