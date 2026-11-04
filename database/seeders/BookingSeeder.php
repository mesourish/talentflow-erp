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

use App\Models\Booking;
use App\Models\BookingCelebrity;
use App\Models\Celebrity;
use App\Models\CelebrityAvailability;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Class BookingSeeder
 *
 * Enterprise Seeder responsible for BookingSeeder business logic and data operations.
 *
 * @package Database\Seeders
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $bookingCoord = User::where('email', 'booking@demo.test')->first() ?? User::first();
        $p1 = Project::where('project_code', 'PRJ-2026-0001')->first() ?? Project::first();
        $celebA = Celebrity::where('talent_code', 'TLT-0001')->first() ?? Celebrity::first();

        // 1. Primary Demo Booking: Brand X Photoshoot (Section 3.2 Page 8)
        $b1 = Booking::updateOrCreate(
            ['booking_code' => 'BKG-2026-0001'],
            [
                'project_id' => $p1->id,
                'title' => 'Brand X Campaign - Primary Studio Photoshoot',
                'booking_type' => 'shoot',
                'venue_name' => 'Mehboob Studios (Floor 2)',
                'venue_address' => 'Hill Road, Bandra West, Mumbai 400050',
                'timezone' => 'Asia/Kolkata',
                'starts_at' => now()->addDays(3)->setTime(9, 0),
                'ends_at' => now()->addDays(3)->setTime(18, 0),
                'status' => 'confirmed',
                'coordinator_id' => $bookingCoord->id,
                'notes' => 'Call time 08:30 AM for hair, makeup and styling. Primary TVC and print stills.',
                'created_by' => $bookingCoord->id,
            ]
        );

        BookingCelebrity::updateOrCreate(
            ['booking_id' => $b1->id, 'celebrity_id' => $celebA->id],
            [
                'assignment_role' => 'Principal Brand Ambassador',
                'agreed_fee' => 800000.00,
                'arrival_at' => now()->addDays(3)->setTime(8, 30),
            ]
        );

        // 2. Primary Demo Booking: Brand X Press Conference & Launch Gala
        $b2 = Booking::updateOrCreate(
            ['booking_code' => 'BKG-2026-0002'],
            [
                'project_id' => $p1->id,
                'title' => 'Brand X Nationwide Launch Gala & Press Conference',
                'booking_type' => 'event',
                'venue_name' => 'The St. Regis Hotel (Grand Ballroom)',
                'venue_address' => 'Senapati Bapat Marg, Lower Parel, Mumbai 400013',
                'timezone' => 'Asia/Kolkata',
                'starts_at' => now()->addDays(12)->setTime(18, 0),
                'ends_at' => now()->addDays(12)->setTime(23, 0),
                'status' => 'tentative',
                'coordinator_id' => $bookingCoord->id,
                'notes' => 'Evening keynote, media interviews and VIP meet & greet.',
                'created_by' => $bookingCoord->id,
            ]
        );

        BookingCelebrity::updateOrCreate(
            ['booking_id' => $b2->id, 'celebrity_id' => $celebA->id],
            [
                'assignment_role' => 'Guest of Honour',
                'agreed_fee' => 400000.00,
                'arrival_at' => now()->addDays(12)->setTime(17, 30),
            ]
        );

        // Also add Celebrity Availability Block for demo
        CelebrityAvailability::firstOrCreate(
            [
                'celebrity_id' => $celebA->id,
                'starts_at' => now()->addDays(20)->setTime(0, 0),
                'ends_at' => now()->addDays(24)->setTime(23, 59),
            ],
            [
                'availability_type' => 'blocked',
                'reason' => 'Annual family vacation / Personal leave',
                'created_by' => $bookingCoord->id,
            ]
        );

        // 28 more bookings across various dates, projects, and celebrities
        $projects = Project::all();
        $celebrities = Celebrity::all();
        $venues = [
            ['name' => 'Yash Raj Films Studio', 'addr' => 'Andheri West, Mumbai'],
            ['name' => 'JW Marriott Juhu', 'addr' => 'Juhu Tara Rd, Mumbai'],
            ['name' => 'Grand Hyatt Mumbai Hotel & Residences', 'addr' => 'Bandra Kurla Complex, Mumbai'],
            ['name' => 'Taj Palace Hotel', 'addr' => 'Sardar Patel Marg, New Delhi'],
            ['name' => 'ITC Gardenia', 'addr' => 'Residency Road, Bengaluru'],
            ['name' => 'Film City Studio Floor 7', 'addr' => 'Goregaon East, Mumbai'],
        ];

        $types = ['event', 'shoot', 'appearance', 'campaign'];
        $statuses = ['confirmed', 'tentative', 'completed', 'confirmed', 'tentative'];

        for ($i = 3; $i <= 30; $i++) {
            $code = sprintf('BKG-2026-%04d', $i);
            $project = $projects[$i % $projects->count()];
            $venue = $venues[$i % count($venues)];
            $type = $types[$i % count($types)];
            $status = $statuses[$i % count($statuses)];

            // Spread across -60 days to +45 days
            $dayOffset = ($i * 4) - 50;
            $start = now()->addDays($dayOffset)->setTime(10, 0);
            $end = (clone $start)->addHours(rand(4, 8));

            $booking = Booking::updateOrCreate(
                ['booking_code' => $code],
                [
                    'project_id' => $project->id,
                    'title' => "{$project->project_name} - Session #{$i}",
                    'booking_type' => $type,
                    'venue_name' => $venue['name'],
                    'venue_address' => $venue['addr'],
                    'timezone' => 'Asia/Kolkata',
                    'starts_at' => $start,
                    'ends_at' => $end,
                    'status' => $status,
                    'coordinator_id' => $bookingCoord->id,
                    'notes' => 'Scheduled talent engagement with confirmed security riders.',
                    'created_by' => $bookingCoord->id,
                ]
            );

            // Assign 1 or 2 celebrities
            $celeb1 = $celebrities[$i % $celebrities->count()];
            BookingCelebrity::updateOrCreate(
                ['booking_id' => $booking->id, 'celebrity_id' => $celeb1->id],
                [
                    'assignment_role' => 'Lead Artist',
                    'agreed_fee' => rand(150000, 750000),
                    'arrival_at' => (clone $start)->subMinutes(30),
                ]
            );

            if ($i % 3 === 0) {
                $celeb2 = $celebrities[($i + 5) % $celebrities->count()];
                if ($celeb2->id !== $celeb1->id) {
                    BookingCelebrity::updateOrCreate(
                        ['booking_id' => $booking->id, 'celebrity_id' => $celeb2->id],
                        [
                            'assignment_role' => 'Supporting / Co-Star',
                            'agreed_fee' => rand(100000, 300000),
                            'arrival_at' => (clone $start)->subMinutes(30),
                        ]
                    );
                }
            }
        }
    }
}

