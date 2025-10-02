<?php

namespace Modules\Payment\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Crypt;

class PaymentMethodConfiguration extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $connection = 'landlord';

    public $singleTitle = "payment method configuration";
    public $pluralTitle = "payment method configurations";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_method_id',
        'environment',
        'config_key',
        'config_value',
        'is_secret',
        'config_type',
        'description',
        'is_required',
        'validation_rules',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_secret' => 'boolean',
        'is_required' => 'boolean',
    ];

    /**
     * Get the payment method.
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Scope to filter active configurations.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter by environment.
     */
    public function scopeForEnvironment($query, $environment)
    {
        return $query->where('environment', $environment);
    }

    /**
     * Scope to filter secret configurations.
     */
    public function scopeSecrets($query)
    {
        return $query->where('is_secret', true);
    }

    /**
     * Scope to filter required configurations.
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Get the decrypted configuration value.
     */
    public function getDecryptedValueAttribute()
    {
        if (!$this->is_secret || !$this->config_value) {
            return $this->config_value;
        }

        try {
            return Crypt::decryptString($this->config_value);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Set the configuration value (encrypt if secret).
     */
    public function setConfigValueAttribute($value)
    {
        if ($this->is_secret && $value) {
            $this->attributes['config_value'] = Crypt::encryptString($value);
        } else {
            $this->attributes['config_value'] = $value;
        }
    }

    /**
     * Get the typed configuration value.
     */
    public function getTypedValue()
    {
        $value = $this->getDecryptedValueAttribute();

        if ($value === null) {
            return null;
        }

        switch ($this->config_type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'number':
                return is_numeric($value) ? (float) $value : null;
            case 'json':
                return json_decode($value, true);
            case 'url':
                return filter_var($value, FILTER_VALIDATE_URL) ? $value : null;
            case 'email':
                return filter_var($value, FILTER_VALIDATE_EMAIL) ? $value : null;
            default:
                return $value;
        }
    }

    /**
     * Validate the configuration value.
     */
    public function validateValue($value)
    {
        if ($this->is_required && empty($value)) {
            return ['error' => 'This configuration is required'];
        }

        // Type validation
        switch ($this->config_type) {
            case 'boolean':
                if (!is_bool($value) && !in_array(strtolower($value), ['true', 'false', '1', '0'])) {
                    return ['error' => 'Value must be a boolean'];
                }
                break;
            case 'number':
                if (!is_numeric($value)) {
                    return ['error' => 'Value must be a number'];
                }
                break;
            case 'json':
                if (!is_array($value) && json_decode($value) === null) {
                    return ['error' => 'Value must be valid JSON'];
                }
                break;
            case 'url':
                if (!filter_var($value, FILTER_VALIDATE_URL)) {
                    return ['error' => 'Value must be a valid URL'];
                }
                break;
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return ['error' => 'Value must be a valid email address'];
                }
                break;
        }

        // Custom validation rules
        if ($this->validation_rules) {
            $rules = explode('|', $this->validation_rules);
            foreach ($rules as $rule) {
                $validation = $this->applyValidationRule($rule, $value);
                if (!$validation['valid']) {
                    return ['error' => $validation['message']];
                }
            }
        }

        return ['valid' => true];
    }

    /**
     * Apply a single validation rule.
     */
    protected function applyValidationRule($rule, $value)
    {
        if (strpos($rule, ':') !== false) {
            [$ruleName, $ruleValue] = explode(':', $rule, 2);
        } else {
            $ruleName = $rule;
            $ruleValue = null;
        }

        switch ($ruleName) {
            case 'min':
                if (strlen($value) < (int) $ruleValue) {
                    return ['valid' => false, 'message' => "Minimum length is {$ruleValue} characters"];
                }
                break;
            case 'max':
                if (strlen($value) > (int) $ruleValue) {
                    return ['valid' => false, 'message' => "Maximum length is {$ruleValue} characters"];
                }
                break;
            case 'regex':
                if (!preg_match($ruleValue, $value)) {
                    return ['valid' => false, 'message' => "Value does not match required pattern"];
                }
                break;
            case 'in':
                $allowedValues = explode(',', $ruleValue);
                if (!in_array($value, $allowedValues)) {
                    return ['valid' => false, 'message' => "Value must be one of: " . implode(', ', $allowedValues)];
                }
                break;
        }

        return ['valid' => true];
    }

    /**
     * Get masked value for display (for secret configurations).
     */
    public function getMaskedValue()
    {
        if (!$this->is_secret || !$this->config_value) {
            return $this->config_value;
        }

        $value = $this->getDecryptedValueAttribute();
        if (!$value) {
            return null;
        }

        // Show first 4 and last 4 characters for API keys
        if (strlen($value) > 8) {
            return substr($value, 0, 4) . str_repeat('*', strlen($value) - 8) . substr($value, -4);
        }

        return str_repeat('*', strlen($value));
    }

    /**
     * Check if configuration is properly set up.
     */
    public function isConfigured()
    {
        if ($this->is_required && empty($this->config_value)) {
            return false;
        }

        $validation = $this->validateValue($this->getDecryptedValueAttribute());
        return !isset($validation['error']);
    }
}
