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

use App\Models\Department;
use Illuminate\Database\Seeder;

/**
 * Class DepartmentSeeder
 *
 * Enterprise Seeder responsible for DepartmentSeeder business logic and data operations.
 *
 * @package Database\Seeders
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Executive Leadership', 'description' => 'Agency executives, directors and partners.'],
            ['name' => 'Artist & Talent Management', 'description' => 'Celebrity relations, roster representation and schedules.'],
            ['name' => 'Brand Partnerships & Sales', 'description' => 'Client acquisition, pitches, leads and quotations.'],
            ['name' => 'Creative & Production', 'description' => 'Campaign execution, video shoots, and event staging.'],
            ['name' => 'Legal & Compliance', 'description' => 'Contracts, rights clearance, endorsements and licensing.'],
            ['name' => 'Finance & Accounting', 'description' => 'Invoicing, payouts, tax compliance, and claims processing.'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['name' => $dept['name']], [
                'description' => $dept['description'],
                'status' => 'active',
            ]);
        }
    }
}

