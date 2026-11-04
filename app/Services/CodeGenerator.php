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

use App\Models\Booking;
use App\Models\Celebrity;
use App\Models\Claim;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Quotation;

/**
 * Class CodeGenerator
 *
 * Domain service generating standardized sequential enterprise reference codes (PRJ, QTN, BKG, INV, etc.).
 *
 * @package App\Services
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class CodeGenerator
{
    public function clientCode(): string
    {
        $count = Client::count() + 1;
        return sprintf('CLT-%04d', $count);
    }

    public function leadCode(): string
    {
        $count = Lead::count() + 1;
        return sprintf('LED-%04d', $count);
    }

    public function talentCode(): string
    {
        $count = Celebrity::count() + 1;
        return sprintf('TLT-%04d', $count);
    }

    public function quotationNumber(): string
    {
        $year = date('Y');
        $count = Quotation::whereYear('created_at', $year)->count() + 1;
        return sprintf('QT-%s-%04d', $year, $count);
    }

    public function projectCode(): string
    {
        $year = date('Y');
        $count = Project::whereYear('created_at', $year)->count() + 1;
        return sprintf('PRJ-%s-%04d', $year, $count);
    }

    public function bookingCode(): string
    {
        $year = date('Y');
        $count = Booking::whereYear('created_at', $year)->count() + 1;
        return sprintf('BKG-%s-%04d', $year, $count);
    }

    public function contractNumber(): string
    {
        $year = date('Y');
        $count = Contract::whereYear('created_at', $year)->count() + 1;
        return sprintf('CNT-%s-%04d', $year, $count);
    }

    public function claimNumber(): string
    {
        $year = date('Y');
        $count = Claim::whereYear('created_at', $year)->count() + 1;
        return sprintf('CLM-%s-%04d', $year, $count);
    }

    public function invoiceNumber(): string
    {
        $year = date('Y');
        $count = Invoice::whereYear('created_at', $year)->count() + 1;
        return sprintf('INV-%s-%04d', $year, $count);
    }
}

