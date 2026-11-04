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
 * Class CelebrityRate
 *
 * Enterprise Model responsible for CelebrityRate business logic and data operations.
 *
 * @package App\Models
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class CelebrityRate extends Model
{
    protected $fillable = [
        'celebrity_id',
        'service_type',
        'description',
        'currency',
        'standard_fee',
        'effective_from',
        'effective_to',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'standard_fee' => 'decimal:2',
            'effective_from' => 'date',
            'effective_to' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function celebrity(): BelongsTo
    {
        return $this->belongsTo(Celebrity::class);
    }
}

