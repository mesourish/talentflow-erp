@extends('layouts.app')

@section('title', 'Edit ' . $client->company_name)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}">Clients</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.clients.show', $client) }}">{{ $client->company_name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
<div class="container-fluid px-0" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Edit Client: {{ $client->company_name }}</h4>
            <p class="text-muted small mb-0">Update brand metadata, contact points and commercial payment terms</p>
        </div>
        <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-light border btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Profile
        </a>
    </div>

    <form method="POST" action="{{ route('admin.clients.update', $client) }}" class="card border-0 shadow-sm">
        @csrf
        @method('PUT')
        <div class="card-body p-4">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">1. Organization Details</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Client Code</label>
                    <input type="text" class="form-control bg-light" value="{{ $client->client_code }}" readonly>
                </div>
                <div class="col-md-8">
                    <label class="form-label small fw-semibold">Company / Brand Name <span class="text-danger">*</span></label>
                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $client->company_name) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Client Type <span class="text-danger">*</span></label>
                    <select name="client_type" class="form-select" required>
                        <option value="brand" {{ $client->client_type === 'brand' ? 'selected' : '' }}>Brand / Direct Advertiser</option>
                        <option value="agency" {{ $client->client_type === 'agency' ? 'selected' : '' }}>Advertising / PR Agency</option>
                        <option value="individual" {{ $client->client_type === 'individual' ? 'selected' : '' }}>Individual Promoter</option>
                        <option value="other" {{ $client->client_type === 'other' ? 'selected' : '' }}>Other Commercial Partner</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Industry Sector</label>
                    <input type="text" name="industry" class="form-control" value="{{ old('industry', $client->industry) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Website</label>
                    <input type="url" name="website" class="form-control" value="{{ old('website', $client->website) }}">
                </div>
            </div>

            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">2. Commercial & Billing Setup</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Official Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $client->email) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Main Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $client->phone) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-semibold">GSTIN / Tax ID Number</label>
                    <input type="text" name="tax_number" class="form-control" value="{{ old('tax_number', $client->tax_number) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Payment Terms (Days) <span class="text-danger">*</span></label>
                    <input type="number" name="payment_terms_days" class="form-control" value="{{ old('payment_terms_days', $client->payment_terms_days) }}" min="0" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="active" {{ $client->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="prospect" {{ $client->status === 'prospect' ? 'selected' : '' }}>Prospect</option>
                        <option value="inactive" {{ $client->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label small fw-semibold">Billing Address</label>
                    <textarea name="billing_address" class="form-control" rows="2">{{ old('billing_address', $client->billing_address) }}</textarea>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Internal Notes / Strategic Account Context</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $client->notes) }}</textarea>
            </div>
        </div>

        <div class="card-footer bg-light p-3 d-flex justify-content-between align-items-center">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="if(confirm('Are you sure you want to deactivate or remove this client?')) document.getElementById('delete-form').submit();">
                <i class="bi bi-trash me-1"></i> Delete Client
            </button>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-light border">Cancel</a>
                <button type="submit" class="btn btn-primary px-4 fw-semibold">
                    <i class="bi bi-check-lg me-1"></i> Save Changes
                </button>
            </div>
        </div>
    </form>

    <form id="delete-form" method="POST" action="{{ route('admin.clients.destroy', $client) }}" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection

