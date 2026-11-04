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
use App\Models\Contract;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Class ContractSeeder
 *
 * Enterprise Seeder responsible for ContractSeeder business logic and data operations.
 *
 * @package Database\Seeders
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class ContractSeeder extends Seeder
{
    public function run(): void
    {
        $docManager = User::where('email', 'documents@demo.test')->first() ?? User::first();
        $clientABC = Client::where('client_code', 'CLT-0001')->first();
        $celebA = Celebrity::where('talent_code', 'TLT-0001')->first();
        $p1 = Project::where('project_code', 'PRJ-2026-0001')->first();

        // 1. Primary Demo Contract (ABC Fashion / Brand X / Celebrity A)
        Contract::updateOrCreate(
            ['contract_no' => 'CNT-2026-0001'],
            [
                'project_id' => $p1?->id,
                'client_id' => $clientABC->id,
                'celebrity_id' => $celebA->id,
                'contract_type' => 'Endorsement & Licensing',
                'contract_title' => 'Brand X Master Endorsement & Likeness License Agreement',
                'start_date' => now()->subMonths(1),
                'expiry_date' => now()->addDays(28), // approaching 30-day expiry alert!
                'usage_rights' => 'Exclusive brand ambassador rights for women luxury apparel, accessories and footwear.',
                'territory' => 'Republic of India and UAE / GCC',
                'permitted_channels' => ['Television', 'Digital OTT', 'Social Media', 'Print & Outdoor Hoardings'],
                'exclusivity' => true,
                'signed_at' => now()->subMonths(1)->addDays(2),
                'status' => 'active',
                'created_by' => $docManager->id,
            ]
        );

        // 19 more contracts
        $clients = Client::all();
        $celebrities = Celebrity::all();
        $projects = Project::all();
        $types = ['Endorsement', 'Appearance', 'Licence Agreement', 'NDA & Confidentiality', 'Social Media Influencer Agreement'];

        for ($i = 2; $i <= 20; $i++) {
            $code = sprintf('CNT-2026-%04d', $i);
            $client = $clients[$i % $clients->count()];
            $celeb = $celebrities[$i % $celebrities->count()];
            $project = $projects[$i % $projects->count()];
            $type = $types[$i % count($types)];

            // Create some expiring in 7 days, some in 25 days, some in 6 months
            $expiryDays = match ($i % 5) {
                0 => 7,  // 7-day expiry trigger!
                1 => 22, // 30-day expiry trigger!
                2 => 90,
                3 => 180,
                default => 365,
            };

            $startDate = now()->subMonths(rand(1, 6));
            $expiryDate = now()->addDays($expiryDays);

            Contract::updateOrCreate(
                ['contract_no' => $code],
                [
                    'project_id' => $project->id,
                    'client_id' => $client->id,
                    'celebrity_id' => $celeb->id,
                    'contract_type' => $type,
                    'contract_title' => "{$client->company_name} - {$celeb->stage_name} {$type}",
                    'start_date' => $startDate,
                    'expiry_date' => $expiryDate,
                    'usage_rights' => 'Commercial likeness usage, campaign rights and distribution privileges.',
                    'territory' => ($i % 2 === 0) ? 'Pan India' : 'Worldwide Digital Rights',
                    'permitted_channels' => ['Social Media', 'Digital OTT', 'Website'],
                    'exclusivity' => ($i % 3 === 0),
                    'signed_at' => (clone $startDate)->addDays(3),
                    'status' => 'active',
                    'created_by' => $docManager->id,
                ]
            );
        }
    }
}

