@extends('layouts.app')

@section('title', 'Executive Dashboard')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <!-- Header with Quick Action Buttons -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Executive Overview & Analytics</h4>
            <p class="text-muted small mb-0">Hybrid Celebrity, Talent, Event & Brand Campaign Performance Dashboard</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.quotations.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> New Quotation
            </a>
            <a href="{{ route('admin.bookings.create') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="bi bi-calendar-plus me-1"></i> Schedule Booking
            </a>
            <a href="{{ route('admin.reports.forecast') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-graph-up me-1"></i> Predictive Forecast
            </a>
        </div>
    </div>

    <!-- Forecast Alert Banner (Blueprint Section 14.3 & 14.4) -->
    <div class="card bg-primary-subtle border-primary-subtle mb-4">
        <div class="card-body py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center">
                <div class="bg-primary text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="bi bi-lightning-charge-fill fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-primary-emphasis mb-0">Estimated Next-Month Revenue: INR {{ number_format($forecast['forecast_estimate'], 2) }}</h6>
                    <small class="text-muted">{{ $forecast['methodology'] }} &bull; Target Period: <strong>{{ $forecast['next_month_name'] }}</strong></small>
                </div>
            </div>
            <a href="{{ route('admin.reports.forecast') }}" class="btn btn-primary btn-sm rounded-pill px-3 text-nowrap">
                Inspect Forecast Model &rarr;
            </a>
        </div>
    </div>

    <!-- Primary 8 KPI Cards (Blueprint Section 14.1) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Total Revenue</span>
                    <div class="kpi-icon bg-success-subtle text-success"><i class="bi bi-cash-coin"></i></div>
                </div>
                <h4 class="fw-bold mb-1 text-dark">INR {{ number_format($kpis['total_revenue'] / 100000, 2) }}L</h4>
                <div class="small text-muted"><i class="bi bi-check-circle-fill text-success me-1"></i> Reconciled collections</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Active Projects</span>
                    <div class="kpi-icon bg-primary-subtle text-primary"><i class="bi bi-kanban"></i></div>
                </div>
                <h4 class="fw-bold mb-1 text-dark">{{ $kpis['active_projects'] }}</h4>
                <div class="small text-muted"><i class="bi bi-clock-history me-1"></i> In execution</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Net Project Profit</span>
                    <div class="kpi-icon bg-info-subtle text-info"><i class="bi bi-wallet2"></i></div>
                </div>
                <h4 class="fw-bold mb-1 {{ $kpis['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                    INR {{ number_format($kpis['net_profit'] / 100000, 2) }}L
                </h4>
                <div class="small text-muted"><i class="bi bi-graph-up me-1"></i> Revenue minus costs</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Outstanding Receivables</span>
                    <div class="kpi-icon bg-warning-subtle text-warning"><i class="bi bi-hourglass-split"></i></div>
                </div>
                <h4 class="fw-bold mb-1 text-dark">INR {{ number_format($kpis['outstanding_payments'] / 100000, 2) }}L</h4>
                <div class="small text-muted"><i class="bi bi-exclamation-circle text-warning me-1"></i> Pending client payouts</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Quote Conversion</span>
                    <div class="kpi-icon bg-secondary-subtle text-secondary"><i class="bi bi-percent"></i></div>
                </div>
                <h4 class="fw-bold mb-1 text-dark">{{ $kpis['conversion_rate'] }}%</h4>
                <div class="small text-muted"><i class="bi bi-arrow-up-right text-success me-1"></i> Won proposals</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Pending Quotes</span>
                    <div class="kpi-icon bg-purple-subtle text-primary"><i class="bi bi-file-earmark-text"></i></div>
                </div>
                <h4 class="fw-bold mb-1 text-dark">{{ $kpis['pending_quotations'] }}</h4>
                <div class="small text-muted"><i class="bi bi-bell text-primary me-1"></i> Under negotiation</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Total Costs & Claims</span>
                    <div class="kpi-icon bg-danger-subtle text-danger"><i class="bi bi-receipt"></i></div>
                </div>
                <h4 class="fw-bold mb-1 text-dark">INR {{ number_format($kpis['total_expenses'] / 100000, 2) }}L</h4>
                <div class="small text-muted"><i class="bi bi-shield-check me-1"></i> Verified expenses</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Expiring Contracts</span>
                    <div class="kpi-icon bg-danger-subtle text-danger"><i class="bi bi-shield-exclamation"></i></div>
                </div>
                <h4 class="fw-bold mb-1 text-danger">{{ $kpis['expiring_contracts']->count() }}</h4>
                <div class="small text-muted"><i class="bi bi-alarm me-1"></i> Within 30 days</div>
            </div>
        </div>
    </div>

    <!-- Charts and Demand Row -->
    <div class="row g-4 mb-4">
        <!-- Revenue vs Expenses 6-Month Chart -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-bar-chart-line text-primary me-2"></i> Cash Collections vs. Operational Expenses</h6>
                        <small class="text-muted">Trailing 6-Month Realized Financials (INR)</small>
                    </div>
                    <span class="badge bg-light text-secondary border">Audited Cashflow</span>
                </div>
                <div class="card-body">
                    <canvas id="revenueExpensesChart" height="260"></canvas>
                </div>
            </div>
        </div>

        <!-- Celebrity Demand Roster -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-fire text-danger me-2"></i> Celebrity Demand Status</h6>
                        <small class="text-muted">Booking inquiry & selection velocity</small>
                    </div>
                    <a href="{{ route('admin.reports.celebrities') }}" class="btn btn-link btn-sm p-0 text-decoration-none">View All</a>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($celebrityDemand as $cd)
                            <li class="list-group-item d-flex align-items-center justify-content-between py-2 px-3">
                                <div>
                                    <a href="{{ route('admin.celebrities.show', $cd['id']) }}" class="text-decoration-none fw-semibold text-dark small">
                                        {{ $cd['name'] }}
                                    </a>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        {{ $cd['bookings_count'] }} bookings &bull; INR {{ number_format($cd['total_earnings']) }}
                                    </div>
                                </div>
                                <span class="badge {{ $cd['badge'] }} rounded-pill small">{{ $cd['demand'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Operational Tables Row: Upcoming Bookings & Recent Quotations -->
    <div class="row g-4 mb-4">
        <!-- Upcoming Bookings Preview -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar2-week text-primary me-2"></i> Upcoming Bookings & Shoots</h6>
                        <small class="text-muted">Live calendar dispatch preview</small>
                    </div>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Open Calendar</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Event / Project</th>
                                    <th>Talent</th>
                                    <th>Date & Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kpis['upcoming_bookings'] as $booking)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.bookings.show', $booking) }}" class="text-decoration-none fw-semibold text-dark small d-block">
                                                {{ $booking->title }}
                                            </a>
                                            <small class="text-muted">{{ $booking->venue_name ?? 'Location TBA' }}</small>
                                        </td>
                                        <td class="small">
                                            @foreach($booking->celebrities as $c)
                                                <span class="badge bg-light text-dark border">{{ $c->stage_name }}</span>
                                            @endforeach
                                        </td>
                                        <td class="small">
                                            <div>{{ $booking->starts_at->format('d M Y') }}</div>
                                            <small class="text-muted">{{ $booking->starts_at->format('h:i A') }}</small>
                                        </td>
                                        <td>
                                            @if($booking->status === 'confirmed')
                                                <span class="badge bg-success-subtle text-success badge-status">Confirmed</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning badge-status">Tentative</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">No upcoming bookings scheduled.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Quotations with Quick Actions -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-spreadsheet text-primary me-2"></i> Quotation Pipeline</h6>
                        <small class="text-muted">Latest commercial proposals</small>
                    </div>
                    <a href="{{ route('admin.quotations.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">All Quotations</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Quotation No</th>
                                    <th>Client</th>
                                    <th>Grand Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentQuotations as $quote)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.quotations.show', $quote) }}" class="text-decoration-none fw-bold text-dark small">
                                                {{ $quote->quotation_no }}
                                            </a>
                                            <div class="text-muted" style="font-size: 0.72rem;">v{{ $quote->version_no }} &bull; {{ $quote->quotation_date->format('d M Y') }}</div>
                                        </td>
                                        <td class="small text-truncate" style="max-width: 140px;">{{ $quote->client->company_name }}</td>
                                        <td class="small fw-semibold">INR {{ number_format($quote->grand_total, 2) }}</td>
                                        <td>
                                            @php
                                                $qBadge = match($quote->status) {
                                                    'approved' => 'bg-success-subtle text-success',
                                                    'converted' => 'bg-primary-subtle text-primary',
                                                    'sent' => 'bg-info-subtle text-info',
                                                    'rejected' => 'bg-danger-subtle text-danger',
                                                    default => 'bg-secondary-subtle text-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $qBadge }} badge-status">{{ ucfirst($quote->status) }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.quotations.show', $quote) }}" class="btn btn-light btn-sm p-1 px-2 border" title="Inspect">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Projects & Audit Trails -->
    <div class="row g-4">
        <!-- Active Projects Spotlight -->
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-briefcase text-primary me-2"></i> Active Campaign Deliveries</h6>
                    <a href="{{ route('admin.projects.index') }}" class="btn btn-link btn-sm p-0 text-decoration-none">View All Projects</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Project</th>
                                    <th>Client</th>
                                    <th>Budget</th>
                                    <th>Timeline</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeProjects as $proj)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.projects.show', $proj) }}" class="text-decoration-none fw-semibold text-dark small">
                                                {{ $proj->project_name }}
                                            </a>
                                            <div class="text-muted" style="font-size: 0.72rem;">{{ $proj->project_code }} &bull; {{ ucfirst($proj->project_type) }}</div>
                                        </td>
                                        <td class="small">{{ $proj->client->company_name }}</td>
                                        <td class="small fw-semibold">INR {{ number_format($proj->budget_amount, 2) }}</td>
                                        <td class="small text-muted">{{ $proj->start_date->format('M d') }} - {{ $proj->end_date->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Audit Activity Trail -->
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-activity text-primary me-2"></i> Real-time Enterprise Audit Log</h6>
                </div>
                <div class="card-body p-0" style="max-height: 290px; overflow-y: auto;">
                    <ul class="list-group list-group-flush">
                        @foreach($recentActivities as $act)
                            <li class="list-group-item py-2 px-3 small">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-dark">{{ $act->user ? $act->user->name : 'System' }}</strong>
                                    <small class="text-muted">{{ $act->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="text-muted mt-1">{{ $act->description }}</div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('revenueExpensesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [
                {
                    label: 'Cash Collections (Payments)',
                    data: {!! json_encode($chartRevenue) !!},
                    backgroundColor: 'rgba(37, 99, 235, 0.85)',
                    borderColor: '#2563eb',
                    borderRadius: 6,
                },
                {
                    label: 'Project Expenses & Claims',
                    data: {!! json_encode($chartExpenses) !!},
                    backgroundColor: 'rgba(239, 68, 68, 0.85)',
                    borderColor: '#ef4444',
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': INR ' + Number(context.raw).toLocaleString('en-IN');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + (value / 1000).toFixed(0) + 'k';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush

