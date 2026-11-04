<?php

/**
 * TalentFlow ERP - Celebrity & Talent Management System
 *
 * @package    App\Http\Controllers
 * @author     Sourish Mondal
 * @version    1.0.0
 * @since      Laravel 12.x / PHP 8.2+
 * @copyright  2026 TalentFlow ERP. All rights reserved.
 * @license    Proprietary / Enterprise Commercial
 */

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Quotation;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class DashboardController
 *
 * Handles executive dashboard KPI aggregation, revenue forecasting charts, demand metrics, and quick actions.
 *
 * @package App\Http\Controllers
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class DashboardController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService
    ) {}

    public function index(): View
    {
        $kpis = $this->analyticsService->getDashboardKPIs();
        $forecast = $this->analyticsService->getRevenueForecast();
        $celebrityDemand = array_slice($this->analyticsService->getCelebrityDemandMetrics(), 0, 6);

        $recentQuotations = Quotation::with('client')
            ->latest()
            ->take(6)
            ->get();

        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->take(8)
            ->get();

        $activeProjects = Project::with(['client', 'projectManager'])
            ->where('status', 'active')
            ->take(5)
            ->get();

        // Monthly revenue & expense chart data for the last 6 months
        $chartLabels = [];
        $chartRevenue = [];
        $chartExpenses = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartLabels[] = $month->format('M Y');

            $rev = Payment::whereYear('payment_date', $month->year)
                ->whereMonth('payment_date', $month->month)
                ->sum('amount');
            $chartRevenue[] = (float) $rev;

            $exp = \App\Models\ProjectExpense::whereYear('expense_date', $month->year)
                ->whereMonth('expense_date', $month->month)
                ->sum('amount');
            $chartExpenses[] = (float) $exp;
        }

        return view('dashboard.index', compact(
            'kpis',
            'forecast',
            'celebrityDemand',
            'recentQuotations',
            'recentActivities',
            'activeProjects',
            'chartLabels',
            'chartRevenue',
            'chartExpenses'
        ));
    }
}

