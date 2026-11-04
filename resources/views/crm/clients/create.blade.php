@extends('layouts.app')

@section('title', 'Register New Client')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}">Clients</a></li>
    <li class="breadcrumb-item active" aria-current="page">Register</li>
@endsection

@section('content')
<div class="container-fluid px-0" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Register New Client Account</h4>
            <p class="text-muted small mb-0">Add brand, agency or corporate partner credentials to the CRM roster</p>
        </div>
        <a href="{{ route('admin.clients.index') }}" class="btn btn-light border btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Roster
        </a>
    </div>

    <form method="POST" action="{{ route('admin.clients.store') }}" class="card border-0 shadow-sm">
        @csrf
        <div class="card-body p-4">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">1. Organization Details</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Client Code <span class="text-danger">*</span></label>
                    <input type="text" name="client_code" class="form-control" value="{{ old('client_code', $suggestedCode) }}" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label small fw-semibold">Company / Brand Name <span class="text-danger">*</span></label>
                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name') }}" placeholder="e.g. Zenith Fashion Group" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Client Type <span class="text-danger">*</span></label>
                    <select name="client_type" class="form-select" required>
                        <option value="brand">Brand / Direct Advertiser</option>
                        <option value="agency">Advertising / PR Agency</option>
                        <option value="individual">Individual Promoter</option>
                        <option value="other">Other Commercial Partner</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Industry Sector</label>
                    <input type="text" name="industry" class="form-control" value="{{ old('industry') }}" placeholder="e.g. Luxury Retail / FMCG">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Website</label>
                    <input type="url" name="website" class="form-control" value="{{ old('website') }}" placeholder="https://example.com">
                </div>
            </div>

            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">2. Commercial & Billing Setup</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Official Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="accounts@client.example.com">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Main Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+91 22 1234 5678">
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-semibold">GSTIN / Tax ID Number</label>
                    <input type="text" name="tax_number" class="form-control" value="{{ old('tax_number') }}" placeholder="e.g. 27AAACA1234F1Z8">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Payment Terms (Days) <span class="text-danger">*</span></label>
                    <input type="number" name="payment_terms_days" class="form-control" value="{{ old('payment_terms_days', 30) }}" min="0" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="active">Active</option>
                        <option value="prospect">Prospect</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label small fw-semibold">Billing Address</label>
                    <textarea name="billing_address" class="form-control" rows="2" placeholder="Registered commercial billing headquarters...">{{ old('billing_address') }}</textarea>
                </div>
            </div>

            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">3. Primary Key Contact (Optional)</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Contact Person Name</label>
                    <input type="text" name="contact_name" class="form-control" value="{{ old('contact_name') }}" placeholder="e.g. Vikram Malhotra">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Designation / Role</label>
                    <input type="text" name="contact_designation" class="form-control" value="{{ old('contact_designation') }}" placeholder="e.g. Head of Marketing">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Contact Direct Phone</label>
                    <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone') }}" placeholder="+91 98200 12345">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Internal Notes / Strategic Account Context</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Brand history, preferred talent types, agency commission agreements...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="card-footer bg-light p-3 d-flex justify-content-end gap-2">
            <a href="{{ route('admin.clients.index') }}" class="btn btn-light border">Cancel</a>
            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                <i class="bi bi-check-lg me-1"></i> Register Client
            </button>
        </div>
    </form>
</div>
@endsection

