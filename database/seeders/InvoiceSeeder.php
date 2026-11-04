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
use App\Models\CelebrityPayout;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Class InvoiceSeeder
 *
 * Enterprise Seeder responsible for InvoiceSeeder business logic and data operations.
 *
 * @package Database\Seeders
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $clientABC = Client::where('client_code', 'CLT-0001')->first();
        $p1 = Project::where('project_code', 'PRJ-2026-0001')->first();
        $celebA = Celebrity::where('talent_code', 'TLT-0001')->first();

        // 1. Primary Demo Invoice 1 (Blueprint Section 3.2 Page 8)
        $inv1 = Invoice::updateOrCreate(
            ['invoice_no' => 'INV-2026-0001'],
            [
                'client_id' => $clientABC->id,
                'project_id' => $p1->id,
                'invoice_date' => now()->subMonths(1)->subDays(10),
                'due_date' => now()->subDays(10),
                'currency' => 'INR',
                'subtotal' => 1500000.00,
                'discount_total' => 0.00,
                'tax_total' => 270000.00, // 18% GST
                'grand_total' => 1770000.00,
                'amount_paid' => 1000000.00, // Partial payment demo!
                'status' => 'partially_paid',
                'notes' => 'Milestone Invoice 1 for Brand X Campaign. Advance received, second installment due.',
            ]
        );

        InvoiceItem::firstOrCreate(
            ['invoice_id' => $inv1->id, 'description' => 'Brand X Advertising Campaign Endorsement & Staging Phase 1'],
            [
                'quantity' => 1,
                'unit_price' => 1500000.00,
                'tax_rate' => 18.00,
                'tax_amount' => 270000.00,
                'line_total' => 1770000.00,
            ]
        );

        // Celebrity Payout for Demo Scenario (Blueprint Section 4.9 & 11.7 Page 22, 45)
        // Gross: 10,00,000; Commission 20%: 2,00,000; Net: 8,00,000
        CelebrityPayout::updateOrCreate(
            ['project_id' => $p1->id, 'celebrity_id' => $celebA->id],
            [
                'gross_amount' => 1000000.00,
                'commission_amount' => 200000.00,
                'other_deductions' => 0.00,
                'net_payable' => 800000.00,
                'status' => 'approved',
                'paid_at' => now()->subDays(3),
                'payment_reference' => 'RTGS-HDFC-998822',
            ]
        );

        // 19 more invoices across clients and projects
        $projects = Project::all();
        $statuses = ['paid', 'paid', 'partially_paid', 'issued', 'overdue'];

        for ($i = 2; $i <= 20; $i++) {
            $code = sprintf('INV-2026-%04d', $i);
            $project = $projects[$i % $projects->count()];
            $status = $statuses[($i - 2) % count($statuses)];
            $monthsAgo = ($i % 4);

            $subtotal = 300000 + (($i * 65000) % 1500000);
            $tax = round($subtotal * 0.18, 2);
            $grand = round($subtotal + $tax, 2);

            $paid = match ($status) {
                'paid' => $grand,
                'partially_paid' => round($grand * 0.5, 2),
                default => 0.00,
            };

            $invDate = now()->subMonths($monthsAgo)->subDays($i % 20);
            $dueDate = ($status === 'overdue') ? now()->subDays(15) : (clone $invDate)->addDays(30);

            $inv = Invoice::updateOrCreate(
                ['invoice_no' => $code],
                [
                    'client_id' => $project->client_id,
                    'project_id' => $project->id,
                    'invoice_date' => $invDate,
                    'due_date' => $dueDate,
                    'currency' => 'INR',
                    'subtotal' => $subtotal,
                    'discount_total' => 0.00,
                    'tax_total' => $tax,
                    'grand_total' => $grand,
                    'amount_paid' => $paid,
                    'status' => $status,
                    'notes' => "Invoice for {$project->project_name}.",
                ]
            );

            InvoiceItem::firstOrCreate(
                ['invoice_id' => $inv->id, 'description' => "Professional talent engagement fees for {$project->project_name}"],
                [
                    'quantity' => 1,
                    'unit_price' => $subtotal,
                    'tax_rate' => 18.00,
                    'tax_amount' => $tax,
                    'line_total' => $grand,
                ]
            );
        }
    }
}

