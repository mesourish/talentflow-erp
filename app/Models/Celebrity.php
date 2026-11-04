<?php

/**
 * TalentFlow ERP - Celebrity & Talent Management System
 *
 * @package    App\Models
 * @author     Sourish Mondal
 * @version    1.0.0
 * @since      Laravel 12.x / PHP 8.2+
 * @copyright  2026 TalentFlow ERP. All rights reserved.
 * @license    Proprietary / Enterprise Commercial
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Celebrity
 *
 * Enterprise Model responsible for Celebrity business logic and data operations.
 *
 * @package App\Models
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class Celebrity extends Model
{
    protected $fillable = [
        'talent_code',
        'category_id',
        'stage_name',
        'legal_name',
        'biography',
        'base_city',
        'email',
        'phone',
        'manager_name',
        'manager_contact',
        'profile_image',
        'availability_status',
        'profile_status',
        'notes',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(TalentCategory::class, 'category_id');
    }

    public function rates(): HasMany
    {
        return $this->hasMany(CelebrityRate::class);
    }

    public function activeRates(): HasMany
    {
        return $this->hasMany(CelebrityRate::class)->where('is_active', true);
    }

    public function socialProfiles(): HasMany
    {
        return $this->hasMany(CelebritySocialProfile::class);
    }

    public function availability(): HasMany
    {
        return $this->hasMany(CelebrityAvailability::class);
    }

    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'booking_celebrities')
            ->withPivot(['id', 'assignment_role', 'agreed_fee', 'arrival_at'])
            ->withTimestamps();
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(CelebrityPayout::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
}

