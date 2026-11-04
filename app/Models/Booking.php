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

/**
 * Class Booking
 *
 * Enterprise Model responsible for Booking business logic and data operations.
 *
 * @package App\Models
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class Booking extends Model
{
    protected $fillable = [
        'booking_code',
        'project_id',
        'title',
        'booking_type',
        'venue_name',
        'venue_address',
        'timezone',
        'starts_at',
        'ends_at',
        'status',
        'coordinator_id',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function celebrities(): BelongsToMany
    {
        return $this->belongsToMany(Celebrity::class, 'booking_celebrities')
            ->withPivot(['id', 'assignment_role', 'agreed_fee', 'arrival_at'])
            ->withTimestamps();
    }
}

