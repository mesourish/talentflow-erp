@extends('layouts.app')

@section('title', 'Edit ' . $lead->lead_title)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.leads.index') }}">Leads</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.leads.show', $lead) }}">{{ $lead->lead_code }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
<div class="container-fluid px-0" style="max-width: 860px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Edit Lead: {{ $lead->lead_title }}</h4>
            <p class="text-muted small mb-0">Update stage progress, budget expectation or assigned owner</p>
        </div>
        <a href="{{ route('admin.leads.show', $lead) }}" class="btn btn-light border btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Detail
        </a>
    </div>

    <form method="POST" action="{{ route('admin.leads.update', $lead) }}" class="card border-0 shadow-sm">
        @csrf
        @method('PUT')
        <div class="card-body p-4">
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Lead Code</label>
                    <input type="text" class="form-control bg-light" value="{{ $lead->lead_code }}" readonly>
                </div>
                <div class="col-md-8">
                    <label class="form-label small fw-semibold">Opportunity Title <span class="text-danger">*</span></label>
                    <input type="text" name="lead_title" class="form-control" value="{{ old('lead_title', $lead->lead_title) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Associated Client / Brand</label>
                    <select name="client_id" class="form-select">
                        <option value="">-- Prospective / New Brand --</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ old('client_id', $lead->client_id) == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Lead Source <span class="text-danger">*</span></label>
                    <input type="text" name="lead_source" class="form-control" value="{{ old('lead_source', $lead->lead_source) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Pipeline Stage <span class="text-danger">*</span></label>
                    <select name="stage" class="form-select" required>
                        <option value="new" {{ $lead->stage === 'new' ? 'selected' : '' }}>New Inquiry</option>
                        <option value="contacted" {{ $lead->stage === 'contacted' ? 'selected' : '' }}>Contacted & Briefed</option>
                        <option value="proposal" {{ $lead->stage === 'proposal' ? 'selected' : '' }}>Proposal Prepared</option>
                        <option value="negotiation" {{ $lead->stage === 'negotiation' ? 'selected' : '' }}>Under Negotiation</option>
                        <option value="won" {{ $lead->stage === 'won' ? 'selected' : '' }}>Won / Closed</option>
                        <option value="lost" {{ $lead->stage === 'lost' ? 'selected' : '' }}>Lost</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Estimated Budget (INR) <span class="text-danger">*</span></label>
                    <input type="number" name="estimated_value" class="form-control" value="{{ old('estimated_value', $lead->estimated_value) }}" step="10000" min="0" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Win Probability (%) <span class="text-danger">*</span></label>
                    <input type="number" name="probability" class="form-control" value="{{ old('probability', $lead->probability) }}" min="0" max="100" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Expected Close Date</label>
                    <input type="date" name="expected_close_date" class="form-control" value="{{ old('expected_close_date', $lead->expected_close_date?->toDateString()) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Assigned Account Owner</label>
                    <select name="assigned_to" class="form-select">
                        <option value="">-- Unassigned --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ old('assigned_to', $lead->assigned_to) == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->getRoleNames()->first() }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label small fw-semibold">Campaign Brief & Commercial Requirements</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $lead->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="card-footer bg-light p-3 d-flex justify-content-end gap-2">
            <a href="{{ route('admin.leads.show', $lead) }}" class="btn btn-light border">Cancel</a>
            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                <i class="bi bi-check-lg me-1"></i> Save Changes
            </button>
        </div>
    </form>
</div>
@endsection

