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
use App\Models\ClientContact;
use Illuminate\Database\Seeder;

/**
 * Class ClientContactSeeder
 *
 * Enterprise Seeder responsible for ClientContactSeeder business logic and data operations.
 *
 * @package Database\Seeders
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class ClientContactSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all()->keyBy('client_code');

        $contacts = [
            // ABC Fashion
            ['client' => 'CLT-0001', 'name' => 'Rajesh Mehra', 'designation' => 'Chief Marketing Officer', 'email' => 'rajesh.mehra@abcfashion.example.com', 'phone' => '+91 98201 11223', 'is_primary' => true],
            ['client' => 'CLT-0001', 'name' => 'Priya Tandon', 'designation' => 'Brand Director', 'email' => 'priya.t@abcfashion.example.com', 'phone' => '+91 98201 11224', 'is_primary' => false],
            // Zenith
            ['client' => 'CLT-0002', 'name' => 'Sunil Varma', 'designation' => 'VP Brand Marketing', 'email' => 'sunil.v@zenithbrands.example.com', 'phone' => '+91 98111 22334', 'is_primary' => true],
            ['client' => 'CLT-0002', 'name' => 'Meera Chawla', 'designation' => 'Media Buying Lead', 'email' => 'meera.c@zenithbrands.example.com', 'phone' => '+91 98111 22335', 'is_primary' => false],
            // Nexus Media
            ['client' => 'CLT-0003', 'name' => 'Devika Sen', 'designation' => 'Managing Partner', 'email' => 'devika@nexuscreative.example.com', 'phone' => '+91 98450 33445', 'is_primary' => true],
            ['client' => 'CLT-0003', 'name' => 'Kunal Bansal', 'designation' => 'Celebrity Outreach Lead', 'email' => 'kunal@nexuscreative.example.com', 'phone' => '+91 98450 33446', 'is_primary' => false],
            // Aura Cosmetics
            ['client' => 'CLT-0004', 'name' => 'Natasha D’Souza', 'designation' => 'Head of PR & Influencers', 'email' => 'natasha@auracosmetics.example.com', 'phone' => '+91 98202 44556', 'is_primary' => true],
            ['client' => 'CLT-0004', 'name' => 'Ritika Sen', 'designation' => 'Brand Manager', 'email' => 'ritika@auracosmetics.example.com', 'phone' => '+91 98202 44557', 'is_primary' => false],
            // Titanium Devices
            ['client' => 'CLT-0005', 'name' => 'Amit Saxena', 'designation' => 'Marketing Director', 'email' => 'amit.s@titaniumtech.example.com', 'phone' => '+91 98101 55667', 'is_primary' => true],
            ['client' => 'CLT-0005', 'name' => 'Shreya Rao', 'designation' => 'Campaign Coordinator', 'email' => 'shreya@titaniumtech.example.com', 'phone' => '+91 98101 55668', 'is_primary' => false],
            // Saffron Hospitality
            ['client' => 'CLT-0006', 'name' => 'Chef Ranveer Nanda', 'designation' => 'Director Hospitality', 'email' => 'ranveer@saffronhotels.example.com', 'phone' => '+91 98203 66778', 'is_primary' => true],
            // Oasis Beverages
            ['client' => 'CLT-0007', 'name' => 'Karthik Raman', 'designation' => 'Brand Head - Energy Drinks', 'email' => 'karthik@oasisbev.example.com', 'phone' => '+91 98401 77889', 'is_primary' => true],
            ['client' => 'CLT-0007', 'name' => 'Sowmya Krishnan', 'designation' => 'Activation Manager', 'email' => 'sowmya@oasisbev.example.com', 'phone' => '+91 98401 77890', 'is_primary' => false],
            // Pulse Fitness
            ['client' => 'CLT-0008', 'name' => 'Gaurav Kulkarni', 'designation' => 'Founder & CEO', 'email' => 'gaurav@pulsefit.example.com', 'phone' => '+91 98220 88990', 'is_primary' => true],
            // Silver Screen
            ['client' => 'CLT-0009', 'name' => 'Mahesh Bhattacharya', 'designation' => 'Casting Director', 'email' => 'mahesh@silverscreen.example.com', 'phone' => '+91 98204 99001', 'is_primary' => true],
            ['client' => 'CLT-0009', 'name' => 'Simran Kaur', 'designation' => 'Production Controller', 'email' => 'simran@silverscreen.example.com', 'phone' => '+91 98204 99002', 'is_primary' => false],
            // Velocity Motors
            ['client' => 'CLT-0010', 'name' => 'Vikrant Anand', 'designation' => 'Vice President Corporate Affairs', 'email' => 'vikrant@velocitymotors.example.com', 'phone' => '+91 98112 11002', 'is_primary' => true],
            // Verve FinTech
            ['client' => 'CLT-0011', 'name' => 'Ravi Chandran', 'designation' => 'Head of Brand & Growth', 'email' => 'ravi@vervefin.example.com', 'phone' => '+91 98860 22334', 'is_primary' => true],
            // Kalyan Jewel
            ['client' => 'CLT-0012', 'name' => 'Murali Menon', 'designation' => 'Managing Director', 'email' => 'murali@kalyanjewels.example.com', 'phone' => '+91 98470 33445', 'is_primary' => true],
            ['client' => 'CLT-0012', 'name' => 'Lakshmi Warrier', 'designation' => 'Head of Media', 'email' => 'lakshmi@kalyanjewels.example.com', 'phone' => '+91 98470 33446', 'is_primary' => false],
            // Global Star
            ['client' => 'CLT-0013', 'name' => 'Farhan Qureshi', 'designation' => 'Tour Director', 'email' => 'farhan@globalstarconcerts.example.com', 'phone' => '+91 98205 44556', 'is_primary' => true],
            // Prime Living
            ['client' => 'CLT-0014', 'name' => 'Harsh Singhi', 'designation' => 'Director Sales & Mktg', 'email' => 'harsh@primeliving.example.com', 'phone' => '+91 98206 55667', 'is_primary' => true],
            ['client' => 'CLT-0014', 'name' => 'Neha Somani', 'designation' => 'PR Lead', 'email' => 'neha@primeliving.example.com', 'phone' => '+91 98206 55668', 'is_primary' => false],
            // EcoPure
            ['client' => 'CLT-0015', 'name' => 'Siddharth Reddy', 'designation' => 'Co-Founder', 'email' => 'sid@ecopure.example.com', 'phone' => '+91 98490 66778', 'is_primary' => true],
            ['client' => 'CLT-0015', 'name' => 'Ankita Paul', 'designation' => 'Marketing Associate', 'email' => 'ankita@ecopure.example.com', 'phone' => '+91 98490 66779', 'is_primary' => false],
        ];

        foreach ($contacts as $cnt) {
            $clientModel = $clients[$cnt['client']] ?? null;
            if ($clientModel) {
                ClientContact::firstOrCreate(
                    ['client_id' => $clientModel->id, 'email' => $cnt['email']],
                    [
                        'name' => $cnt['name'],
                        'designation' => $cnt['designation'],
                        'phone' => $cnt['phone'],
                        'is_primary' => $cnt['is_primary'],
                        'notes' => 'Seeded client key contact.',
                    ]
                );
            }
        }
    }
}

