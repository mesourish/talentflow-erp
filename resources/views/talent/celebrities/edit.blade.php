@extends('layouts.app')

@section('title', 'Edit ' . $celebrity->stage_name)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.celebrities.index') }}">Talent</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.celebrities.show', $celebrity) }}">{{ $celebrity->stage_name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
<div class="container-fluid px-0" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Edit Celebrity Profile: {{ $celebrity->stage_name }}</h4>
            <p class="text-muted small mb-0">Update bio, availability status and representation contacts</p>
        </div>
        <a href="{{ route('admin.celebrities.show', $celebrity) }}" class="btn btn-light border btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Profile
        </a>
    </div>

    <form method="POST" action="{{ route('admin.celebrities.update', $celebrity) }}" class="card border-0 shadow-sm">
        @csrf
        @method('PUT')
        <div class="card-body p-4">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">1. Public Profile</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Talent Code</label>
                    <input type="text" class="form-control bg-light" value="{{ $celebrity->talent_code }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Stage / Professional Name <span class="text-danger">*</span></label>
                    <input type="text" name="stage_name" class="form-control" value="{{ old('stage_name', $celebrity->stage_name) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Talent Category <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $celebrity->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Base City</label>
                    <input type="text" name="base_city" class="form-control" value="{{ old('base_city', $celebrity->base_city) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Profile Photo URL</label>
                    <input type="url" name="profile_image" class="form-control" value="{{ old('profile_image', $celebrity->profile_image) }}">
                </div>

                <div class="col-12">
                    <label class="form-label small fw-semibold">Biography</label>
                    <textarea name="biography" class="form-control" rows="3">{{ old('biography', $celebrity->biography) }}</textarea>
                </div>
            </div>

            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">2. Confidential / Management Data</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Legal Real Name (Contracts)</label>
                    <input type="text" name="legal_name" class="form-control" value="{{ old('legal_name', $celebrity->legal_name) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Personal Private Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $celebrity->email) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Manager / Agent Name</label>
                    <input type="text" name="manager_name" class="form-control" value="{{ old('manager_name', $celebrity->manager_name) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Manager Direct Contact Phone</label>
                    <input type="text" name="manager_contact" class="form-control" value="{{ old('manager_contact', $celebrity->manager_contact) }}">
                </div>
            </div>

            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">3. Status</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Availability Status <span class="text-danger">*</span></label>
                    <select name="availability_status" class="form-select" required>
                        <option value="available" {{ $celebrity->availability_status === 'available' ? 'selected' : '' }}>Available for Bookings</option>
                        <option value="busy" {{ $celebrity->availability_status === 'busy' ? 'selected' : '' }}>Busy / Shooting</option>
                        <option value="on_leave" {{ $celebrity->availability_status === 'on_leave' ? 'selected' : '' }}>On Leave / Blocked</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Profile Status <span class="text-danger">*</span></label>
                    <select name="profile_status" class="form-select" required>
                        <option value="active" {{ $celebrity->profile_status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="draft" {{ $celebrity->profile_status === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="archived" {{ $celebrity->profile_status === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label small fw-semibold">Internal Notes & Riders</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes', $celebrity->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="card-footer bg-light p-3 d-flex justify-content-end gap-2">
            <a href="{{ route('admin.celebrities.show', $celebrity) }}" class="btn btn-light border">Cancel</a>
            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                <i class="bi bi-check-lg me-1"></i> Save Changes
            </button>
        </div>
    </form>
</div>
@endsection

