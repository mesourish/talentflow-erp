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
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Department
 *
 * Enterprise Model responsible for Department business logic and data operations.
 *
 * @package App\Models
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class Department extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}

