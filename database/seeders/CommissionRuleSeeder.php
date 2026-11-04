<?php

/**
 * TalentFlow ERP - Celebrity & Talent Management System
 *
 * @package    Database\Seeders
 * @author     Sourish Mondal
 * @version    1.0.0
 * @since      Laravel 12.x / PHP 8.2+
 * @copyright  2026 TalentFlow ERP. All rights reserved.
 * @license    Proprietary / Enterprise Commercial
 */

namespace Database\Seeders;

use App\Models\Celebrity;
use App\Models\Client;
use App\Models\CommissionRule;
use Illuminate\Database\Seeder;

/**
 * Class CommissionRuleSeeder
 *
 * Enterprise Seeder responsible for CommissionRuleSeeder business logic and data operations.
 *
 * @package Database\Seeders
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class CommissionRuleSeeder extends Seeder
{
    public function run(): void
    {
        $celebA = Celebrity::where('talent_code', 'TLT-0001')->first();
        $clientA = Client::where('client_code', 'CLT-0001')->first();

        CommissionRule::firstOrCreate(
            ['name' => 'Default Agency Standard (20%)'],
            [
                'applies_to' => 'default',
                'celebrity_id' => null,
                'client_id' => null,
                'commission_type' => 'percentage',
                'commission_value' => 20.00,
                'effective_from' => now()->startOfYear(),
                'effective_to' => null,
                'is_active' => true,
            ]
        );

        if ($celebA) {
            CommissionRule::firstOrCreate(
                ['name' => 'Celebrity A Exclusive Representation (20%)'],
                [
                    'applies_to' => 'celebrity',
                    'celebrity_id' => $celebA->id,
                    'client_id' => null,
                    'commission_type' => 'percentage',
                    'commission_value' => 20.00,
                    'effective_from' => now()->startOfYear(),
                    'effective_to' => null,
                    'is_active' => true,
                ]
            );
        }

        if ($clientA) {
            CommissionRule::firstOrCreate(
                ['name' => 'ABC Fashion Strategic Account (18%)'],
                [
                    'applies_to' => 'client',
                    'celebrity_id' => null,
                    'client_id' => $clientA->id,
                    'commission_type' => 'percentage',
                    'commission_value' => 18.00,
                    'effective_from' => now()->startOfYear(),
                    'effective_to' => null,
                    'is_active' => true,
                ]
            );
        }
    }
}

