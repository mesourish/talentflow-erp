@extends('layouts.app')

@section('title', 'Add New Talent Profile')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.celebrities.index') }}">Talent</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create</li>
@endsection

@section('content')
<div class="container-fluid px-0" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Register Celebrity / Talent Profile</h4>
            <p class="text-muted small mb-0">Add artist bio, rate cards, category classification and confidential management contacts</p>
        </div>
        <a href="{{ route('admin.celebrities.index') }}" class="btn btn-light border btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Roster
        </a>
    </div>

    <form method="POST" action="{{ route('admin.celebrities.store') }}" class="card border-0 shadow-sm">
        @csrf
        <div class="card-body p-4">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">1. Public Profile</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Talent Code <span class="text-danger">*</span></label>
                    <input type="text" name="talent_code" class="form-control" value="{{ old('talent_code', $suggestedCode) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Stage / Professional Name <span class="text-danger">*</span></label>
                    <input type="text" name="stage_name" class="form-control" value="{{ old('stage_name') }}" placeholder="e.g. Kiara Sharma" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Talent Category <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Base City</label>
                    <input type="text" name="base_city" class="form-control" value="{{ old('base_city', 'Mumbai') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Profile Photo URL (Square/Portrait)</label>
                    <input type="url" name="profile_image" class="form-control" value="{{ old('profile_image') }}" placeholder="https://images.unsplash.com/...">
                </div>

                <div class="col-12">
                    <label class="form-label small fw-semibold">Biography & Career Highlights</label>
                    <textarea name="biography" class="form-control" rows="3" placeholder="Accolades, popular films, hit singles, brand collaborations...">{{ old('biography') }}</textarea>
                </div>
            </div>

            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">2. Confidential / Management Data (Restricted Access)</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Legal Real Name (for Contracts)</label>
                    <input type="text" name="legal_name" class="form-control" value="{{ old('legal_name') }}" placeholder="Full name as per passport">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Personal Private Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="talent.private@agency.test">
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Manager / Agent Name</label>
                    <input type="text" name="manager_name" class="form-control" value="{{ old('manager_name') }}" placeholder="e.g. Rohit Verma">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Manager Direct Contact Phone</label>
                    <input type="text" name="manager_contact" class="form-control" value="{{ old('manager_contact') }}" placeholder="+91 98200 99881">
                </div>
            </div>

            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">3. Commercials & Status</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Standard Brand Endorsement Fee (INR)</label>
                    <input type="number" name="standard_rate" class="form-control" value="{{ old('standard_rate', 500000) }}" step="10000" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Availability Status <span class="text-danger">*</span></label>
                    <select name="availability_status" class="form-select" required>
                        <option value="available">Available for Bookings</option>
                        <option value="busy">Busy / Active Production</option>
                        <option value="on_leave">On Leave / Blocked</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Profile Status <span class="text-danger">*</span></label>
                    <select name="profile_status" class="form-select" required>
                        <option value="active">Active</option>
                        <option value="draft">Draft</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label small fw-semibold">Internal Notes & Riders</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Hospitality riders, dietary restrictions, preferred brands...">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card-footer bg-light p-3 d-flex justify-content-end gap-2">
            <a href="{{ route('admin.celebrities.index') }}" class="btn btn-light border">Cancel</a>
            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                <i class="bi bi-check-lg me-1"></i> Save Celebrity Profile
            </button>
        </div>
    </form>
</div>
@endsection

