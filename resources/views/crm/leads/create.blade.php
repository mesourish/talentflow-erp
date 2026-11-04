@extends('layouts.app')

@section('title', 'Add Sales Opportunity Lead')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.leads.index') }}">Leads</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create</li>
@endsection

@section('content')
<div class="container-fluid px-0" style="max-width: 860px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Register Commercial Opportunity Lead</h4>
            <p class="text-muted small mb-0">Capture inbound brand interest, expected campaign size and win probability</p>
        </div>
        <a href="{{ route('admin.leads.index') }}" class="btn btn-light border btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Pipeline
        </a>
    </div>

    <form method="POST" action="{{ route('admin.leads.store') }}" class="card border-0 shadow-sm">
        @csrf
        <div class="card-body p-4">
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Lead Code <span class="text-danger">*</span></label>
                    <input type="text" name="lead_code" class="form-control" value="{{ old('lead_code', $suggestedCode) }}" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label small fw-semibold">Opportunity Title <span class="text-danger">*</span></label>
                    <input type="text" name="lead_title" class="form-control" value="{{ old('lead_title') }}" placeholder="e.g. Luxury Festive Brand Endorsement 2026" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Associated Client / Brand</label>
                    <select name="client_id" class="form-select">
                        <option value="">-- Prospective / New Brand --</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }} ({{ $c->client_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Lead Source <span class="text-danger">*</span></label>
                    <select name="lead_source" class="form-select" required>
                        <option value="Referral">Referral / Word of Mouth</option>
                        <option value="Agency Partner">Agency Partner</option>
                        <option value="Inbound Pitch">Inbound Pitch / Web Form</option>
                        <option value="Event">Industry Event / Conclave</option>
                        <option value="Direct Outreach">Direct Commercial Outreach</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Pipeline Stage <span class="text-danger">*</span></label>
                    <select name="stage" class="form-select" required>
                        <option value="new">New Inquiry</option>
                        <option value="contacted">Contacted & Briefed</option>
                        <option value="proposal">Proposal Prepared</option>
                        <option value="negotiation">Under Negotiation</option>
                        <option value="won">Won / Closed</option>
                        <option value="lost">Lost</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Estimated Budget (INR) <span class="text-danger">*</span></label>
                    <input type="number" name="estimated_value" class="form-control" value="{{ old('estimated_value', 1000000) }}" step="10000" min="0" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Win Probability (%) <span class="text-danger">*</span></label>
                    <input type="number" name="probability" class="form-control" value="{{ old('probability', 50) }}" min="0" max="100" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Expected Close Date</label>
                    <input type="date" name="expected_close_date" class="form-control" value="{{ old('expected_close_date', now()->addDays(30)->toDateString()) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Assigned Account Owner</label>
                    <select name="assigned_to" class="form-select">
                        <option value="">-- Unassigned --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ old('assigned_to', auth()->id()) == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->getRoleNames()->first() }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label small fw-semibold">Campaign Brief & Commercial Requirements</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Target celebrities, event dates, media channels, deliverables...">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card-footer bg-light p-3 d-flex justify-content-end gap-2">
            <a href="{{ route('admin.leads.index') }}" class="btn btn-light border">Cancel</a>
            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                <i class="bi bi-check-lg me-1"></i> Register Lead
            </button>
        </div>
    </form>
</div>
@endsection

