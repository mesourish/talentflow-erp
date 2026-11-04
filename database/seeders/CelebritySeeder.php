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
use App\Models\TalentCategory;
use Illuminate\Database\Seeder;

/**
 * Class CelebritySeeder
 *
 * Enterprise Seeder responsible for CelebritySeeder business logic and data operations.
 *
 * @package Database\Seeders
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class CelebritySeeder extends Seeder
{
    public function run(): void
    {
        $actorCat = TalentCategory::where('name', 'Actor')->first();
        $singerCat = TalentCategory::where('name', 'Singer & Musician')->first();
        $influencerCat = TalentCategory::where('name', 'Digital Creator & Influencer')->first();
        $modelCat = TalentCategory::where('name', 'Fashion Model')->first();
        $sportsCat = TalentCategory::where('name', 'Sports Personality')->first();
        $chefCat = TalentCategory::where('name', 'Celebrity Chef & Host')->first();

        $celebrities = [
            // 1. Celebrity A (Demo scenario primary talent)
            [
                'talent_code' => 'TLT-0001',
                'category_id' => $actorCat?->id,
                'stage_name' => 'Celebrity A (Aanya Sen)',
                'legal_name' => 'Aanya Priya Sen',
                'biography' => 'Leading Bollywood and OTT actress with multiple film awards, brand endorsements and nationwide appeal.',
                'base_city' => 'Mumbai',
                'email' => 'aanya.private@agencytalent.test',
                'phone' => '+91 98200 90001',
                'manager_name' => 'Rohit Verma',
                'manager_contact' => '+91 98200 90002',
                'profile_image' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=500&auto=format&fit=crop&q=80',
                'availability_status' => 'available',
                'profile_status' => 'active',
                'notes' => 'Primary talent for ABC Fashion / Brand X advertising campaign.',
            ],
            // 2.
            [
                'talent_code' => 'TLT-0002',
                'category_id' => $actorCat?->id,
                'stage_name' => 'Aryan Malhotra',
                'legal_name' => 'Aryan Vikram Malhotra',
                'biography' => 'Action film star, fitness icon, and leading male youth brand ambassador.',
                'base_city' => 'Mumbai',
                'email' => 'aryan.m@agencytalent.test',
                'phone' => '+91 98200 90003',
                'manager_name' => 'Kunal Joshi',
                'manager_contact' => '+91 98200 90004',
                'profile_image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=500&auto=format&fit=crop&q=80',
                'availability_status' => 'available',
                'profile_status' => 'active',
                'notes' => 'Preferred for automotive, tech and energy brands.',
            ],
            // 3.
            [
                'talent_code' => 'TLT-0003',
                'category_id' => $singerCat?->id,
                'stage_name' => 'Rhea Kapoor Singer',
                'legal_name' => 'Rhea Sunidhi Kapoor',
                'biography' => 'Chart-topping playback singer and live touring artist with over 15M monthly streams.',
                'base_city' => 'Mumbai',
                'email' => 'rhea.live@agencytalent.test',
                'phone' => '+91 98200 90005',
                'manager_name' => 'Deepak Suri',
                'manager_contact' => '+91 98200 90006',
                'profile_image' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=500&auto=format&fit=crop&q=80',
                'availability_status' => 'available',
                'profile_status' => 'active',
                'notes' => 'Available for festival headliners and luxury corporate events.',
            ],
            // 4.
            [
                'talent_code' => 'TLT-0004',
                'category_id' => $singerCat?->id,
                'stage_name' => 'Kabir Vox',
                'legal_name' => 'Kabir Sharma',
                'biography' => 'Indie pop and rock singer-songwriter known for soulful acoustic sets and major music festivals.',
                'base_city' => 'New Delhi',
                'email' => 'kabir.vox@agencytalent.test',
                'phone' => '+91 98110 90007',
                'manager_name' => 'Megha Arora',
                'manager_contact' => '+91 98110 90008',
                'profile_image' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=500&auto=format&fit=crop&q=80',
                'availability_status' => 'busy',
                'profile_status' => 'active',
                'notes' => 'Currently on North America tour until next month.',
            ],
            // 5.
            [
                'talent_code' => 'TLT-0005',
                'category_id' => $influencerCat?->id,
                'stage_name' => 'Tara Sparkles',
                'legal_name' => 'Tara Bhatia',
                'biography' => 'Top beauty, skincare and luxury lifestyle digital creator with 4.8M Instagram followers.',
                'base_city' => 'Mumbai',
                'email' => 'tara@agencytalent.test',
                'phone' => '+91 98200 90009',
                'manager_name' => 'Pooja Nair',
                'manager_contact' => '+91 98200 90010',
                'profile_image' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=500&auto=format&fit=crop&q=80',
                'availability_status' => 'available',
                'profile_status' => 'active',
                'notes' => 'Consistently high engagement rate in beauty/fashion.',
            ],
            // 6.
            [
                'talent_code' => 'TLT-0006',
                'category_id' => $influencerCat?->id,
                'stage_name' => 'Samir TechVibe',
                'legal_name' => 'Samir Khandelwal',
                'biography' => 'India leading consumer technology and gadget reviewer with 6.2M YouTube subscribers.',
                'base_city' => 'Bengaluru',
                'email' => 'samir@agencytalent.test',
                'phone' => '+91 98860 90011',
                'manager_name' => 'Nikhil Gupta',
                'manager_contact' => '+91 98860 90012',
                'profile_image' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=500&auto=format&fit=crop&q=80',
                'availability_status' => 'available',
                'profile_status' => 'active',
                'notes' => 'Top pick for consumer tech product unboxings and launches.',
            ],
            // 7.
            [
                'talent_code' => 'TLT-0007',
                'category_id' => $modelCat?->id,
                'stage_name' => 'Natasha Roy Model',
                'legal_name' => 'Natasha Catherine Roy',
                'biography' => 'International runway model, Vogue cover face and high-fashion luxury brand ambassador.',
                'base_city' => 'Mumbai',
                'email' => 'natasha.roy@agencytalent.test',
                'phone' => '+91 98200 90013',
                'manager_name' => 'Aditi Rao',
                'manager_contact' => '+91 98200 90014',
                'profile_image' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=500&auto=format&fit=crop&q=80',
                'availability_status' => 'available',
                'profile_status' => 'active',
                'notes' => 'Milan and Paris fashion week regular.',
            ],
            // 8.
            [
                'talent_code' => 'TLT-0008',
                'category_id' => $modelCat?->id,
                'stage_name' => 'Zahan Merchant',
                'legal_name' => 'Zahan Cyrus Merchant',
                'biography' => 'Men fashion and editorial model featuring in top menswear campaigns across India and UAE.',
                'base_city' => 'Mumbai',
                'email' => 'zahan@agencytalent.test',
                'phone' => '+91 98200 90015',
                'manager_name' => 'Rohit Verma',
                'manager_contact' => '+91 98200 90016',
                'profile_image' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=500&auto=format&fit=crop&q=80',
                'availability_status' => 'available',
                'profile_status' => 'active',
                'notes' => 'Suits and luxury grooming specialist.',
            ],
            // 9.
            [
                'talent_code' => 'TLT-0009',
                'category_id' => $sportsCat?->id,
                'stage_name' => 'Rohan Rathore (Cricket)',
                'legal_name' => 'Rohan Singh Rathore',
                'biography' => 'National cricket team opening batsman and international sporting personality.',
                'base_city' => 'New Delhi',
                'email' => 'rohan.rathore@agencytalent.test',
                'phone' => '+91 98110 90017',
                'manager_name' => 'Manish Chhabra',
                'manager_contact' => '+91 98110 90018',
                'profile_image' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=500&auto=format&fit=crop&q=80',
                'availability_status' => 'available',
                'profile_status' => 'active',
                'notes' => 'High tier athletic endorsements and TV commercials.',
            ],
            // 10.
            [
                'talent_code' => 'TLT-0010',
                'category_id' => $sportsCat?->id,
                'stage_name' => 'Alisha D’Cruz (Tennis)',
                'legal_name' => 'Alisha Maria D’Cruz',
                'biography' => 'Grand Slam tennis champion and advocate for youth athletics and women health.',
                'base_city' => 'Bengaluru',
                'email' => 'alisha@agencytalent.test',
                'phone' => '+91 98860 90019',
                'manager_name' => 'Sharad Rao',
                'manager_contact' => '+91 98860 90020',
                'profile_image' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=500&auto=format&fit=crop&q=80',
                'availability_status' => 'available',
                'profile_status' => 'active',
                'notes' => 'Sports wear, beverages and financial security ads.',
            ],
            // 11.
            [
                'talent_code' => 'TLT-0011',
                'category_id' => $chefCat?->id,
                'stage_name' => 'Chef Kunal Khanna',
                'legal_name' => 'Kunal Dev Khanna',
                'biography' => 'Michelin star celebrity chef, MasterChef judge, and author of 4 culinary bestsellers.',
                'base_city' => 'Mumbai',
                'email' => 'chef.kunal@agencytalent.test',
                'phone' => '+91 98200 90021',
                'manager_name' => 'Sunil Shetty',
                'manager_contact' => '+91 98200 90022',
                'profile_image' => 'https://images.unsplash.com/photo-1577219491135-ce391730fb2c?w=500&auto=format&fit=crop&q=80',
                'availability_status' => 'available',
                'profile_status' => 'active',
                'notes' => 'Food, kitchenware and luxury resort brand ambassador.',
            ],
            // 12.
            [
                'talent_code' => 'TLT-0012',
                'category_id' => $chefCat?->id,
                'stage_name' => 'Rochelle Anchor',
                'legal_name' => 'Rochelle Maria Pinto',
                'biography' => 'Prime-time television host, Filmfare emcee, and corporate keynote moderator.',
                'base_city' => 'Mumbai',
                'email' => 'rochelle@agencytalent.test',
                'phone' => '+91 98200 90023',
                'manager_name' => 'Pooja Nair',
                'manager_contact' => '+91 98200 90024',
                'profile_image' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=500&auto=format&fit=crop&q=80',
                'availability_status' => 'available',
                'profile_status' => 'active',
                'notes' => 'High profile corporate gala host.',
            ],
            // 13.
            [
                'talent_code' => 'TLT-0013',
                'category_id' => $actorCat?->id,
                'stage_name' => 'Siddharth Deshmukh',
                'legal_name' => 'Siddharth Deshmukh',
                'biography' => 'Versatile regional cinema and OTT drama actor with pan-India acclaim.',
                'base_city' => 'Pune',
                'email' => 'siddharth@agencytalent.test',
                'phone' => '+91 98220 90025',
                'manager_name' => 'Anil Pawar',
                'manager_contact' => '+91 98220 90026',
                'profile_image' => 'https://images.unsplash.com/photo-1501196354995-cbb51c65aaea?w=500&auto=format&fit=crop&q=80',
                'availability_status' => 'available',
                'profile_status' => 'active',
                'notes' => 'Regional TV commercials and OTT web series.',
            ],
            // 14.
            [
                'talent_code' => 'TLT-0014',
                'category_id' => $actorCat?->id,
                'stage_name' => 'Meera Chidambaram',
                'legal_name' => 'Meera Chidambaram',
                'biography' => 'Acclaimed South Indian actress and classical dancer with strong regional presence.',
                'base_city' => 'Chennai',
                'email' => 'meera.c@agencytalent.test',
                'phone' => '+91 98400 90027',
                'manager_name' => 'Ravi Sundaram',
                'manager_contact' => '+91 98400 90028',
                'profile_image' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=500&auto=format&fit=crop&q=80',
                'availability_status' => 'available',
                'profile_status' => 'active',
                'notes' => 'Jewellery and ethnic apparel endorsements.',
            ],
            // 15.
            [
                'talent_code' => 'TLT-0015',
                'category_id' => $singerCat?->id,
                'stage_name' => 'DJ BeatWave (Arjun)',
                'legal_name' => 'Arjun Singhal',
                'biography' => 'Electronic dance music DJ and producer performing at Sunburn and international festivals.',
                'base_city' => 'Goa',
                'email' => 'beatwave@agencytalent.test',
                'phone' => '+91 98230 90029',
                'manager_name' => 'Kunal Joshi',
                'manager_contact' => '+91 98230 90030',
                'profile_image' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?w=500&auto=format&fit=crop&q=80',
                'availability_status' => 'available',
                'profile_status' => 'active',
                'notes' => 'Campus festivals and nightclub mega shows.',
            ],
            // 16.
            [
                'talent_code' => 'TLT-0016',
                'category_id' => $influencerCat?->id,
                'stage_name' => 'Dr. HealthFit (Aditya)',
                'legal_name' => 'Dr. Aditya Kulkarni',
                'biography' => 'Medical doctor and certified fitness expert sharing science-backed nutrition and training advice.',
                'base_city' => 'Mumbai',
                'email' => 'aditya@agencytalent.test',
                'phone' => '+91 98200 90031',
                'manager_name' => 'Pooja Nair',
                'manager_contact' => '+91 98200 90032',
                'profile_image' => 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?w=500&auto=format&fit=crop&q=80',
                'availability_status' => 'available',
                'profile_status' => 'active',
                'notes' => 'Health supplements, fitness gear and wearable tech.',
            ],
            // 17.
            [
                'talent_code' => 'TLT-0017',
                'category_id' => $influencerCat?->id,
                'stage_name' => 'Simran Wanderlust',
                'legal_name' => 'Simran Walia',
                'biography' => 'Luxury travel documentarian and photographer featured on National Geographic Traveller.',
                'base_city' => 'Jaipur',
                'email' => 'simran@agencytalent.test',
                'phone' => '+91 98290 90033',
                'manager_name' => 'Rohit Verma',
                'manager_contact' => '+91 98290 90034',
                'profile_image' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=500&auto=format&fit=crop&q=80',
                'availability_status' => 'available',
                'profile_status' => 'active',
                'notes' => 'Hospitality, tourism boards and camera equipment.',
            ],
            // 18.
            [
                'talent_code' => 'TLT-0018',
                'category_id' => $modelCat?->id,
                'stage_name' => 'Ishaani Sen Model',
                'legal_name' => 'Ishaani Sen',
                'biography' => 'Bridal couture and high jewelry supermodel with regular appearances at India Couture Week.',
                'base_city' => 'Kolkata',
                'email' => 'ishaani@agencytalent.test',
                'phone' => '+91 98300 90035',
                'manager_name' => 'Aditi Rao',
                'manager_contact' => '+91 98300 90036',
                'profile_image' => 'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?w=500&auto=format&fit=crop&q=80',
                'availability_status' => 'available',
                'profile_status' => 'active',
                'notes' => 'Luxury bridal campaigns and commercial fashion shoots.',
            ],
            // 19.
            [
                'talent_code' => 'TLT-0019',
                'category_id' => $sportsCat?->id,
                'stage_name' => 'Vikramaditya (Formula E)',
                'legal_name' => 'Vikramaditya Roy',
                'biography' => 'International motorsport racer representing India in world championship circuits.',
                'base_city' => 'Chennai',
                'email' => 'vikram.race@agencytalent.test',
                'phone' => '+91 98400 90037',
                'manager_name' => 'Sharad Rao',
                'manager_contact' => '+91 98400 90038',
                'profile_image' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=500&auto=format&fit=crop&q=80',
                'availability_status' => 'available',
                'profile_status' => 'active',
                'notes' => 'Automotive, luxury watches and precision tech brands.',
            ],
            // 20.
            [
                'talent_code' => 'TLT-0020',
                'category_id' => $actorCat?->id,
                'stage_name' => 'Devraj Oberoi',
                'legal_name' => 'Devraj Oberoi',
                'biography' => 'Veteran character actor, voiceover virtuoso and iconic narrator for historical docuseries.',
                'base_city' => 'Mumbai',
                'email' => 'devraj@agencytalent.test',
                'phone' => '+91 98200 90039',
                'manager_name' => 'Kunal Joshi',
                'manager_contact' => '+91 98200 90040',
                'profile_image' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=500&auto=format&fit=crop&q=80',
                'availability_status' => 'available',
                'profile_status' => 'active',
                'notes' => 'Voiceover campaigns, radio ads and luxury heritage endorsements.',
            ],
        ];

        foreach ($celebrities as $c) {
            Celebrity::updateOrCreate(['talent_code' => $c['talent_code']], $c);
        }
    }
}

