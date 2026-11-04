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
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Class ClientSeeder
 *
 * Enterprise Seeder responsible for ClientSeeder business logic and data operations.
 *
 * @package Database\Seeders
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::first();

        $clients = [
            [
                'client_code' => 'CLT-0001',
                'company_name' => 'ABC Fashion Pvt. Ltd.',
                'client_type' => 'brand',
                'industry' => 'Apparel & Luxury Fashion',
                'website' => 'https://abcfashion.example.com',
                'email' => 'partnerships@abcfashion.example.com',
                'phone' => '+91 22 4589 1000',
                'billing_address' => 'Tower B, Nariman Point, Mumbai, Maharashtra 400021',
                'tax_number' => '27AAACA1234F1Z8',
                'payment_terms_days' => 30,
                'status' => 'active',
                'notes' => 'Primary client for Brand X advertising campaigns.',
            ],
            [
                'client_code' => 'CLT-0002',
                'company_name' => 'Zenith Consumer Brands',
                'client_type' => 'brand',
                'industry' => 'FMCG & Personal Care',
                'website' => 'https://zenithbrands.example.com',
                'email' => 'marketing@zenithbrands.example.com',
                'phone' => '+91 11 2345 6789',
                'billing_address' => 'Connaught Place, New Delhi 110001',
                'tax_number' => '07AABCZ9876K1Z2',
                'payment_terms_days' => 45,
                'status' => 'active',
                'notes' => 'Major FMCG conglomerate with quarterly celebrity campaigns.',
            ],
            [
                'client_code' => 'CLT-0003',
                'company_name' => 'Nexus Media & Creative Agency',
                'client_type' => 'agency',
                'industry' => 'Advertising & PR',
                'website' => 'https://nexuscreative.example.com',
                'email' => 'accounts@nexuscreative.example.com',
                'phone' => '+91 80 4123 5500',
                'billing_address' => 'Indiranagar 100ft Road, Bengaluru, Karnataka 560038',
                'tax_number' => '29AAACN3344M1Z5',
                'payment_terms_days' => 30,
                'status' => 'active',
                'notes' => 'Top tier ad agency booking our talent for multiple brands.',
            ],
            [
                'client_code' => 'CLT-0004',
                'company_name' => 'Aura Cosmetics International',
                'client_type' => 'brand',
                'industry' => 'Beauty & Skincare',
                'website' => 'https://auracosmetics.example.com',
                'email' => 'pr@auracosmetics.example.com',
                'phone' => '+91 22 6123 8899',
                'billing_address' => 'Bandra Kurla Complex, Mumbai 400051',
                'tax_number' => '27AAACA9988D1Z9',
                'payment_terms_days' => 30,
                'status' => 'active',
                'notes' => 'Frequent influencer and brand ambassador agreements.',
            ],
            [
                'client_code' => 'CLT-0005',
                'company_name' => 'Titanium Smart Devices',
                'client_type' => 'brand',
                'industry' => 'Consumer Electronics',
                'website' => 'https://titaniumtech.example.com',
                'email' => 'collabs@titaniumtech.example.com',
                'phone' => '+91 124 456 7890',
                'billing_address' => 'Cyber City Phase 2, Gurugram, Haryana 122002',
                'tax_number' => '06AAACT5566R1ZQ',
                'payment_terms_days' => 60,
                'status' => 'active',
                'notes' => 'Annual product launch ambassadors.',
            ],
            [
                'client_code' => 'CLT-0006',
                'company_name' => 'Saffron Hospitality Group',
                'client_type' => 'brand',
                'industry' => 'Hospitality & Luxury Dining',
                'website' => 'https://saffronhotels.example.com',
                'email' => 'events@saffronhotels.example.com',
                'phone' => '+91 22 2844 1122',
                'billing_address' => 'Juhu Tara Road, Mumbai 400049',
                'tax_number' => '27AAACS1122J1Z1',
                'payment_terms_days' => 15,
                'status' => 'active',
                'notes' => 'Regular celebrity appearances for restaurant launches.',
            ],
            [
                'client_code' => 'CLT-0007',
                'company_name' => 'Oasis Beverages Ltd.',
                'client_type' => 'brand',
                'industry' => 'Beverages',
                'website' => 'https://oasisbev.example.com',
                'email' => 'brand@oasisbev.example.com',
                'phone' => '+91 44 2811 9900',
                'billing_address' => 'Anna Salai, Chennai, Tamil Nadu 600002',
                'tax_number' => '33AAACO4455P1Z3',
                'payment_terms_days' => 30,
                'status' => 'active',
                'notes' => 'Youth energy drink endorsement campaigns.',
            ],
            [
                'client_code' => 'CLT-0008',
                'company_name' => 'Pulse Fitness & Lifestyle',
                'client_type' => 'brand',
                'industry' => 'Health & Fitness',
                'website' => 'https://pulsefit.example.com',
                'email' => 'marketing@pulsefit.example.com',
                'phone' => '+91 20 2567 8901',
                'billing_address' => 'FC Road, Pune, Maharashtra 411004',
                'tax_number' => '27AAACP7788L1Z6',
                'payment_terms_days' => 30,
                'status' => 'active',
                'notes' => 'Fitness ambassador contracts.',
            ],
            [
                'client_code' => 'CLT-0009',
                'company_name' => 'Silver Screen Entertainment',
                'client_type' => 'agency',
                'industry' => 'Film Production',
                'website' => 'https://silverscreen.example.com',
                'email' => 'casting@silverscreen.example.com',
                'phone' => '+91 22 2630 1199',
                'billing_address' => 'Andheri West, Mumbai 400053',
                'tax_number' => '27AAACS8899T1Z4',
                'payment_terms_days' => 30,
                'status' => 'active',
                'notes' => 'Feature film casting and cameo appearances.',
            ],
            [
                'client_code' => 'CLT-0010',
                'company_name' => 'Velocity Motors India',
                'client_type' => 'brand',
                'industry' => 'Automotive',
                'website' => 'https://velocitymotors.example.com',
                'email' => 'pr@velocitymotors.example.com',
                'phone' => '+91 11 4987 6543',
                'billing_address' => 'Okhla Industrial Area, New Delhi 110020',
                'tax_number' => '07AAACV6677H1Z0',
                'payment_terms_days' => 45,
                'status' => 'active',
                'notes' => 'Auto expo and launch event celebrity bookings.',
            ],
            [
                'client_code' => 'CLT-0011',
                'company_name' => 'Verve FinTech Solutions',
                'client_type' => 'brand',
                'industry' => 'Financial Services',
                'website' => 'https://vervefin.example.com',
                'email' => 'growth@vervefin.example.com',
                'phone' => '+91 80 6789 0123',
                'billing_address' => 'Koramangala 4th Block, Bengaluru 560034',
                'tax_number' => '29AAACV5544K1Z7',
                'payment_terms_days' => 30,
                'status' => 'active',
                'notes' => 'Brand trust campaigns featuring sports icons.',
            ],
            [
                'client_code' => 'CLT-0012',
                'company_name' => 'Kalyan Jewel Crafters',
                'client_type' => 'brand',
                'industry' => 'Jewellery & Luxury',
                'website' => 'https://kalyanjewels.example.com',
                'email' => 'advertising@kalyanjewels.example.com',
                'phone' => '+91 487 242 4111',
                'billing_address' => 'Round West, Thrissur, Kerala 680001',
                'tax_number' => '32AAACK1122D1Z5',
                'payment_terms_days' => 30,
                'status' => 'active',
                'notes' => 'Festive campaign endorsements.',
            ],
            [
                'client_code' => 'CLT-0013',
                'company_name' => 'Global Star Concerts',
                'client_type' => 'agency',
                'industry' => 'Live Entertainment',
                'website' => 'https://globalstarconcerts.example.com',
                'email' => 'tours@globalstarconcerts.example.com',
                'phone' => '+91 22 2288 3344',
                'billing_address' => 'Worli Seaface, Mumbai 400030',
                'tax_number' => '27AAACG9900B1Z8',
                'payment_terms_days' => 15,
                'status' => 'active',
                'notes' => 'Stadium tours and multi-city music events.',
            ],
            [
                'client_code' => 'CLT-0014',
                'company_name' => 'Prime Living Real Estate',
                'client_type' => 'brand',
                'industry' => 'Real Estate',
                'website' => 'https://primeliving.example.com',
                'email' => 'sales@primeliving.example.com',
                'phone' => '+91 22 6600 4400',
                'billing_address' => 'Lower Parel, Mumbai 400013',
                'tax_number' => '27AAACP4433E1Z2',
                'payment_terms_days' => 30,
                'status' => 'active',
                'notes' => 'Luxury residential project brand faces.',
            ],
            [
                'client_code' => 'CLT-0015',
                'company_name' => 'EcoPure Organics',
                'client_type' => 'brand',
                'industry' => 'Organic & Sustainable Goods',
                'website' => 'https://ecopure.example.com',
                'email' => 'hello@ecopure.example.com',
                'phone' => '+91 40 2311 5566',
                'billing_address' => 'Hitec City, Hyderabad, Telangana 500081',
                'tax_number' => '36AAACE6655L1Z1',
                'payment_terms_days' => 30,
                'status' => 'prospect',
                'notes' => 'Prospective client discussing influencer engagement.',
            ],
        ];

        foreach ($clients as $c) {
            Client::updateOrCreate(
                ['client_code' => $c['client_code']],
                array_merge($c, ['created_by' => $creator->id])
            );
        }
    }
}

