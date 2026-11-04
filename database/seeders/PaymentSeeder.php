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

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Class PaymentSeeder
 *
 * Enterprise Seeder responsible for PaymentSeeder business logic and data operations.
 *
 * @package Database\Seeders
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $financeManager = User::where('email', 'finance@demo.test')->first() ?? User::first();

        // 1. Primary Demo Partial Payment for Invoice 1
        $inv1 = Invoice::where('invoice_no', 'INV-2026-0001')->first();
        if ($inv1) {
            Payment::updateOrCreate(
                ['invoice_id' => $inv1->id, 'payment_reference' => 'TXN-HDFC-889911'],
                [
                    'payment_date' => now()->subDays(25),
                    'amount' => 1000000.00,
                    'payment_method' => 'bank_transfer',
                    'notes' => 'First milestone advance payment received from ABC Fashion Pvt. Ltd.',
                    'received_by' => $financeManager->id,
                ]
            );
        }

        // Additional payments for other invoices
        $paidInvoices = Invoice::where('id', '!=', $inv1?->id)
            ->whereIn('status', ['paid', 'partially_paid'])
            ->get();

        foreach ($paidInvoices as $index => $invoice) {
            $ref = sprintf('TXN-ICICI-%06d', 100000 + $index);
            $amount = $invoice->amount_paid;

            if ($amount > 0) {
                Payment::updateOrCreate(
                    ['invoice_id' => $invoice->id, 'payment_reference' => $ref],
                    [
                        'payment_date' => $invoice->invoice_date->addDays(rand(3, 15)),
                        'amount' => $amount,
                        'payment_method' => ($index % 2 === 0) ? 'bank_transfer' : 'cheque',
                        'notes' => 'Payment reconciled and cleared in bank account.',
                        'received_by' => $financeManager->id,
                    ]
                );
            }
        }
    }
}

