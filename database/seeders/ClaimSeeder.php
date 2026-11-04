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

use App\Models\Claim;
use App\Models\ClaimApproval;
use App\Models\ClaimItem;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Class ClaimSeeder
 *
 * Enterprise Seeder responsible for ClaimSeeder business logic and data operations.
 *
 * @package Database\Seeders
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class ClaimSeeder extends Seeder
{
    public function run(): void
    {
        $projectManager = User::where('email', 'project@demo.test')->first() ?? User::first();
        $bookingCoord = User::where('email', 'booking@demo.test')->first() ?? User::first();
        $financeManager = User::where('email', 'finance@demo.test')->first() ?? User::first();

        $p1 = Project::where('project_code', 'PRJ-2026-0001')->first() ?? Project::first();

        // 1. Primary Demo Claim 1: Travel & Entourage Logistics (Blueprint Section 3.2: INR 50,000)
        $c1 = Claim::updateOrCreate(
            ['claim_no' => 'CLM-2026-0001'],
            [
                'project_id' => $p1->id,
                'submitted_by' => $bookingCoord->id,
                'claim_date' => now()->subDays(10),
                'description' => 'Travel and luxury airport transfer for Celebrity A and personal stylist.',
                'total_amount' => 50000.00,
                'currency' => 'INR',
                'status' => 'approved',
                'submitted_at' => now()->subDays(10),
                'approved_at' => now()->subDays(8),
                'paid_at' => now()->subDays(5),
            ]
        );

        ClaimItem::firstOrCreate(
            ['claim_id' => $c1->id, 'description' => 'Business class flights Delhi-Mumbai for stylist'],
            [
                'expense_category' => 'Travel',
                'expense_date' => now()->subDays(11),
                'amount' => 32000.00,
            ]
        );
        ClaimItem::firstOrCreate(
            ['claim_id' => $c1->id, 'description' => 'Mercedes Maybach luxury airport transfers'],
            [
                'expense_category' => 'Transport',
                'expense_date' => now()->subDays(10),
                'amount' => 18000.00,
            ]
        );

        ClaimApproval::firstOrCreate(
            ['claim_id' => $c1->id, 'action' => 'approved'],
            [
                'user_id' => $financeManager->id,
                'comments' => 'Receipts verified against travel vouchers. Approved for reimbursement.',
                'action_at' => now()->subDays(8),
            ]
        );

        // Project Expense posted from Claim 1
        ProjectExpense::updateOrCreate(
            ['source_claim_id' => $c1->id],
            [
                'project_id' => $p1->id,
                'expense_category' => 'Travel & Transport',
                'vendor_name' => 'Aura Luxury Fleet & Airlines',
                'expense_date' => now()->subDays(10),
                'description' => "Posted from Claim {$c1->claim_no}: Travel and luxury transport",
                'amount' => 50000.00,
                'currency' => 'INR',
                'reference_no' => $c1->claim_no,
                'created_by' => $financeManager->id,
            ]
        );

        // 2. Primary Demo Project Direct Expense 1: Production Cost (Blueprint Section 3.2: INR 2,00,000)
        ProjectExpense::firstOrCreate(
            ['project_id' => $p1->id, 'reference_no' => 'PROD-EXP-001'],
            [
                'expense_category' => 'Production & Staging',
                'vendor_name' => 'Mehboob Studios & CineEquipment Ltd.',
                'expense_date' => now()->subDays(12),
                'description' => 'Studio floor rental, lighting rig and camera kit rental for Brand X shoot.',
                'amount' => 200000.00,
                'currency' => 'INR',
                'created_by' => $projectManager->id,
                'source_claim_id' => null,
            ]
        );

        // 3. Primary Demo Project Direct Expense 2: Other Expenses (Blueprint Section 3.2: INR 50,000)
        ProjectExpense::firstOrCreate(
            ['project_id' => $p1->id, 'reference_no' => 'MISC-EXP-002'],
            [
                'expense_category' => 'Catering & Hospitality',
                'vendor_name' => 'Gourmet Staging Catering Mumbai',
                'expense_date' => now()->subDays(9),
                'description' => 'Green room catering, vanity van setup and specialized refreshments.',
                'amount' => 50000.00,
                'currency' => 'INR',
                'created_by' => $projectManager->id,
                'source_claim_id' => null,
            ]
        );

        // 13 more claims across various projects
        $projects = Project::all();
        $categories = ['Travel', 'Meal & Hospitality', 'Equipment Rental', 'Wardrobe', 'Logistics'];
        $statuses = ['approved', 'approved', 'pending', 'draft', 'rejected'];

        for ($i = 2; $i <= 14; $i++) {
            $code = sprintf('CLM-2026-%04d', $i);
            $project = $projects[$i % $projects->count()];
            $status = $statuses[($i - 2) % count($statuses)];
            $amount = rand(15000, 85000);

            $claim = Claim::updateOrCreate(
                ['claim_no' => $code],
                [
                    'project_id' => $project->id,
                    'submitted_by' => ($i % 2 === 0) ? $bookingCoord->id : $projectManager->id,
                    'claim_date' => now()->subDays($i * 3),
                    'description' => "Operational expenses for {$project->project_name}",
                    'total_amount' => $amount,
                    'currency' => 'INR',
                    'status' => $status,
                    'submitted_at' => ($status !== 'draft') ? now()->subDays($i * 3) : null,
                    'approved_at' => ($status === 'approved') ? now()->subDays(($i * 3) - 2) : null,
                    'paid_at' => ($status === 'approved' && $i % 2 === 0) ? now()->subDays(($i * 3) - 3) : null,
                ]
            );

            ClaimItem::firstOrCreate(
                ['claim_id' => $claim->id, 'description' => "On-ground itemized expense for {$project->project_code}"],
                [
                    'expense_category' => $categories[$i % count($categories)],
                    'expense_date' => now()->subDays($i * 3),
                    'amount' => $amount,
                ]
            );

            if ($status === 'approved') {
                ClaimApproval::firstOrCreate(
                    ['claim_id' => $claim->id, 'action' => 'approved'],
                    [
                        'user_id' => $financeManager->id,
                        'comments' => 'Verified and approved by Finance.',
                        'action_at' => now()->subDays(($i * 3) - 2),
                    ]
                );

                ProjectExpense::updateOrCreate(
                    ['source_claim_id' => $claim->id],
                    [
                        'project_id' => $project->id,
                        'expense_category' => $categories[$i % count($categories)],
                        'vendor_name' => 'Operational Reimbursement',
                        'expense_date' => now()->subDays($i * 3),
                        'description' => "Reimbursement Claim {$claim->claim_no}",
                        'amount' => $amount,
                        'currency' => 'INR',
                        'reference_no' => $claim->claim_no,
                        'created_by' => $financeManager->id,
                    ]
                );
            }
        }
    }
}

