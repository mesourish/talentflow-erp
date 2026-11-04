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

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Class ActivityLogSeeder
 *
 * Enterprise Seeder responsible for ActivityLogSeeder business logic and data operations.
 *
 * @package Database\Seeders
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        $logs = [
            ['action' => 'user_login', 'desc' => 'User admin@demo.test logged in successfully', 'ip' => '127.0.0.1'],
            ['action' => 'client_created', 'desc' => 'Client CLT-0001 (ABC Fashion Pvt. Ltd.) registered in CRM', 'ip' => '127.0.0.1'],
            ['action' => 'talent_created', 'desc' => 'Celebrity TLT-0001 (Celebrity A / Aanya Sen) profile created', 'ip' => '127.0.0.1'],
            ['action' => 'quotation_created', 'desc' => 'Quotation QT-2026-0001 drafted for Brand X advertising campaign', 'ip' => '127.0.0.1'],
            ['action' => 'quotation_approved', 'desc' => 'Quotation QT-2026-0001 approved by Management', 'ip' => '127.0.0.1'],
            ['action' => 'quotation_converted', 'desc' => 'Quotation QT-2026-0001 converted into Project PRJ-2026-0001', 'ip' => '127.0.0.1'],
            ['action' => 'booking_confirmed', 'desc' => 'Booking BKG-2026-0001 confirmed at Mehboob Studios for Celebrity A', 'ip' => '127.0.0.1'],
            ['action' => 'contract_uploaded', 'desc' => 'Signed contract CNT-2026-0001 uploaded and encrypted in DMS', 'ip' => '127.0.0.1'],
            ['action' => 'claim_approved', 'desc' => 'Claim CLM-2026-0001 approved and posted to project expenses', 'ip' => '127.0.0.1'],
            ['action' => 'invoice_created', 'desc' => 'Invoice INV-2026-0001 generated for INR 17,70,000', 'ip' => '127.0.0.1'],
            ['action' => 'payment_recorded', 'desc' => 'Partial payment of INR 10,00,000 received for Invoice INV-2026-0001', 'ip' => '127.0.0.1'],
            ['action' => 'payout_approved', 'desc' => 'Celebrity payout of INR 8,00,000 approved for Celebrity A', 'ip' => '127.0.0.1'],
        ];

        foreach ($logs as $log) {
            ActivityLog::create([
                'user_id' => $admin?->id,
                'action' => $log['action'],
                'description' => $log['desc'],
                'ip_address' => $log['ip'],
                'created_at' => now()->subDays(rand(1, 20)),
            ]);
        }
    }
}

