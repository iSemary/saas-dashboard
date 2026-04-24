<?php

namespace Modules\Auth\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $section = $this->get('section', 'general');

        switch ($section) {
            case 'notifications':
                return $this->getNotificationValidationRules();
            case 'appearance':
                return $this->getAppearanceValidationRules();
            case 'privacy':
                return $this->getPrivacyValidationRules();
            default:
                return $this->getGeneralValidationRules();
        }
    }

    /**
     * Get general validation rules
     */
    private function getGeneralValidationRules(): array
    {
        return [
            'notifications_email' => 'sometimes|boolean',
            'notifications_push' => 'sometimes|boolean',
            'notifications_sms' => 'sometimes|boolean',
            'theme_mode' => 'sometimes|in:light,dark',
            'date_format' => 'sometimes|in:Y-m-d,d-m-Y,m/d/Y',
            'time_format' => 'sometimes|in:12,24',
            'timezone' => 'sometimes|string|max:255',
            'language_preference' => 'sometimes|string|max:10',
            'currency_preference' => 'sometimes|string|max:3',
            'email_frequency' => 'sometimes|in:daily,weekly,monthly,never',
        ];
    }

    /**
     * Get notification validation rules
     */
    private function getNotificationValidationRules(): array
    {
        return [
            'notifications_email' => 'required|boolean',
            'notifications_push' => 'required|boolean',
            'notifications_sms' => 'required|boolean',
            'email_frequency' => 'sometimes|in:daily,weekly,monthly,never',
            'push_topics' => 'sometimes|array',
            'push_topics.*' => 'string|max:50',
            'sms_emergency_only' => 'sometimes|boolean',
        ];
    }

    /**
     * Get appearance validation rules
     */
    private function getAppearanceValidationRules(): array
    {
        return [
            'theme_mode' => 'required|in:light,dark',
            'language_preference' => 'required|string|max:10',
            'currency_preference' => 'required|string|max:3',
            'timezone' => 'required|string|max:255',
            'date_format' => 'required|in:Y-m-d,d-m-Y,m/d/Y',
            'time_format' => 'required|in:12,24',
            'sidebar_position' => 'sometimes|in:left,right',
            'compact_mode' => 'sometimes|boolean',
        ];
    }

    /**
     * Get privacy validation rules
     */
    private function getPrivacyValidationRules(): array
    {
        return [
            'data_privacy_level' => 'required|in:strict,standard,relaxed',
            'profile_visibility' => 'required|in:public,private,limited',
            'allow_data_analytics' => 'required|boolean',
            'cookie_consent' => 'sometimes|boolean',
            'marketing_emails' => 'sometimes|boolean',
            'data_retention_preference' => 'sometimes|in:minimal,standard,extended',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'notifications_email.required' => @translate('email_notification_setting_required'),
            'notifications_push.required' => @translate('push_notification_setting_required'),
            'notifications_sms.required' => @translate('sms_notification_setting_required'),
            'theme_mode.required' => @translate('theme_mode_required'),
            'theme_mode.in' => @translate('theme_mode_must_be_light_or_dark'),
            'date_format.required' => @translate('date_format_required'),
            'date_format.in' => @translate('invalid_date_format'),
            'time_format.required' => @translate('time_format_required'),
            'time_format.in' => @translate('time_format_must_be_12_or_24'),
            'timezone.required' => @translate('timezone_required'),
            'language_preference.required' => @translate('language_preference_required'),
            'currency_preference.required' => @translate('currency_preference_required'),
            'data_privacy_level.required' => @translate('data_privacy_level_required'),
            'data_privacy_level.in' => @translate('invalid_data_privacy_level'),
            'profile_visibility.required' => @translate('profile_visibility_required'),
            'profile_visibility.in' => @translate('invalid_profile_visibility'),
            'allow_data_analytics.required' => @translate('data_analytics_setting_required'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'notifications_email' => @translate('email_notifications'),
            'notifications_push' => @translate('push_notifications'),
            'notifications_sms' => @translate('sms_notifications'),
            'theme_mode' => @translate('theme_mode'),
            'date_format' => @translate('date_format'),
            'time_format' => @translate('time_format'),
            'timezone' => @translate('timezone'),
            'language_preference' => @translate('language_preference'),
            'currency_preference' => @translate('currency_preference'),
            'data_privacy_level' => @translate('data_privacy_level'),
            'profile_visibility' => @translate('profile_visibility'),
            'allow_data_analytics' => @translate('data_analytics'),
        ];
    }
}
