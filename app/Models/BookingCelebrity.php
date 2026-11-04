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

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class BookingCelebrity
 *
 * Enterprise Model responsible for BookingCelebrity business logic and data operations.
 *
 * @package App\Models
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class BookingCelebrity extends Pivot
{
    protected $table = 'booking_celebrities';

    protected $fillable = [
        'booking_id',
        'celebrity_id',
        'assignment_role',
        'agreed_fee',
        'arrival_at',
    ];

    protected function casts(): array
    {
        return [
            'agreed_fee' => 'decimal:2',
            'arrival_at' => 'datetime',
        ];
    }
}

