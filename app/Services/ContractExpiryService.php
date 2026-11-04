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
use App\Models\Contract;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

/**
 * Class ContractExpiryService
 *
 * Domain service monitoring contract end dates and identifying expiring agreements within 30-day windows.
 *
 * @package App\Services
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class ContractExpiryService
{
    /**
     * Check contracts expiring in $days and notify responsible managers.
     */
    public function checkAndNotifyExpiringContracts(int $days = 30): array
    {
        $targetDate = now()->addDays($days)->toDateString();

        $expiringContracts = Contract::with(['client', 'celebrity'])
            ->where('status', 'active')
            ->whereDate('expiry_date', $targetDate)
            ->get();

        $notified = [];

        foreach ($expiringContracts as $contract) {
            $dedupKey = "contract_expiry_reminder_{$contract->id}_{$days}_" . now()->toDateString();

            if (!Cache::has($dedupKey)) {
                Cache::put($dedupKey, true, now()->addDays(2));

                ActivityLog::log(
                    'contract_expiry_alert',
                    "Contract #{$contract->contract_no} ({$contract->contract_title}) expires in {$days} days on {$contract->expiry_date->format('d M Y')}.",
                    $contract,
                    ['reminder_days' => $days, 'expiry_date' => $contract->expiry_date->toDateString()]
                );

                $notified[] = $contract;
            }
        }

        return $notified;
    }
}

