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

use Illuminate\Database\Seeder;

/**
 * Class DatabaseSeeder
 *
 * Enterprise Seeder responsible for DatabaseSeeder business logic and data operations.
 *
 * @package Database\Seeders
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            DepartmentSeeder::class,
            DemoUserSeeder::class,
            TalentCategorySeeder::class,
            ClientSeeder::class,
            ClientContactSeeder::class,
            CelebritySeeder::class,
            CelebrityRateSeeder::class,
            LeadSeeder::class,
            CommissionRuleSeeder::class,
            QuotationSeeder::class,
            ProjectSeeder::class,
            BookingSeeder::class,
            ContractSeeder::class,
            DocumentSeeder::class,
            ClaimSeeder::class,
            InvoiceSeeder::class,
            PaymentSeeder::class,
            ActivityLogSeeder::class,
        ]);
    }
}
