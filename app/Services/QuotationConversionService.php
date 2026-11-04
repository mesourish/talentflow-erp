<?php

/**
 * TalentFlow ERP - Celebrity & Talent Management System
 *
 * @package    App\Services
 * @author     Sourish Mondal
 * @version    1.0.0
 * @since      Laravel 12.x / PHP 8.2+
 * @copyright  2026 TalentFlow ERP. All rights reserved.
 * @license    Proprietary / Enterprise Commercial
 */

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Models\ProjectTask;
use App\Models\Quotation;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Class QuotationConversionService
 *
 * Domain service transforming approved commercial quotations into active production projects seamlessly.
 *
 * @package App\Services
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class QuotationConversionService
{
    public function __construct(
        protected CodeGenerator $codeGenerator
    ) {}

    /**
     * Convert an approved quotation into a project atomically.
     */
    public function convert(int $quotationId, ?string $projectName = null, ?string $projectType = null): Project
    {
        return DB::transaction(function () use ($quotationId, $projectName, $projectType) {
            $quotation = Quotation::with(['items.celebrity', 'client'])
                ->lockForUpdate()
                ->findOrFail($quotationId);

            if ($quotation->status !== 'approved') {
                throw new DomainException('Only approved quotations can be converted into a project.');
            }

            if ($quotation->project()->exists()) {
                throw new DomainException('This quotation has already been converted into a project.');
            }

            $project = Project::create([
                'project_code' => $this->codeGenerator->projectCode(),
                'quotation_id' => $quotation->id,
                'client_id' => $quotation->client_id,
                'project_name' => $projectName ?: ('Campaign - ' . $quotation->quotation_no),
                'project_type' => $projectType ?: 'campaign',
                'project_manager_id' => auth()->id(),
                'description' => 'Converted from Quotation #' . $quotation->quotation_no . ($quotation->notes ? "\n\n" . $quotation->notes : ''),
                'start_date' => $quotation->quotation_date,
                'end_date' => $quotation->valid_until,
                'budget_amount' => $quotation->grand_total,
                'currency' => $quotation->currency,
                'status' => 'planned',
                'created_by' => auth()->id() ?? $quotation->created_by,
            ]);

            // Create initial milestones and tasks based on quotation items
            ProjectMilestone::create([
                'project_id' => $project->id,
                'milestone_name' => 'Project Kickoff & Briefing',
                'due_date' => $project->start_date,
                'status' => 'pending',
            ]);

            ProjectMilestone::create([
                'project_id' => $project->id,
                'milestone_name' => 'Execution & Delivery',
                'due_date' => $project->end_date,
                'status' => 'pending',
            ]);

            foreach ($quotation->items as $item) {
                ProjectTask::create([
                    'project_id' => $project->id,
                    'task_title' => $item->service_name . ($item->celebrity ? ' (' . $item->celebrity->stage_name . ')' : ''),
                    'description' => $item->description,
                    'assigned_to' => null,
                    'priority' => 'high',
                    'status' => 'open',
                    'start_date' => $project->start_date,
                    'due_date' => $project->end_date,
                ]);
            }

            $quotation->update([
                'status' => 'converted',
                'converted_at' => now(),
            ]);

            ActivityLog::log('quotation_converted', "Quotation {$quotation->quotation_no} converted into Project {$project->project_code}", $project);

            return $project;
        });
    }
}

