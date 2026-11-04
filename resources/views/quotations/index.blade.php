@extends('layouts.app')

@section('title', 'Quotations Management')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Quotations</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Quotations & Commercial Proposals</h4>
            <p class="text-muted small mb-0">Dynamic multi-talent quoting, automatic tax calculation, revisions, approval audit trails and 1-click project conversion</p>
        </div>
        <a href="{{ route('admin.quotations.create') }}" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="bi bi-file-earmark-plus me-1"></i> Create Quotation
        </a>
    </div>

    <!-- Filters & Search -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.quotations.index') }}" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search quotation number or client..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                    @if(request()->anyFilled(['search', 'status']))
                        <a href="{{ route('admin.quotations.index') }}" class="btn btn-light border" title="Reset"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Quotations Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Quotation No</th>
                            <th>Client Account</th>
                            <th>Quote Date</th>
                            <th>Valid Until</th>
                            <th>Subtotal</th>
                            <th>Grand Total (Tax Incl.)</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotations as $quote)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.quotations.show', $quote) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $quote->quotation_no }}
                                    </a>
                                    <div class="text-muted small">v{{ $quote->version_no }} &bull; By: {{ $quote->creator->name }}</div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.clients.show', $quote->client) }}" class="text-decoration-none text-dark fw-semibold small">
                                        {{ $quote->client->company_name }}
                                    </a>
                                    <div class="text-muted" style="font-size: 0.72rem;">{{ $quote->client->client_code }}</div>
                                </td>
                                <td class="small">{{ $quote->quotation_date->format('d M Y') }}</td>
                                <td class="small text-muted">{{ $quote->valid_until->format('d M Y') }}</td>
                                <td class="small">INR {{ number_format($quote->subtotal, 2) }}</td>
                                <td class="fw-bold text-dark small">
                                    INR {{ number_format($quote->grand_total, 2) }}
                                </td>
                                <td>
                                    @php
                                        $badge = match($quote->status) {
                                            'approved' => 'bg-success-subtle text-success',
                                            'converted' => 'bg-primary-subtle text-primary',
                                            'sent' => 'bg-info-subtle text-info',
                                            'under_negotiation' => 'bg-warning-subtle text-warning',
                                            'rejected' => 'bg-danger-subtle text-danger',
                                            default => 'bg-secondary-subtle text-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badge }} badge-status">{{ ucfirst(str_replace('_', ' ', $quote->status)) }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.quotations.show', $quote) }}" class="btn btn-light border" title="Inspect">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.quotations.pdf', $quote) }}" target="_blank" class="btn btn-light border" title="Download PDF">
                                            <i class="bi bi-file-earmark-pdf text-danger"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-file-earmark-spreadsheet fs-1 d-block mb-2 text-secondary"></i>
                                    No quotations found matching filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($quotations->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $quotations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

