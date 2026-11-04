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
 * Class CelebritySocialProfile
 *
 * Enterprise Model responsible for CelebritySocialProfile business logic and data operations.
 *
 * @package App\Models
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class CelebritySocialProfile extends Model
{
    protected $fillable = [
        'celebrity_id',
        'platform',
        'profile_url',
        'follower_count',
        'engagement_rate',
        'last_verified_at',
    ];

    protected function casts(): array
    {
        return [
            'follower_count' => 'integer',
            'engagement_rate' => 'decimal:3',
            'last_verified_at' => 'datetime',
        ];
    }

    public function celebrity(): BelongsTo
    {
        return $this->belongsTo(Celebrity::class);
    }
}

