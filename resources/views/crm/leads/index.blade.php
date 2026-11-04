@extends('layouts.app')

@section('title', 'Sales Leads & Pipeline')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Leads</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Campaign Sales Opportunities & Leads</h4>
            <p class="text-muted small mb-0">Track commercial inquiries from new and existing brands through the conversion funnel</p>
        </div>
        <a href="{{ route('admin.leads.create') }}" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="bi bi-funnel-plus me-1"></i> Add New Lead
        </a>
    </div>

    <!-- Pipeline Stage Summary Counters -->
    <div class="row g-2 mb-4">
        @foreach($stages as $stg)
            @php
                $stgCount = $stageCounts[$stg] ?? 0;
                $activeStg = request('stage') === $stg;
                $pillClass = match($stg) {
                    'won' => 'text-success bg-success-subtle',
                    'lost' => 'text-danger bg-danger-subtle',
                    'negotiation' => 'text-warning bg-warning-subtle',
                    'proposal' => 'text-primary bg-primary-subtle',
                    default => 'text-secondary bg-light'
                };
            @endphp
            <div class="col-6 col-md-2">
                <a href="{{ route('admin.leads.index', ['stage' => $activeStg ? null : $stg]) }}" class="card text-decoration-none border text-center p-2 h-100 {{ $activeStg ? 'border-primary shadow-sm bg-primary-subtle' : 'bg-white' }}">
                    <div class="small fw-semibold text-capitalize text-muted">{{ $stg }}</div>
                    <div class="fs-4 fw-bold text-dark">{{ $stgCount }}</div>
                </a>
            </div>
        @endforeach
    </div>

    <!-- Filters & Search -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.leads.index') }}" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search by opportunity title or code..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="stage" class="form-select">
                        <option value="">All Pipeline Stages</option>
                        @foreach($stages as $stg)
                            <option value="{{ $stg }}" {{ request('stage') === $stg ? 'selected' : '' }}>{{ ucfirst($stg) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                    @if(request()->anyFilled(['search', 'stage']))
                        <a href="{{ route('admin.leads.index') }}" class="btn btn-light border" title="Reset"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Leads Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Lead Opportunity</th>
                            <th>Brand / Client</th>
                            <th>Est. Value</th>
                            <th>Win Prob.</th>
                            <th>Owner</th>
                            <th>Stage</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $lead)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.leads.show', $lead) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $lead->lead_title }}
                                    </a>
                                    <div class="text-muted small">
                                        <code>{{ $lead->lead_code }}</code> &bull; Source: {{ $lead->lead_source }}
                                    </div>
                                </td>
                                <td>
                                    @if($lead->client)
                                        <a href="{{ route('admin.clients.show', $lead->client) }}" class="text-decoration-none text-dark fw-semibold small">
                                            {{ $lead->client->company_name }}
                                        </a>
                                    @else
                                        <span class="text-muted small">Unassigned Brand</span>
                                    @endif
                                </td>
                                <td class="fw-semibold small">
                                    INR {{ number_format($lead->estimated_value, 2) }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px; width: 60px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $lead->probability }}%"></div>
                                        </div>
                                        <span class="small text-muted">{{ $lead->probability }}%</span>
                                    </div>
                                </td>
                                <td class="small">
                                    {{ $lead->assignee ? $lead->assignee->name : 'Unassigned' }}
                                </td>
                                <td>
                                    @php
                                        $badge = match($lead->stage) {
                                            'won' => 'bg-success-subtle text-success',
                                            'negotiation' => 'bg-warning-subtle text-warning',
                                            'proposal' => 'bg-primary-subtle text-primary',
                                            'lost' => 'bg-danger-subtle text-danger',
                                            default => 'bg-secondary-subtle text-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badge }} badge-status text-capitalize">{{ $lead->stage }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.leads.show', $lead) }}" class="btn btn-light border" title="Inspect">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.leads.edit', $lead) }}" class="btn btn-light border" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-funnel fs-1 d-block mb-2 text-secondary"></i>
                                    No sales leads found in this filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($leads->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $leads->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

