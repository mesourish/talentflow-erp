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

use App\Models\TalentCategory;
use Illuminate\Database\Seeder;

/**
 * Class TalentCategorySeeder
 *
 * Enterprise Seeder responsible for TalentCategorySeeder business logic and data operations.
 *
 * @package Database\Seeders
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class TalentCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Actor', 'description' => 'Film, OTT, theatre and commercial screen actors.'],
            ['name' => 'Singer & Musician', 'description' => 'Playback singers, touring bands and instrumentalists.'],
            ['name' => 'Digital Creator & Influencer', 'description' => 'Lifestyle, beauty, gaming, tech and comedy influencers.'],
            ['name' => 'Fashion Model', 'description' => 'Runway, editorial, and commercial campaign models.'],
            ['name' => 'Sports Personality', 'description' => 'Athletes, cricketers, olympians and sporting icons.'],
            ['name' => 'Celebrity Chef & Host', 'description' => 'Culinary experts, emcees, and televised event anchors.'],
        ];

        foreach ($categories as $cat) {
            TalentCategory::firstOrCreate(['name' => $cat['name']], [
                'description' => $cat['description'],
                'status' => 'active',
            ]);
        }
    }
}

