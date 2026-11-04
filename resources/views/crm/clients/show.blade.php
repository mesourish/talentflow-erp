@extends('layouts.app')

@section('title', $client->company_name)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}">Clients</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $client->company_name }}</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <!-- Header Banner -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center">
                <div class="bg-primary text-white rounded-3 p-3 me-3 d-flex align-items-center justify-content-center" style="width: 58px; height: 58px;">
                    <i class="bi bi-building fs-3"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h4 class="fw-bold mb-0 text-dark">{{ $client->company_name }}</h4>
                        <span class="badge bg-secondary-subtle text-secondary">{{ ucfirst($client->client_type) }}</span>
                        @php
                            $badge = match($client->status) {
                                'active' => 'bg-success-subtle text-success',
                                'prospect' => 'bg-info-subtle text-info',
                                default => 'bg-secondary-subtle text-secondary',
                            };
                        @endphp
                        <span class="badge {{ $badge }} badge-status">{{ ucfirst($client->status) }}</span>
                    </div>
                    <div class="text-muted small">
                        <code>{{ $client->client_code }}</code> &bull; {{ $client->industry ?? 'General Enterprise' }} &bull; Net {{ $client->payment_terms_days }} Days
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.quotations.create') }}?client_id={{ $client->id }}" class="btn btn-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-plus-lg me-1"></i> New Quotation
                </a>
                <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-pencil me-1"></i> Edit Client
                </a>
            </div>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="row g-4">
        <!-- Left: Profile & Contacts -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-info-circle text-primary me-2"></i> Company Overview</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2"><span class="text-muted d-block">Tax/GSTIN ID:</span> <strong>{{ $client->tax_number ?? 'Not Specified' }}</strong></li>
                        <li class="mb-2"><span class="text-muted d-block">Official Email:</span> <strong>{{ $client->email ?? 'N/A' }}</strong></li>
                        <li class="mb-2"><span class="text-muted d-block">Phone:</span> <strong>{{ $client->phone ?? 'N/A' }}</strong></li>
                        <li class="mb-2"><span class="text-muted d-block">Website:</span> 
                            @if($client->website)
                                <a href="{{ $client->website }}" target="_blank" class="text-decoration-none">{{ $client->website }}</a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </li>
                        <li class="mb-2"><span class="text-muted d-block">Billing Address:</span> {{ $client->billing_address ?? 'N/A' }}</li>
                        @if($client->notes)
                            <li class="pt-2 border-top"><span class="text-muted d-block">Internal Account Notes:</span> <em>{{ $client->notes }}</em></li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Contacts Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-people text-primary me-2"></i> Client Contacts</h6>
                    <span class="badge bg-light text-secondary border">{{ $client->contacts->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($client->contacts as $contact)
                            <li class="list-group-item py-2 px-3 small">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-dark">{{ $contact->name }}</strong>
                                    @if($contact->is_primary)
                                        <span class="badge bg-primary-subtle text-primary" style="font-size: 0.65rem;">Primary</span>
                                    @endif
                                </div>
                                <div class="text-muted">{{ $contact->designation ?? 'Representative' }}</div>
                                <div class="text-muted mt-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-envelope me-1"></i> {{ $contact->email ?? 'N/A' }} &bull;
                                    <i class="bi bi-telephone me-1"></i> {{ $contact->phone ?? 'N/A' }}
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item py-3 text-center text-muted small">No contacts registered.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Right: Linked Quotations, Projects, Invoices -->
        <div class="col-lg-8">
            <!-- Linked Projects -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-kanban text-primary me-2"></i> Active Campaigns & Projects</h6>
                    <span class="badge bg-light text-secondary border">{{ $client->projects->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead>
                                <tr>
                                    <th>Project Name</th>
                                    <th>Type</th>
                                    <th>Budget</th>
                                    <th>Timeline</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($client->projects as $p)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.projects.show', $p) }}" class="fw-semibold text-dark text-decoration-none">
                                                {{ $p->project_name }}
                                            </a>
                                            <div class="text-muted" style="font-size: 0.72rem;">{{ $p->project_code }}</div>
                                        </td>
                                        <td>{{ ucfirst($p->project_type) }}</td>
                                        <td class="fw-semibold">INR {{ number_format($p->budget_amount, 2) }}</td>
                                        <td class="text-muted">{{ $p->start_date->format('d M') }} - {{ $p->end_date->format('d M Y') }}</td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary badge-status">{{ ucfirst($p->status) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">No projects created yet for this client.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Linked Quotations -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-spreadsheet text-primary me-2"></i> Quotations History</h6>
                    <span class="badge bg-light text-secondary border">{{ $client->quotations->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead>
                                <tr>
                                    <th>Quote No</th>
                                    <th>Date</th>
                                    <th>Grand Total</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($client->quotations as $q)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.quotations.show', $q) }}" class="fw-bold text-dark text-decoration-none">
                                                {{ $q->quotation_no }}
                                            </a>
                                            <span class="text-muted">v{{ $q->version_no }}</span>
                                        </td>
                                        <td>{{ $q->quotation_date->format('d M Y') }}</td>
                                        <td class="fw-semibold">INR {{ number_format($q->grand_total, 2) }}</td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary badge-status">{{ ucfirst($q->status) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.quotations.show', $q) }}" class="btn btn-light btn-sm p-1 px-2 border"><i class="bi bi-eye"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">No quotations generated yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Linked Invoices -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-receipt-cutoff text-primary me-2"></i> Billing & Invoices</h6>
                    <span class="badge bg-light text-secondary border">{{ $client->invoices->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead>
                                <tr>
                                    <th>Invoice No</th>
                                    <th>Date</th>
                                    <th>Grand Total</th>
                                    <th>Paid</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($client->invoices as $inv)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.invoices.show', $inv) }}" class="fw-bold text-dark text-decoration-none">
                                                {{ $inv->invoice_no }}
                                            </a>
                                        </td>
                                        <td>{{ $inv->invoice_date->format('d M Y') }}</td>
                                        <td class="fw-semibold">INR {{ number_format($inv->grand_total, 2) }}</td>
                                        <td class="text-success fw-semibold">INR {{ number_format($inv->amount_paid, 2) }}</td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary badge-status">{{ ucfirst($inv->status) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">No invoices generated yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

