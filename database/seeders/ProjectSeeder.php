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
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\ProjectMilestone;
use App\Models\ProjectTask;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Class ProjectSeeder
 *
 * Enterprise Seeder responsible for ProjectSeeder business logic and data operations.
 *
 * @package Database\Seeders
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projectManager = User::where('email', 'project@demo.test')->first() ?? User::first();
        $bookingCoord = User::where('email', 'booking@demo.test')->first() ?? User::first();
        $salesManager = User::where('email', 'sales@demo.test')->first() ?? User::first();

        $quote1 = Quotation::where('quotation_no', 'QT-2026-0001')->first();
        $clientABC = Client::where('client_code', 'CLT-0001')->first();

        // 1. Primary Demo Campaign Project (Blueprint Section 3.2 Page 8)
        $p1 = Project::updateOrCreate(
            ['project_code' => 'PRJ-2026-0001'],
            [
                'quotation_id' => $quote1?->id,
                'client_id' => $clientABC->id,
                'project_name' => 'Brand X Advertising Campaign',
                'project_type' => 'endorsement',
                'project_manager_id' => $projectManager->id,
                'description' => 'Comprehensive nationwide advertising campaign for Brand X luxury apparel, featuring Celebrity A across digital, outdoor and television commercials.',
                'start_date' => now()->subMonths(1)->startOfMonth(),
                'end_date' => now()->addMonths(2)->endOfMonth(),
                'budget_amount' => 1500000.00,
                'currency' => 'INR',
                'status' => 'active',
                'created_by' => $salesManager->id,
            ]
        );

        ProjectMember::firstOrCreate(['project_id' => $p1->id, 'user_id' => $projectManager->id], ['assigned_role' => 'Project Lead']);
        ProjectMember::firstOrCreate(['project_id' => $p1->id, 'user_id' => $bookingCoord->id], ['assigned_role' => 'Talent & Logistics Coordinator']);

        // Milestones for P1
        ProjectMilestone::firstOrCreate(['project_id' => $p1->id, 'milestone_name' => 'Concept & Storyboard Finalization'], [
            'due_date' => now()->subMonths(1)->addDays(10),
            'status' => 'completed',
            'completed_at' => now()->subMonths(1)->addDays(9),
        ]);
        ProjectMilestone::firstOrCreate(['project_id' => $p1->id, 'milestone_name' => 'Photoshoot & TVC Staging'], [
            'due_date' => now()->addDays(5),
            'status' => 'pending',
        ]);
        ProjectMilestone::firstOrCreate(['project_id' => $p1->id, 'milestone_name' => 'Final Commercial Post-Production & Delivery'], [
            'due_date' => now()->addDays(25),
            'status' => 'pending',
        ]);

        // Tasks for P1
        ProjectTask::firstOrCreate(['project_id' => $p1->id, 'task_title' => 'Celebrity Styling & Wardrobe Finalization'], [
            'description' => 'Coordinate designer wear and wardrobe fittings with Celebrity A stylist.',
            'assigned_to' => $bookingCoord->id,
            'priority' => 'high',
            'status' => 'done',
            'start_date' => now()->subDays(15),
            'due_date' => now()->subDays(5),
            'completed_at' => now()->subDays(6),
        ]);
        ProjectTask::firstOrCreate(['project_id' => $p1->id, 'task_title' => 'Studio Booking & Stage Equipment Setup'], [
            'description' => 'Secure Mehboob Studios Floor 2 and Arri Alexa mini LF camera package.',
            'assigned_to' => $projectManager->id,
            'priority' => 'urgent',
            'status' => 'in_progress',
            'start_date' => now()->subDays(2),
            'due_date' => now()->addDays(4),
        ]);
        ProjectTask::firstOrCreate(['project_id' => $p1->id, 'task_title' => 'Social Media Asset Cutdowns (Reels & Shorts)'], [
            'description' => 'Produce 9:16 vertical teaser cuts for Instagram and YouTube Shorts.',
            'assigned_to' => $projectManager->id,
            'priority' => 'medium',
            'status' => 'open',
            'start_date' => now()->addDays(6),
            'due_date' => now()->addDays(20),
        ]);

        // 14 More Projects
        $otherQuotes = Quotation::where('status', 'converted')->where('id', '!=', $quote1?->id)->get();
        $clients = Client::all();
        $types = ['campaign', 'event', 'shoot', 'endorsement'];
        $statuses = ['active', 'active', 'planned', 'completed', 'on_hold'];

        for ($i = 2; $i <= 15; $i++) {
            $code = sprintf('PRJ-2026-%04d', $i);
            $client = $clients[$i % $clients->count()];
            $type = $types[$i % count($types)];
            $status = $statuses[($i - 2) % count($statuses)];
            $quote = $otherQuotes[($i - 2) % max(1, $otherQuotes->count())] ?? null;

            $monthsOffset = ($i % 3) - 1;
            $startDate = now()->addMonths($monthsOffset)->startOfMonth();
            $endDate = (clone $startDate)->addMonths(2);

            $proj = Project::updateOrCreate(
                ['project_code' => $code],
                [
                    'quotation_id' => ($quote && !$quote->project()->exists()) ? $quote->id : null,
                    'client_id' => $client->id,
                    'project_name' => "{$client->company_name} " . ucfirst($type) . " 2026",
                    'project_type' => $type,
                    'project_manager_id' => $projectManager->id,
                    'description' => "Execution of strategic talent engagement for {$client->company_name}.",
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'budget_amount' => 500000 + (($i * 125000) % 2500000),
                    'currency' => 'INR',
                    'status' => $status,
                    'created_by' => $salesManager->id,
                ]
            );

            ProjectMember::firstOrCreate(['project_id' => $proj->id, 'user_id' => $projectManager->id], ['assigned_role' => 'Lead Producer']);

            ProjectMilestone::firstOrCreate(['project_id' => $proj->id, 'milestone_name' => 'Project Delivery Phase 1'], [
                'due_date' => (clone $startDate)->addDays(20),
                'status' => ($status === 'completed') ? 'completed' : 'pending',
                'completed_at' => ($status === 'completed') ? (clone $startDate)->addDays(18) : null,
            ]);

            ProjectTask::firstOrCreate(['project_id' => $proj->id, 'task_title' => 'Talent briefing and script review'], [
                'description' => 'Review script notes and call time logistics with talent manager.',
                'assigned_to' => $bookingCoord->id,
                'priority' => 'medium',
                'status' => ($status === 'completed') ? 'done' : 'open',
                'start_date' => $startDate,
                'due_date' => (clone $startDate)->addDays(10),
            ]);
        }
    }
}
