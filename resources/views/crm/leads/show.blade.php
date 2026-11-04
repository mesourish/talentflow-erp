@extends('layouts.app')

@section('title', $lead->lead_title)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.leads.index') }}">Leads</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $lead->lead_code }}</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h4 class="fw-bold mb-0 text-dark">{{ $lead->lead_title }}</h4>
                    <span class="badge bg-secondary-subtle text-secondary">{{ $lead->lead_source }}</span>
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
                </div>
                <div class="text-muted small">
                    <code>{{ $lead->lead_code }}</code> &bull; Expected Close: {{ $lead->expected_close_date ? $lead->expected_close_date->format('d M Y') : 'TBD' }} &bull; Probability: {{ $lead->probability }}%
                </div>
            </div>
            <div class="d-flex gap-2">
                @if($lead->client)
                    <a href="{{ route('admin.quotations.create') }}?client_id={{ $lead->client_id }}&lead_id={{ $lead->id }}" class="btn btn-primary btn-sm rounded-pill px-3">
                        <i class="bi bi-file-earmark-plus me-1"></i> Draft Quotation
                    </a>
                @endif
                <a href="{{ route('admin.leads.edit', $lead) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-pencil me-1"></i> Edit Lead
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-funnel text-primary me-2"></i> Opportunity Overview</h6>
                </div>
                <div class="card-body small">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><span class="text-muted d-block">Estimated Budget:</span> <strong class="fs-6 text-dark">INR {{ number_format($lead->estimated_value, 2) }}</strong></li>
                        <li class="mb-2"><span class="text-muted d-block">Account Owner:</span> <strong>{{ $lead->assignee ? $lead->assignee->name : 'Unassigned' }}</strong></li>
                        <li class="mb-2"><span class="text-muted d-block">Brand / Client:</span> 
                            @if($lead->client)
                                <a href="{{ route('admin.clients.show', $lead->client) }}" class="text-decoration-none fw-semibold">{{ $lead->client->company_name }}</a>
                            @else
                                <span class="text-muted">Unlinked New Prospect</span>
                            @endif
                        </li>
                        <li class="mb-2"><span class="text-muted d-block">Win Probability:</span>
                            <div class="progress mt-1" style="height: 6px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $lead->probability }}%"></div>
                            </div>
                            <span class="text-muted" style="font-size: 0.75rem;">{{ $lead->probability }}% likelihood</span>
                        </li>
                        @if($lead->notes)
                            <li class="pt-2 border-top"><span class="text-muted d-block">Scope & Brief Notes:</span> <em>{{ $lead->notes }}</em></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Linked Quotations -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-spreadsheet text-primary me-2"></i> Linked Quotations</h6>
                    <span class="badge bg-light text-secondary border">{{ $lead->quotations->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead>
                                <tr>
                                    <th>Quotation No</th>
                                    <th>Grand Total</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lead->quotations as $q)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.quotations.show', $q) }}" class="fw-bold text-dark text-decoration-none">
                                                {{ $q->quotation_no }}
                                            </a>
                                            <div class="text-muted" style="font-size: 0.72rem;">v{{ $q->version_no }}</div>
                                        </td>
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
                                        <td colspan="4" class="text-center py-3 text-muted">No quotations generated yet for this lead.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Follow-up Activities -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-primary me-2"></i> Interaction Timeline & Follow-ups</h6>
                    <button class="btn btn-outline-primary btn-sm rounded-pill" data-bs-toggle="collapse" data-bs-target="#newActivityForm">
                        <i class="bi bi-plus-lg me-1"></i> Log Activity
                    </button>
                </div>

                <div class="collapse p-3 bg-light border-bottom" id="newActivityForm">
                    <form method="POST" action="{{ route('admin.activities.store') }}">
                        @csrf
                        <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                        <input type="hidden" name="client_id" value="{{ $lead->client_id }}">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <select name="activity_type" class="form-select form-select-sm" required>
                                    <option value="call">Phone Call</option>
                                    <option value="email">Email Sent</option>
                                    <option value="meeting">Pitch Meeting</option>
                                    <option value="follow-up">Follow-up Task</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="subject" class="form-control form-select-sm" placeholder="Subject / Meeting topic" required>
                            </div>
                            <div class="col-md-4">
                                <input type="datetime-local" name="activity_at" class="form-control form-select-sm" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                            </div>
                            <div class="col-12">
                                <textarea name="description" class="form-control form-select-sm" rows="2" placeholder="Discussion summary, client objections, next action items..."></textarea>
                            </div>
                            <div class="col-12 text-end">
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="btn btn-primary btn-sm px-3">Save Activity</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($lead->activities as $act)
                            <li class="list-group-item py-2 px-3 small">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-light text-dark border text-uppercase" style="font-size: 0.68rem;">{{ $act->activity_type }}</span>
                                    <small class="text-muted">{{ $act->activity_at->format('d M Y, h:i A') }}</small>
                                </div>
                                <div class="fw-semibold text-dark mt-1">{{ $act->subject }}</div>
                                <div class="text-muted">{{ $act->description }}</div>
                            </li>
                        @empty
                            <li class="list-group-item py-3 text-center text-muted small">No logged activities for this lead yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

