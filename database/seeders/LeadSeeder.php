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

use App\Models\Client;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Class LeadSeeder
 *
 * Enterprise Seeder responsible for LeadSeeder business logic and data operations.
 *
 * @package Database\Seeders
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class LeadSeeder extends Seeder
{
    public function run(): void
    {
        $salesManager = User::where('email', 'sales@demo.test')->first() ?? User::first();
        $crmExec = User::where('email', 'crm@demo.test')->first() ?? User::first();
        $clients = Client::all();

        $leadsData = [
            ['title' => 'Brand X Summer 2026 Festive Endorsement', 'source' => 'Referral', 'stage' => 'won', 'val' => 1500000, 'prob' => 100, 'client_idx' => 0],
            ['title' => 'Zenith Skincare Multi-Talent Campaign', 'source' => 'Inbound Pitch', 'stage' => 'negotiation', 'val' => 2500000, 'prob' => 80, 'client_idx' => 1],
            ['title' => 'Nexus Media Monsoon Beverage TVC', 'source' => 'Agency Partner', 'stage' => 'proposal', 'val' => 1800000, 'prob' => 60, 'client_idx' => 2],
            ['title' => 'Aura Cosmetics Luxury Lipstick Face', 'source' => 'Referral', 'stage' => 'won', 'val' => 1200000, 'prob' => 100, 'client_idx' => 3],
            ['title' => 'Titanium Smartwatch Tech Unboxing Series', 'source' => 'Web Inquiry', 'stage' => 'won', 'val' => 850000, 'prob' => 100, 'client_idx' => 4],
            ['title' => 'Saffron Luxury Resort Gala Evening Anchor', 'source' => 'Event', 'stage' => 'won', 'val' => 600000, 'prob' => 100, 'client_idx' => 5],
            ['title' => 'Oasis Energy Drink Youth Tour 2026', 'source' => 'Direct Outreach', 'stage' => 'negotiation', 'val' => 3200000, 'prob' => 75, 'client_idx' => 6],
            ['title' => 'Pulse Fitness Annual Brand Face Contract', 'source' => 'Referral', 'stage' => 'won', 'val' => 1400000, 'prob' => 100, 'client_idx' => 7],
            ['title' => 'Silver Screen OTT Drama Star Cameo', 'source' => 'Industry Network', 'stage' => 'won', 'val' => 2000000, 'prob' => 100, 'client_idx' => 8],
            ['title' => 'Velocity Motors Electric SUV Launch Event', 'source' => 'Agency Partner', 'stage' => 'contacted', 'val' => 2200000, 'prob' => 50, 'client_idx' => 9],
            ['title' => 'Verve FinTech Financial Literacy TV Ad', 'source' => 'Web Inquiry', 'stage' => 'won', 'val' => 2800000, 'prob' => 100, 'client_idx' => 10],
            ['title' => 'Kalyan Jewel Diwali High Jewellery Shoot', 'source' => 'Referral', 'stage' => 'won', 'val' => 1600000, 'prob' => 100, 'client_idx' => 11],
            ['title' => 'Global Star 5-City Arena Tour Headline', 'source' => 'Event Promoter', 'stage' => 'won', 'val' => 5000000, 'prob' => 100, 'client_idx' => 12],
            ['title' => 'Prime Living Signature Tower Launch Face', 'source' => 'Direct Outreach', 'stage' => 'proposal', 'val' => 1900000, 'prob' => 65, 'client_idx' => 13],
            ['title' => 'EcoPure Organic Wellness Ambassadorship', 'source' => 'Web Inquiry', 'stage' => 'contacted', 'val' => 950000, 'prob' => 40, 'client_idx' => 14],
            ['title' => 'National Retail Fashion Week Showstopper', 'source' => 'Agency Partner', 'stage' => 'negotiation', 'val' => 1100000, 'prob' => 70, 'client_idx' => 0],
            ['title' => 'Zenith Hygiene Line National Campaign', 'source' => 'Referral', 'stage' => 'new', 'val' => 1750000, 'prob' => 30, 'client_idx' => 1],
            ['title' => 'Nexus PR Tech Conclave Moderator', 'source' => 'Direct Outreach', 'stage' => 'new', 'val' => 450000, 'prob' => 20, 'client_idx' => 2],
            ['title' => 'Aura Cosmetics Summer Skincare Reels', 'source' => 'Inbound Pitch', 'stage' => 'proposal', 'val' => 700000, 'prob' => 55, 'client_idx' => 3],
            ['title' => 'Titanium Smart Ring Influencer Seeding', 'source' => 'Web Inquiry', 'stage' => 'contacted', 'val' => 500000, 'prob' => 45, 'client_idx' => 4],
        ];

        foreach ($leadsData as $i => $item) {
            $code = sprintf('LED-%04d', $i + 1);
            $client = $clients[$item['client_idx']] ?? null;

            Lead::updateOrCreate(
                ['lead_code' => $code],
                [
                    'client_id' => $client?->id,
                    'lead_title' => $item['title'],
                    'lead_source' => $item['source'],
                    'stage' => $item['stage'],
                    'estimated_value' => $item['val'],
                    'probability' => $item['prob'],
                    'expected_close_date' => now()->addDays(rand(10, 60)),
                    'assigned_to' => ($i % 2 === 0) ? $salesManager->id : $crmExec->id,
                    'notes' => 'Qualified through sales pipeline review.',
                ]
            );
        }
    }
}

