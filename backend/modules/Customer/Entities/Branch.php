<?php

namespace Modules\Customer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Modules\Localization\Traits\Translatable;
use Modules\Customer\Entities\Brand;
use Modules\Auth\Entities\User;
use OwenIt\Auditing\Contracts\Auditable;

class Branch extends Model implements Auditable
{
    use HasFactory, SoftDeletes, Translatable, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
        'code',
        'description',
        'working_hours',
        'working_days',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'phone',
        'email',
        'website',
        'manager_name',
        'manager_email',
        'manager_phone',
        'latitude',
        'longitude',
        'status',
        'brand_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'working_hours' => 'array',
        'working_days' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $translatable = [
        'name',
        'description',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($branch) {
            if (empty($branch->code)) {
                $branch->code = static::generateUniqueCode($branch->name, $branch->brand_id);
            }
            if (Auth::check()) {
                $branch->created_by = Auth::id();
            }
        });

        static::updating(function ($branch) {
            if ($branch->isDirty('name') && empty($branch->code)) {
                $branch->code = static::generateUniqueCode($branch->name, $branch->brand_id);
            }
            if (Auth::check()) {
                $branch->updated_by = Auth::id();
            }
        });
    }

    /**
     * Get the brand that owns the branch.
     * This is a cross-database relationship since brands are landlord-only.
     */
    public function brand()
    {
        // Since brands are landlord-only, we need to use a custom approach
        // We'll store the brand_id and fetch the brand name when needed
        return $this->belongsTo(Brand::class)->withoutGlobalScopes();
    }

    /**
     * Get the brand name for display purposes.
     * This method handles the cross-database relationship safely.
     */
    public function getBrandNameAttribute()
    {
        if (!$this->brand_id) {
            return 'N/A';
        }

        try {
            // Try to get the brand from landlord database
            $brand = Brand::on('landlord')->find($this->brand_id);
            return $brand ? $brand->name : 'N/A';
        } catch (\Exception $e) {
            // If there's any issue accessing the landlord database, return N/A
            return 'N/A';
        }
    }

    /**
     * Get the user who created the branch.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the branch.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for filtering by brand.
     */
    public function scopeForBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    /**
     * Scope for filtering by status.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for searching branches.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('code', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%')
              ->orWhere('address', 'like', '%' . $search . '%')
              ->orWhere('city', 'like', '%' . $search . '%')
              ->orWhere('manager_name', 'like', '%' . $search . '%');
        });
    }

    /**
     * Generate unique code for branch
     */
    private static function generateUniqueCode($name, $brandId)
    {
        $baseCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 3));
        $code = $baseCode;
        $counter = 1;

        while (static::where('code', $code)->where('brand_id', $brandId)->exists()) {
            $code = $baseCode . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $counter++;
        }

        return $code;
    }

    /**
     * Get the full address
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get coordinates as array
     */
    public function getCoordinatesAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return [
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude
            ];
        }
        return null;
    }

    /**
     * Check if branch has location data
     */
    public function hasLocation()
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    /**
     * Get formatted working hours
     */
    public function getFormattedWorkingHoursAttribute()
    {
        if (!$this->working_hours) {
            return 'Not specified';
        }

        $hours = $this->working_hours;
        $formatted = [];

        foreach ($hours as $day => $time) {
            if ($time && isset($time['open']) && isset($time['close'])) {
                $formatted[] = ucfirst($day) . ': ' . $time['open'] . ' - ' . $time['close'];
            } else {
                $formatted[] = ucfirst($day) . ': Closed';
            }
        }

        return implode('<br>', $formatted);
    }

    /**
     * Get formatted working days
     */
    public function getFormattedWorkingDaysAttribute()
    {
        if (!$this->working_days) {
            return 'Not specified';
        }

        $days = $this->working_days;
        $dayNames = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday'
        ];

        $workingDays = [];
        foreach ($days as $day => $isWorking) {
            if ($isWorking) {
                $workingDays[] = $dayNames[$day] ?? ucfirst($day);
            }
        }

        return empty($workingDays) ? 'No working days specified' : implode(', ', $workingDays);
    }

    /**
     * Check if branch is open on a specific day
     */
    public function isOpenOnDay($day)
    {
        if (!$this->working_days) {
            return false;
        }

        return isset($this->working_days[$day]) && $this->working_days[$day];
    }

    /**
     * Get working hours for a specific day
     */
    public function getWorkingHoursForDay($day)
    {
        if (!$this->working_hours || !isset($this->working_hours[$day])) {
            return null;
        }

        return $this->working_hours[$day];
    }
}
