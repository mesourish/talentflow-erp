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
use App\Models\Claim;
use App\Models\ClaimApproval;
use App\Models\ProjectExpense;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Class ClaimApprovalService
 *
 * Domain service enforcing double-posting protection and transferring approved claims into project expenses.
 *
 * @package App\Services
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class ClaimApprovalService
{
    /**
     * Approve claim and automatically post to project expenses.
     */
    public function approve(int $claimId, int $userId, ?string $comments = null): Claim
    {
        return DB::transaction(function () use ($claimId, $userId, $comments) {
            $claim = Claim::lockForUpdate()->findOrFail($claimId);

            if ($claim->status === 'approved') {
                throw new DomainException('This claim has already been approved.');
            }

            // Check if expense already posted
            if (ProjectExpense::where('source_claim_id', $claim->id)->exists()) {
                throw new DomainException('An expense has already been posted for this claim.');
            }

            // Record approval action
            ClaimApproval::create([
                'claim_id' => $claim->id,
                'user_id' => $userId,
                'action' => 'approved',
                'comments' => $comments,
                'action_at' => now(),
            ]);

            // Create linked project expense
            ProjectExpense::create([
                'project_id' => $claim->project_id,
                'expense_category' => 'Claim Reimbursement',
                'vendor_name' => $claim->submitter ? $claim->submitter->name : 'Staff Member',
                'expense_date' => $claim->claim_date,
                'description' => "Approved claim #{$claim->claim_no}: " . ($claim->description ?? 'Project Expense Reimbursement'),
                'amount' => $claim->total_amount,
                'currency' => $claim->currency,
                'reference_no' => $claim->claim_no,
                'created_by' => $userId,
                'source_claim_id' => $claim->id,
            ]);

            $claim->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            ActivityLog::log('claim_approved', "Claim #{$claim->claim_no} approved and posted to Project expenses", $claim);

            return $claim;
        });
    }

    /**
     * Reject claim.
     */
    public function reject(int $claimId, int $userId, ?string $comments = null): Claim
    {
        return DB::transaction(function () use ($claimId, $userId, $comments) {
            $claim = Claim::lockForUpdate()->findOrFail($claimId);

            if ($claim->status === 'approved') {
                throw new DomainException('Cannot reject an already approved claim.');
            }

            ClaimApproval::create([
                'claim_id' => $claim->id,
                'user_id' => $userId,
                'action' => 'rejected',
                'comments' => $comments,
                'action_at' => now(),
            ]);

            $claim->update([
                'status' => 'rejected',
            ]);

            ActivityLog::log('claim_rejected', "Claim #{$claim->claim_no} rejected", $claim);

            return $claim;
        });
    }
}

