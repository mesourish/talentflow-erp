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
use App\Models\CelebrityRate;
use App\Models\CelebritySocialProfile;
use Illuminate\Database\Seeder;

/**
 * Class CelebrityRateSeeder
 *
 * Enterprise Seeder responsible for CelebrityRateSeeder business logic and data operations.
 *
 * @package Database\Seeders
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class CelebrityRateSeeder extends Seeder
{
    public function run(): void
    {
        $celebrities = Celebrity::all();

        $ratesData = [
            // Standard services template per talent
            ['service' => 'Brand Endorsement', 'desc' => 'Annual exclusive brand ambassadorship and commercial campaigns', 'fee_ratio' => 1.0],
            ['service' => 'Social Media Promotion', 'desc' => '1 Instagram static post + 2 Story sequences with swipe-up/link', 'fee_ratio' => 0.4],
            ['service' => 'Event Appearance', 'desc' => '3-hour red carpet appearance, ribbon cutting and media interview', 'fee_ratio' => 0.5],
            ['service' => 'TV Advertisement', 'desc' => 'Full production 2-day TV commercial shoot with buy-out rights', 'fee_ratio' => 1.2],
            ['service' => 'Photoshoot', 'desc' => '1-day print and digital catalogue photoshoot with licensed rights', 'fee_ratio' => 0.35],
        ];

        foreach ($celebrities as $celeb) {
            // Base fee varies according to talent code
            $baseFee = match ($celeb->talent_code) {
                'TLT-0001' => 800000.0, // Celebrity A
                'TLT-0002' => 750000.0,
                'TLT-0003' => 600000.0,
                'TLT-0004' => 400000.0,
                'TLT-0005' => 350000.0,
                'TLT-0006' => 450000.0,
                'TLT-0007' => 300000.0,
                'TLT-0008' => 250000.0,
                'TLT-0009' => 900000.0,
                'TLT-0010' => 550000.0,
                default => 250000.0,
            };

            foreach ($ratesData as $rate) {
                $standardFee = round($baseFee * $rate['fee_ratio'], 2);

                CelebrityRate::firstOrCreate(
                    [
                        'celebrity_id' => $celeb->id,
                        'service_type' => $rate['service'],
                    ],
                    [
                        'description' => $rate['desc'],
                        'currency' => 'INR',
                        'standard_fee' => $standardFee,
                        'effective_from' => now()->startOfYear(),
                        'effective_to' => null,
                        'is_active' => true,
                    ]
                );
            }

            // Also seed Social Profiles for each celebrity
            $handle = strtolower(str_replace([' ', '(', ')', '.', '’', '\''], '', $celeb->stage_name));
            CelebritySocialProfile::firstOrCreate(
                ['celebrity_id' => $celeb->id, 'platform' => 'Instagram'],
                [
                    'profile_url' => "https://instagram.com/{$handle}",
                    'follower_count' => rand(500000, 15000000),
                    'engagement_rate' => round(rand(25, 75) / 10, 2),
                    'last_verified_at' => now()->subDays(rand(1, 20)),
                ]
            );

            CelebritySocialProfile::firstOrCreate(
                ['celebrity_id' => $celeb->id, 'platform' => 'YouTube'],
                [
                    'profile_url' => "https://youtube.com/@{$handle}",
                    'follower_count' => rand(200000, 5000000),
                    'engagement_rate' => round(rand(30, 60) / 10, 2),
                    'last_verified_at' => now()->subDays(rand(1, 30)),
                ]
            );
        }
    }
}

