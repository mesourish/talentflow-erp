<?php

namespace App\Console\Commands;

use App\Services\ContractExpiryService;
use Illuminate\Console\Command;

class NotifyExpiringContracts extends Command
{
    protected $signature = 'contracts:notify-expiring';
    protected $description = 'Check contracts expiring in 30 days and 7 days, and notify document managers';

    public function handle(ContractExpiryService $expiryService): int
    {
        $this->info('Checking contracts expiring in 30 days...');
        $expiring30 = $expiryService->checkAndNotifyExpiringContracts(30);
        $this->info(count($expiring30) . ' contracts notified for 30-day expiry.');

        $this->info('Checking contracts expiring in 7 days...');
        $expiring7 = $expiryService->checkAndNotifyExpiringContracts(7);
        $this->info(count($expiring7) . ' contracts notified for 7-day expiry.');

        return Command::SUCCESS;
    }
}

