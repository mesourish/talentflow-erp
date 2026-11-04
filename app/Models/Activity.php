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

/**
 * Class Activity
 *
 * Enterprise Model responsible for Activity business logic and data operations.
 *
 * @package App\Models
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class Activity extends Model
{
    protected $fillable = [
        'client_id',
        'lead_id',
        'celebrity_id',
        'project_id',
        'user_id',
        'activity_type',
        'subject',
        'description',
        'activity_at',
        'follow_up_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'activity_at' => 'datetime',
            'follow_up_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function celebrity(): BelongsTo
    {
        return $this->belongsTo(Celebrity::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

