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
use App\Models\BookingCelebrity;
use App\Models\Celebrity;
use App\Models\CelebrityPayout;
use App\Models\Claim;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\Quotation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class AnalyticsService
 *
 * Domain service calculating executive KPIs, margin analytics, pipeline forecasts, and artist demand.
 *
 * @package App\Services
 * @author  Sourish Mondal
 * @version 1.0.0
 */
class AnalyticsService
{
    /**
     * Get primary dashboard KPI metrics.
     */
    public function getDashboardKPIs(): array
    {
        $totalRevenue = (float) Payment::sum('amount');
        $totalInvoiced = (float) Invoice::where('status', '!=', 'cancelled')->sum('grand_total');
        $totalExpenses = (float) ProjectExpense::sum('amount');
        $netProfit = $totalRevenue - $totalExpenses;

        $totalQuotes = Quotation::count();
        $convertedQuotes = Quotation::whereIn('status', ['converted', 'approved'])->count();
        $conversionRate = $totalQuotes > 0 ? round(($convertedQuotes / $totalQuotes) * 100, 1) : 0;

        $pendingQuotations = Quotation::whereIn('status', ['draft', 'sent', 'under_negotiation'])->count();
        $activeProjects = Project::where('status', 'active')->count();
        $outstandingPayments = (float) Invoice::whereIn('status', ['issued', 'unpaid', 'partially_paid', 'overdue'])
            ->selectRaw('SUM(grand_total - amount_paid) as outstanding')
            ->value('outstanding') ?? 0.0;

        $upcomingBookings = Booking::with(['project', 'celebrities'])
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->take(5)
            ->get();

        $expiringContracts = Contract::with(['client', 'celebrity'])
            ->where('status', 'active')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(30))
            ->where('expiry_date', '>=', now())
            ->orderBy('expiry_date')
            ->take(5)
            ->get();

        return [
            'total_revenue' => $totalRevenue,
            'total_invoiced' => $totalInvoiced,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'conversion_rate' => $conversionRate,
            'pending_quotations' => $pendingQuotations,
            'active_projects' => $activeProjects,
            'outstanding_payments' => round($outstandingPayments, 2),
            'upcoming_bookings' => $upcomingBookings,
            'expiring_contracts' => $expiringContracts,
        ];
    }

    /**
     * Calculate simple 3-month rolling revenue baseline forecast:
     * Forecast = (R1 + R2 + R3) / 3 (Blueprint 14.4)
     */
    public function getRevenueForecast(): array
    {
        $months = [];
        $values = [];

        for ($i = 3; $i >= 1; $i--) {
            $monthDate = now()->subMonths($i);
            $monthName = $monthDate->format('M Y');
            $revenue = (float) Payment::whereYear('payment_date', $monthDate->year)
                ->whereMonth('payment_date', $monthDate->month)
                ->sum('amount');

            $months[] = $monthName;
            $values[] = $revenue;
        }

        $sum = array_sum($values);
        $forecastNextMonth = count($values) > 0 ? round($sum / count($values), 2) : 0;

        return [
            'months' => $months,
            'historical_revenue' => $values,
            'next_month_name' => now()->addMonth()->format('M Y'),
            'forecast_estimate' => $forecastNextMonth,
            'methodology' => '3-Month Historical Average Baseline: (R1 + R2 + R3) / 3',
        ];
    }

    /**
     * Get celebrity demand & performance indicators (Blueprint 14.3).
     */
    public function getCelebrityDemandMetrics(): array
    {
        $celebrities = Celebrity::withCount(['bookings', 'payouts'])
            ->withSum('payouts', 'net_payable')
            ->get()
            ->map(function ($celeb) {
                $bookingCount = $celeb->bookings_count;
                if ($bookingCount >= 5) {
                    $demand = 'High';
                    $badgeClass = 'bg-success';
                } elseif ($bookingCount >= 2) {
                    $demand = 'Stable';
                    $badgeClass = 'bg-primary';
                } else {
                    $demand = 'Low';
                    $badgeClass = 'bg-warning text-dark';
                }

                return [
                    'id' => $celeb->id,
                    'name' => $celeb->stage_name,
                    'code' => $celeb->talent_code,
                    'bookings_count' => $bookingCount,
                    'total_earnings' => (float) ($celeb->payouts_sum_net_payable ?? 0),
                    'demand' => $demand,
                    'badge' => $badgeClass,
                ];
            });

        return $celebrities->toArray();
    }

    /**
     * Project Profitability Analysis (Blueprint 14.2).
     */
    public function getProjectProfitabilityReport(): array
    {
        return Project::with(['client'])
            ->get()
            ->map(function ($project) {
                $invoiced = (float) Invoice::where('project_id', $project->id)->where('status', '!=', 'cancelled')->sum('grand_total');
                $received = (float) Payment::whereHas('invoice', fn($q) => $q->where('project_id', $project->id))->sum('amount');
                $expenses = (float) ProjectExpense::where('project_id', $project->id)->sum('amount');
                $payouts = (float) CelebrityPayout::where('project_id', $project->id)->sum('net_payable');
                $totalCosts = $expenses + $payouts;
                $profit = $invoiced - $totalCosts;
                $marginPercent = $invoiced > 0 ? round(($profit / $invoiced) * 100, 1) : 0;

                $status = ($marginPercent < 15 || $profit < 0) ? 'Margin Warning' : 'Healthy';
                $badge = ($status === 'Healthy') ? 'bg-success' : 'bg-danger';

                return [
                    'id' => $project->id,
                    'code' => $project->project_code,
                    'name' => $project->project_name,
                    'client' => $project->client ? $project->client->company_name : 'N/A',
                    'budget' => (float) $project->budget_amount,
                    'invoiced' => $invoiced,
                    'received' => $received,
                    'expenses' => $expenses,
                    'payouts' => $payouts,
                    'total_costs' => $totalCosts,
                    'profit' => $profit,
                    'margin_percent' => $marginPercent,
                    'health' => $status,
                    'badge' => $badge,
                ];
            })
            ->toArray();
    }
}

