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

use App\Models\CelebrityPayout;
use App\Models\CommissionRule;
use InvalidArgumentException;

/**
 * Class CommissionCalculator
 *
 * Domain service computing tiered/percentage commissions, TDS withholdings, and net payable talent dues.
 *
 * @package App\Services
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class CommissionCalculator
{
    /**
     * Calculate agency commission and celebrity net payout.
     *
     * @param float $grossAmount Total gross service fee
     * @param int|null $celebrityId
     * @param int|null $clientId
     * @param float $otherDeductions
     * @return array
     */
    public function calculate(
        float $grossAmount,
        ?int $celebrityId = null,
        ?int $clientId = null,
        float $otherDeductions = 0.0
    ): array {
        if ($grossAmount < 0) {
            throw new InvalidArgumentException('Gross amount cannot be negative.');
        }

        // Find best matching active commission rule
        $rule = null;
        if ($celebrityId) {
            $rule = CommissionRule::where('is_active', true)
                ->where('celebrity_id', $celebrityId)
                ->where(function ($q) {
                    $q->whereNull('effective_to')->orWhere('effective_to', '>=', now());
                })
                ->first();
        }

        if (!$rule && $clientId) {
            $rule = CommissionRule::where('is_active', true)
                ->where('client_id', $clientId)
                ->where(function ($q) {
                    $q->whereNull('effective_to')->orWhere('effective_to', '>=', now());
                })
                ->first();
        }

        if (!$rule) {
            $rule = CommissionRule::where('is_active', true)
                ->where('applies_to', 'default')
                ->where(function ($q) {
                    $q->whereNull('effective_to')->orWhere('effective_to', '>=', now());
                })
                ->first();
        }

        $commissionAmount = 0.0;
        $ruleName = $rule ? $rule->name : 'Standard Default (20%)';
        $commissionType = $rule ? $rule->commission_type : 'percentage';
        $commissionValue = $rule ? (float) $rule->commission_value : 20.0;

        if ($commissionType === 'percentage') {
            $commissionAmount = round($grossAmount * ($commissionValue / 100), 2);
        } else {
            $commissionAmount = round(min($grossAmount, $commissionValue), 2);
        }

        $netPayable = round(max(0, $grossAmount - $commissionAmount - $otherDeductions), 2);

        return [
            'gross_amount' => round($grossAmount, 2),
            'commission_amount' => $commissionAmount,
            'commission_rate' => $commissionValue,
            'commission_type' => $commissionType,
            'commission_rule' => $ruleName,
            'other_deductions' => round($otherDeductions, 2),
            'net_payable' => $netPayable,
        ];
    }
}

