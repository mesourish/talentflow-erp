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
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Class DemoUserSeeder
 *
 * Enterprise Seeder responsible for DemoUserSeeder business logic and data operations.
 *
 * @package Database\Seeders
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $execDept = Department::where('name', 'Executive Leadership')->first();
        $talentDept = Department::where('name', 'Artist & Talent Management')->first();
        $salesDept = Department::where('name', 'Brand Partnerships & Sales')->first();
        $prodDept = Department::where('name', 'Creative & Production')->first();
        $legalDept = Department::where('name', 'Legal & Compliance')->first();
        $financeDept = Department::where('name', 'Finance & Accounting')->first();

        $accounts = [
            [
                'name' => 'Vikram Singhania',
                'email' => 'admin@demo.test',
                'phone' => '+91 98200 11001',
                'role' => 'Super Admin',
                'department_id' => $execDept?->id,
            ],
            [
                'name' => 'Ananya Roy',
                'email' => 'management@demo.test',
                'phone' => '+91 98200 11002',
                'role' => 'Management',
                'department_id' => $execDept?->id,
            ],
            [
                'name' => 'Rohan Kapoor',
                'email' => 'sales@demo.test',
                'phone' => '+91 98200 11003',
                'role' => 'Sales Manager',
                'department_id' => $salesDept?->id,
            ],
            [
                'name' => 'Sneha Sharma',
                'email' => 'crm@demo.test',
                'phone' => '+91 98200 11004',
                'role' => 'CRM Executive',
                'department_id' => $salesDept?->id,
            ],
            [
                'name' => 'Kabir Mehra',
                'email' => 'project@demo.test',
                'phone' => '+91 98200 11005',
                'role' => 'Project Manager',
                'department_id' => $prodDept?->id,
            ],
            [
                'name' => 'Pooja Nair',
                'email' => 'booking@demo.test',
                'phone' => '+91 98200 11006',
                'role' => 'Booking Coordinator',
                'department_id' => $talentDept?->id,
            ],
            [
                'name' => 'Arun Joshi',
                'email' => 'finance@demo.test',
                'phone' => '+91 98200 11007',
                'role' => 'Finance Manager',
                'department_id' => $financeDept?->id,
            ],
            [
                'name' => 'Tanvi Merchant',
                'email' => 'documents@demo.test',
                'phone' => '+91 98200 11008',
                'role' => 'Document Manager',
                'department_id' => $legalDept?->id,
            ],
        ];

        foreach ($accounts as $acc) {
            $user = User::updateOrCreate(
                ['email' => $acc['email']],
                [
                    'name' => $acc['name'],
                    'password' => Hash::make('password'),
                    'phone' => $acc['phone'],
                    'department_id' => $acc['department_id'],
                    'is_active' => true,
                    'last_login_at' => now(),
                ]
            );

            $user->syncRoles([$acc['role']]);
        }
    }
}

