@extends('layouts.app')

@section('title', 'Quotation ' . $quotation->quotation_no)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.quotations.index') }}">Quotations</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $quotation->quotation_no }}</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <!-- Top Action Banner -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h4 class="fw-bold mb-0 text-dark">Quotation #{{ $quotation->quotation_no }}</h4>
                    <span class="badge bg-secondary-subtle text-secondary">Version {{ $quotation->version_no }}</span>
                    @php
                        $badge = match($quotation->status) {
                            'approved' => 'bg-success-subtle text-success',
                            'converted' => 'bg-primary-subtle text-primary',
                            'sent' => 'bg-info-subtle text-info',
                            'under_negotiation' => 'bg-warning-subtle text-warning',
                            'rejected' => 'bg-danger-subtle text-danger',
                            default => 'bg-secondary-subtle text-secondary',
                        };
                    @endphp
                    <span class="badge {{ $badge }} badge-status fs-6">{{ ucfirst(str_replace('_', ' ', $quotation->status)) }}</span>
                </div>
                <div class="text-muted small">
                    Client: <strong>{{ $quotation->client->company_name }}</strong> &bull; Issue: {{ $quotation->quotation_date->format('d M Y') }} &bull; Valid Until: {{ $quotation->valid_until->format('d M Y') }}
                </div>
            </div>

            <!-- Workflow State Buttons (Blueprint Section 3.3) -->
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.quotations.pdf', $quotation) }}" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Preview PDF
                </a>

                @if($quotation->status === 'draft')
                    <form method="POST" action="{{ route('admin.quotations.submit', $quotation) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">
                            <i class="bi bi-send me-1"></i> Submit for Approval
                        </button>
                    </form>
                @endif

                @if(in_array($quotation->status, ['sent', 'under_negotiation']))
                    <button class="btn btn-success btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#approveModal">
                        <i class="bi bi-check-lg me-1"></i> Approve Proposal
                    </button>
                    <button class="btn btn-outline-danger btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-x-lg me-1"></i> Reject
                    </button>
                @endif

                @if($quotation->status === 'approved')
                    <button class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#convertModal">
                        <i class="bi bi-rocket-takeoff me-1"></i> Convert to Project &rarr;
                    </button>
                @endif

                @if($quotation->status === 'converted' && $quotation->project)
                    <a href="{{ route('admin.projects.show', $quotation->project) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                        <i class="bi bi-kanban me-1"></i> View Converted Project ({{ $quotation->project->project_code }})
                    </a>
                @endif

                @if(!in_array($quotation->status, ['converted']))
                    <a href="{{ route('admin.quotations.edit', $quotation) }}" class="btn btn-light border btn-sm rounded-pill px-3">
                        <i class="bi bi-pencil me-1"></i> Revise
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Line Items Table -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-list-check text-primary me-2"></i> Deliverables & Commercial Breakdown</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Service & Deliverable</th>
                                    <th>Assigned Talent</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Rate</th>
                                    <th class="text-end">Disc.</th>
                                    <th class="text-center">Tax</th>
                                    <th class="text-end">Line Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quotation->items as $idx => $item)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $item->service_name }}</div>
                                            <div class="text-muted" style="font-size: 0.72rem;">{{ $item->description }}</div>
                                        </td>
                                        <td>
                                            @if($item->celebrity)
                                                <a href="{{ route('admin.celebrities.show', $item->celebrity) }}" class="text-decoration-none fw-semibold">
                                                    {{ $item->celebrity->stage_name }}
                                                </a>
                                            @else
                                                <span class="text-muted">Production / General</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ (float)$item->quantity }} {{ $item->unit }}</td>
                                        <td class="text-end">INR {{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-end text-danger">{{ $item->discount_amount > 0 ? '- INR ' . number_format($item->discount_amount, 2) : '-' }}</td>
                                        <td class="text-center">{{ (float)$item->tax_rate }}%</td>
                                        <td class="text-end fw-bold text-dark">INR {{ number_format($item->line_total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Totals Footnote -->
                <div class="card-footer bg-light p-4">
                    <div class="row justify-content-end">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mb-0 small">
                                <tr>
                                    <td class="text-muted">Subtotal (Pre-Tax):</td>
                                    <td class="text-end fw-semibold">INR {{ number_format($quotation->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Discount Concessions:</td>
                                    <td class="text-end text-danger fw-semibold">- INR {{ number_format($quotation->discount_total, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Total Statutory Tax (GST):</td>
                                    <td class="text-end fw-semibold">INR {{ number_format($quotation->tax_total, 2) }}</td>
                                </tr>
                                <tr class="border-top border-dark border-2">
                                    <td class="fs-5 fw-bold text-dark pt-2">Grand Total Payable:</td>
                                    <td class="fs-5 fw-bold text-primary text-end pt-2">INR {{ number_format($quotation->grand_total, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Commercial Terms -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-file-text text-primary me-2"></i> Commercial Terms & Client Scope</h6>
                </div>
                <div class="card-body">
                    <pre class="mb-0 text-muted" style="font-family: inherit; white-space: pre-wrap; font-size: 0.85rem;">{{ $quotation->terms_conditions ?? 'Standard agency contractual terms apply.' }}</pre>
                </div>
            </div>
        </div>

        <!-- Sidebar: Client info & Approval History -->
        <div class="col-lg-4">
            <!-- Client Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-building text-primary me-2"></i> Client Information</h6>
                </div>
                <div class="card-body small">
                    <h6 class="fw-bold text-dark mb-1">{{ $quotation->client->company_name }}</h6>
                    <div class="text-muted mb-2">Code: <code>{{ $quotation->client->client_code }}</code> &bull; {{ $quotation->client->industry }}</div>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-1"><i class="bi bi-envelope text-muted me-2"></i> {{ $quotation->client->email ?? 'N/A' }}</li>
                        <li class="mb-1"><i class="bi bi-telephone text-muted me-2"></i> {{ $quotation->client->phone ?? 'N/A' }}</li>
                        <li><i class="bi bi-geo-alt text-muted me-2"></i> {{ $quotation->client->billing_address ?? 'N/A' }}</li>
                    </ul>
                </div>
            </div>

            <!-- Approval Audit History -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-check text-primary me-2"></i> Approval Audit Trail</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($quotation->approvals as $approval)
                            <li class="list-group-item py-2 px-3 small">
                                <div class="d-flex justify-content-between align-items-center">
                                    @php
                                        $actBadge = match($approval->action) {
                                            'approved' => 'bg-success-subtle text-success',
                                            'rejected' => 'bg-danger-subtle text-danger',
                                            default => 'bg-secondary-subtle text-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $actBadge }} badge-status text-capitalize">{{ $approval->action }}</span>
                                    <small class="text-muted">{{ $approval->action_at->format('d M Y, h:i A') }}</small>
                                </div>
                                <div class="fw-semibold text-dark mt-1">{{ $approval->user->name }}</div>
                                <div class="text-muted">{{ $approval->comments }}</div>
                            </li>
                        @empty
                            <li class="list-group-item py-3 text-center text-muted small">No approval actions logged yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Approve Quotation -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.quotations.approve', $quotation) }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-success"><i class="bi bi-check-circle me-1"></i> Approve Quotation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-3">You are approving commercial terms for <strong>{{ $quotation->quotation_no }}</strong> totaling <strong>INR {{ number_format($quotation->grand_total, 2) }}</strong>.</p>
                <label class="form-label small fw-semibold">Approval Comments / Margin Notes</label>
                <textarea name="comments" class="form-control" rows="3" placeholder="Commercials verified, margin approved by Management...">Approved as per standard commercials.</textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success px-4">Confirm Approval</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Reject Quotation -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.quotations.reject', $quotation) }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-danger"><i class="bi bi-x-circle me-1"></i> Reject Quotation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="form-label small fw-semibold">Rejection Reason</label>
                <textarea name="comments" class="form-control" rows="3" required placeholder="Specify why the quotation was rejected or required revisions..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger px-4">Reject Quotation</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Convert to Project (Blueprint Section 10.4) -->
<div class="modal fade" id="convertModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.quotations.convert', $quotation) }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary"><i class="bi bi-rocket-takeoff me-1"></i> Convert Approved Quotation into Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2 small mb-3">
                    <i class="bi bi-info-circle me-1"></i> This converts the quotation atomically into a campaign project, preserving the approved budget of <strong>INR {{ number_format($quotation->grand_total, 2) }}</strong> and generating initial tasks and milestones.
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Project / Campaign Title</label>
                    <input type="text" name="project_name" class="form-control" value="Campaign - {{ $quotation->quotation_no }} ({{ $quotation->client->company_name }})" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Project Type</label>
                    <select name="project_type" class="form-select">
                        <option value="campaign">Brand Advertising Campaign</option>
                        <option value="endorsement">Celebrity Endorsement & Ambassadorship</option>
                        <option value="shoot">Commercial Shoot / Production</option>
                        <option value="event">Live Event / Concert Staging</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold">Execute Conversion &rarr;</button>
            </div>
        </form>
    </div>
</div>
@endsection

