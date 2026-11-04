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
use App\Models\Lead;
use App\Models\Quotation;
use App\Models\QuotationApproval;
use App\Models\QuotationItem;
use App\Models\User;
use App\Services\QuotationCalculator;
use Illuminate\Database\Seeder;

/**
 * Class QuotationSeeder
 *
 * Enterprise Seeder responsible for QuotationSeeder business logic and data operations.
 *
 * @package Database\Seeders
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class QuotationSeeder extends Seeder
{
    public function run(): void
    {
        $calculator = new QuotationCalculator();
        $salesManager = User::where('email', 'sales@demo.test')->first() ?? User::first();
        $management = User::where('email', 'management@demo.test')->first() ?? User::first();

        $clientABC = Client::where('client_code', 'CLT-0001')->first();
        $celebA = Celebrity::where('talent_code', 'TLT-0001')->first();
        $lead1 = Lead::first();

        // 1. Blueprint Demo Scenario Quotation (Section 3.2 & 11.6)
        $quote1Data = [
            'quotation_no' => 'QT-2026-0001',
            'client_id' => $clientABC->id,
            'lead_id' => $lead1?->id,
            'version_no' => 1,
            'status' => 'converted',
            'quotation_date' => now()->subMonths(2)->startOfMonth(),
            'valid_until' => now()->subMonths(1)->endOfMonth(),
            'currency' => 'INR',
            'terms_conditions' => "1. 50% advance upon contract signing.\n2. Travel, business class flights and 5-star hospitality included.\n3. Content rights restricted to India, TV and Digital for 12 months.",
            'notes' => 'Primary Brand X Advertising Campaign with Celebrity A.',
            'created_by' => $salesManager->id,
            'approved_by' => $management->id,
            'approved_at' => now()->subMonths(2)->addDays(2),
            'converted_at' => now()->subMonths(2)->addDays(5),
        ];

        $quote1Items = [
            [
                'celebrity_id' => $celebA->id,
                'service_name' => 'Celebrity Appearance & Brand Endorsement',
                'description' => 'Brand X Campaign Endorsement and full appearance',
                'quantity' => 1,
                'unit' => 'campaign',
                'unit_price' => 800000.00,
                'discount_amount' => 0.00,
                'tax_rate' => 18.00,
                'sort_order' => 1,
            ],
            [
                'celebrity_id' => null,
                'service_name' => 'Travel and Accommodation',
                'description' => 'Business class flights and 5-star hotel logistics for entourage',
                'quantity' => 1,
                'unit' => 'trip',
                'unit_price' => 100000.00,
                'discount_amount' => 0.00,
                'tax_rate' => 18.00,
                'sort_order' => 2,
            ],
            [
                'celebrity_id' => null,
                'service_name' => 'Production Expenses',
                'description' => 'Studio equipment, styling, vanity van, and light setup',
                'quantity' => 1,
                'unit' => 'day',
                'unit_price' => 50000.00,
                'discount_amount' => 0.00,
                'tax_rate' => 18.00,
                'sort_order' => 3,
            ],
        ];

        $calc = $calculator->calculate($quote1Items);
        $q1 = Quotation::updateOrCreate(
            ['quotation_no' => $quote1Data['quotation_no']],
            array_merge($quote1Data, [
                'subtotal' => $calc['subtotal'],
                'discount_total' => $calc['discount_total'],
                'tax_total' => $calc['tax_total'],
                'grand_total' => $calc['grand_total'],
            ])
        );

        foreach ($calc['items'] as $item) {
            unset($item['line_amount'], $item['taxable_amount']);
            QuotationItem::create(array_merge($item, ['quotation_id' => $q1->id]));
        }

        QuotationApproval::create([
            'quotation_id' => $q1->id,
            'user_id' => $salesManager->id,
            'action' => 'submitted',
            'comments' => 'Initial submission for Brand X endorsement campaign.',
            'action_at' => now()->subMonths(2)->addDays(1),
        ]);

        QuotationApproval::create([
            'quotation_id' => $q1->id,
            'user_id' => $management->id,
            'action' => 'approved',
            'comments' => 'Commercial terms approved as per standard margins.',
            'action_at' => now()->subMonths(2)->addDays(2),
        ]);

        // 24 more realistic quotations
        $clients = Client::all();
        $celebrities = Celebrity::all();
        $statuses = ['converted', 'approved', 'sent', 'under_negotiation', 'draft', 'rejected'];

        for ($i = 2; $i <= 25; $i++) {
            $quoteNo = sprintf('QT-2026-%04d', $i);
            $client = $clients[($i - 1) % $clients->count()];
            $celeb = $celebrities[($i - 1) % $celebrities->count()];
            $status = $statuses[($i - 2) % count($statuses)];
            $monthsAgo = ($i % 4);

            $date = now()->subMonths($monthsAgo)->subDays($i % 25);
            $validUntil = (clone $date)->addDays(30);

            $unitPrice = 150000 + (($i * 35000) % 800000);
            $itemsRaw = [
                [
                    'celebrity_id' => $celeb->id,
                    'service_name' => 'Celebrity Endorsement & Social Promotion',
                    'description' => "Campaign engagement for {$client->company_name}",
                    'quantity' => 1,
                    'unit' => 'service',
                    'unit_price' => $unitPrice,
                    'discount_amount' => ($i % 5 === 0) ? 20000.00 : 0.00,
                    'tax_rate' => 18.00,
                    'sort_order' => 1,
                ],
                [
                    'celebrity_id' => null,
                    'service_name' => 'Production & Logistics',
                    'description' => 'On-ground execution and coordination fees',
                    'quantity' => 1,
                    'unit' => 'package',
                    'unit_price' => 50000.00,
                    'discount_amount' => 0.00,
                    'tax_rate' => 18.00,
                    'sort_order' => 2,
                ],
            ];

            $computed = $calculator->calculate($itemsRaw);

            $quotation = Quotation::updateOrCreate(
                ['quotation_no' => $quoteNo],
                [
                    'client_id' => $client->id,
                    'lead_id' => null,
                    'version_no' => 1,
                    'status' => $status,
                    'quotation_date' => $date,
                    'valid_until' => $validUntil,
                    'currency' => 'INR',
                    'subtotal' => $computed['subtotal'],
                    'discount_total' => $computed['discount_total'],
                    'tax_total' => $computed['tax_total'],
                    'grand_total' => $computed['grand_total'],
                    'terms_conditions' => "Standard TalentFlow ERP Commercial Terms. All payments net 30 days.",
                    'notes' => "Pitch quotation for {$client->company_name}.",
                    'created_by' => $salesManager->id,
                    'approved_by' => in_array($status, ['approved', 'converted']) ? $management->id : null,
                    'approved_at' => in_array($status, ['approved', 'converted']) ? (clone $date)->addDays(2) : null,
                    'converted_at' => ($status === 'converted') ? (clone $date)->addDays(4) : null,
                ]
            );

            foreach ($computed['items'] as $item) {
                unset($item['line_amount'], $item['taxable_amount']);
                QuotationItem::create(array_merge($item, ['quotation_id' => $quotation->id]));
            }

            if (in_array($status, ['sent', 'under_negotiation', 'approved', 'converted', 'rejected'])) {
                QuotationApproval::create([
                    'quotation_id' => $quotation->id,
                    'user_id' => $salesManager->id,
                    'action' => 'submitted',
                    'comments' => 'Submitted for review.',
                    'action_at' => (clone $date)->addDay(),
                ]);
            }

            if (in_array($status, ['approved', 'converted'])) {
                QuotationApproval::create([
                    'quotation_id' => $quotation->id,
                    'user_id' => $management->id,
                    'action' => 'approved',
                    'comments' => 'Commercials verified and approved.',
                    'action_at' => (clone $date)->addDays(2),
                ]);
            } elseif ($status === 'rejected') {
                QuotationApproval::create([
                    'quotation_id' => $quotation->id,
                    'user_id' => $management->id,
                    'action' => 'rejected',
                    'comments' => 'Client requested higher discount than allowable margin.',
                    'action_at' => (clone $date)->addDays(2),
                ]);
            }
        }
    }
}
