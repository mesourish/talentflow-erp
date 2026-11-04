<?php

/**
 * TalentFlow ERP - Celebrity & Talent Management System
 *
 * @package    App\Http\Controllers\Reports
 * @author     Sourish Mondal
 * @version    1.0.0
 * @since      Laravel 12.x / PHP 8.2+
 * @copyright  2026 TalentFlow ERP. All rights reserved.
 * @license    Proprietary / Enterprise Commercial
 */

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Celebrity;
use App\Models\CelebrityPayout;
use App\Models\Claim;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\Quotation;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class ReportController
 *
 * Generates analytical reports for financial ledgers, project margins, celebrity demand, and forecasts.
 *
 * @package App\Http\Controllers\Reports
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class ReportController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService
    ) {}

    public function finance(): View
    {
        $totalInvoiced = (float) Invoice::where('status', '!=', 'cancelled')->sum('grand_total');
        $totalReceived = (float) Payment::sum('amount');
        $totalExpenses = (float) ProjectExpense::sum('amount');
        $totalCommissions = (float) CelebrityPayout::sum('commission_amount');
        $netAgencyIncome = $totalReceived - $totalExpenses;

        $monthlyFinancials = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthName = $month->format('M Y');

            $invoiced = (float) Invoice::whereYear('invoice_date', $month->year)
                ->whereMonth('invoice_date', $month->month)
                ->sum('grand_total');

            $received = (float) Payment::whereYear('payment_date', $month->year)
                ->whereMonth('payment_date', $month->month)
                ->sum('amount');

            $expenses = (float) ProjectExpense::whereYear('expense_date', $month->year)
                ->whereMonth('expense_date', $month->month)
                ->sum('amount');

            $monthlyFinancials[] = [
                'month' => $monthName,
                'invoiced' => $invoiced,
                'received' => $received,
                'expenses' => $expenses,
                'net' => $received - $expenses,
            ];
        }

        return view('reports.finance', compact(
            'totalInvoiced',
            'totalReceived',
            'totalExpenses',
            'totalCommissions',
            'netAgencyIncome',
            'monthlyFinancials'
        ));
    }

    public function projects(): View
    {
        $projectProfitability = $this->analyticsService->getProjectProfitabilityReport();
        return view('reports.projects', compact('projectProfitability'));
    }

    public function celebrities(): View
    {
        $demandMetrics = $this->analyticsService->getCelebrityDemandMetrics();
        return view('reports.celebrities', compact('demandMetrics'));
    }

    public function quotations(): View
    {
        $total = Quotation::count();
        $converted = Quotation::where('status', 'converted')->count();
        $approved = Quotation::where('status', 'approved')->count();
        $sent = Quotation::where('status', 'sent')->count();
        $rejected = Quotation::where('status', 'rejected')->count();
        $draft = Quotation::where('status', 'draft')->count();

        $conversionRate = $total > 0 ? round(($converted / $total) * 100, 1) : 0;
        $totalQuoteValue = (float) Quotation::sum('grand_total');
        $convertedValue = (float) Quotation::where('status', 'converted')->sum('grand_total');

        return view('reports.quotations', compact(
            'total',
            'converted',
            'approved',
            'sent',
            'rejected',
            'draft',
            'conversionRate',
            'totalQuoteValue',
            'convertedValue'
        ));
    }

    public function claims(): View
    {
        $totalClaims = Claim::count();
        $approvedCount = Claim::where('status', 'approved')->count();
        $totalClaimAmount = (float) Claim::sum('total_amount');
        $approvedAmount = (float) Claim::where('status', 'approved')->sum('total_amount');

        $byCategory = \App\Models\ClaimItem::selectRaw('expense_category, sum(amount) as total, count(*) as count')
            ->groupBy('expense_category')
            ->get();

        return view('reports.claims', compact(
            'totalClaims',
            'approvedCount',
            'totalClaimAmount',
            'approvedAmount',
            'byCategory'
        ));
    }

    public function forecast(): View
    {
        $forecast = $this->analyticsService->getRevenueForecast();
        $celebrityDemand = $this->analyticsService->getCelebrityDemandMetrics();
        $projectProfitability = $this->analyticsService->getProjectProfitabilityReport();

        return view('reports.forecast', compact('forecast', 'celebrityDemand', 'projectProfitability'));
    }
}

