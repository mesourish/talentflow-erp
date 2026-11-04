@extends('layouts.app')

@section('title', $celebrity->stage_name)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.celebrities.index') }}">Talent</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $celebrity->stage_name }}</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <!-- Talent Hero Header -->
    <div class="card border-0 shadow-sm mb-4 overflow-hidden">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="position-relative" style="width: 80px; height: 80px;">
                        @if($celebrity->profile_image)
                            <img src="{{ $celebrity->profile_image }}" class="rounded-circle w-100 h-100 object-fit-cover border shadow-sm">
                        @else
                            <div class="rounded-circle w-100 h-100 bg-primary text-white d-flex align-items-center justify-content-center fs-2 fw-bold">
                                {{ substr($celebrity->stage_name, 0, 1) }}
                            </div>
                        @endif
                        <span class="position-absolute bottom-0 end-0 p-1 bg-{{ $celebrity->availability_status === 'available' ? 'success' : 'warning' }} border border-white rounded-circle"></span>
                    </div>

                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h4 class="fw-bold mb-0 text-dark">{{ $celebrity->stage_name }}</h4>
                            <span class="badge bg-secondary-subtle text-secondary">{{ $celebrity->category->name }}</span>
                            @php
                                $availBadge = match($celebrity->availability_status) {
                                    'available' => 'bg-success-subtle text-success',
                                    'busy' => 'bg-warning-subtle text-warning',
                                    default => 'bg-secondary-subtle text-secondary',
                                };
                            @endphp
                            <span class="badge {{ $availBadge }} badge-status">{{ ucfirst($celebrity->availability_status) }}</span>
                        </div>
                        <div class="text-muted small">
                            <code>{{ $celebrity->talent_code }}</code> &bull; Base: {{ $celebrity->base_city ?? 'Mumbai' }} &bull; Profile: <span class="badge bg-light text-dark border">{{ ucfirst($celebrity->profile_status) }}</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.bookings.create') }}?celebrity_id={{ $celebrity->id }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                        <i class="bi bi-calendar-plus me-1"></i> Book Talent
                    </a>
                    <a href="{{ route('admin.celebrities.edit', $celebrity) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-pencil me-1"></i> Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row g-4">
        <!-- Left: Bio, Social, Confidential Data -->
        <div class="col-lg-4">
            <!-- Biography Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-person-lines-fill text-primary me-2"></i> About & Bio</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-0">{{ $celebrity->biography ?? 'No detailed biography provided.' }}</p>
                </div>
            </div>

            <!-- Social Reach & Verified Metrics -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-share text-primary me-2"></i> Verified Social Reach</h6>
                    <span class="badge bg-light text-secondary border">{{ $celebrity->socialProfiles->count() }} Profiles</span>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($celebrity->socialProfiles as $sp)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3 small">
                                <div>
                                    <i class="bi bi-{{ strtolower($sp->platform) === 'instagram' ? 'instagram text-danger' : 'youtube text-danger' }} me-2"></i>
                                    <a href="{{ $sp->profile_url }}" target="_blank" class="fw-semibold text-dark text-decoration-none">{{ $sp->platform }}</a>
                                    <div class="text-muted" style="font-size: 0.72rem;">Engagement: {{ $sp->engagement_rate }}%</div>
                                </div>
                                <span class="badge bg-light text-dark border fw-bold">{{ number_format($sp->follower_count / 1000000, 2) }}M Followers</span>
                            </li>
                        @empty
                            <li class="list-group-item py-3 text-center text-muted small">No social accounts linked.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Sensitive Management Data (Security Blueprint Page 13 & 50) -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-lock text-danger me-2"></i> Private Management Data</h6>
                    @if($canViewSensitive)
                        <span class="badge bg-success-subtle text-success small">Authorized Access</span>
                    @else
                        <span class="badge bg-warning-subtle text-warning small">Restricted</span>
                    @endif
                </div>
                <div class="card-body small">
                    @if($canViewSensitive)
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><span class="text-muted d-block">Legal Name (Contracts):</span> <strong>{{ $celebrity->legal_name ?? 'N/A' }}</strong></li>
                            <li class="mb-2"><span class="text-muted d-block">Private Email:</span> <strong>{{ $celebrity->email ?? 'N/A' }}</strong></li>
                            <li class="mb-2"><span class="text-muted d-block">Private Phone:</span> <strong>{{ $celebrity->phone ?? 'N/A' }}</strong></li>
                            <li class="mb-2"><span class="text-muted d-block">Manager Name:</span> <strong>{{ $celebrity->manager_name ?? 'N/A' }}</strong></li>
                            <li class="mb-2"><span class="text-muted d-block">Manager Direct Line:</span> <strong>{{ $celebrity->manager_contact ?? 'N/A' }}</strong></li>
                            @if($celebrity->notes)
                                <li class="pt-2 border-top"><span class="text-muted d-block">Confidential Riders:</span> <em>{{ $celebrity->notes }}</em></li>
                            @endif
                        </ul>
                    @else
                        <div class="p-3 bg-light rounded text-center text-muted">
                            <i class="bi bi-lock-fill fs-2 text-secondary d-block mb-1"></i>
                            Legal name, personal contact numbers and manager direct lines are protected under agency security policy.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Rate Cards, Bookings, Payouts -->
        <div class="col-lg-8">
            <!-- Rate Cards Table -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-tag text-primary me-2"></i> Official Rate Cards</h6>
                    <button class="btn btn-outline-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#addRateModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Service Rate
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead>
                                <tr>
                                    <th>Service Description</th>
                                    <th>Standard Fee</th>
                                    <th>Effective From</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($celebrity->rates as $rate)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $rate->service_type }}</div>
                                            <div class="text-muted small">{{ $rate->description }}</div>
                                        </td>
                                        <td class="fw-bold text-primary">
                                            INR {{ number_format($rate->standard_fee, 2) }}
                                        </td>
                                        <td class="text-muted">{{ $rate->effective_from->format('d M Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $rate->is_active ? 'success' : 'secondary' }}-subtle text-{{ $rate->is_active ? 'success' : 'secondary' }} badge-status">
                                                {{ $rate->is_active ? 'Active' : 'Archived' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">No rate cards configured yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Historical & Upcoming Bookings -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar-check text-primary me-2"></i> Booking Schedule & Engagements</h6>
                    <span class="badge bg-light text-secondary border">{{ $celebrity->bookings->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead>
                                <tr>
                                    <th>Booking / Project</th>
                                    <th>Date & Time</th>
                                    <th>Agreed Fee</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($celebrity->bookings as $booking)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.bookings.show', $booking) }}" class="fw-semibold text-dark text-decoration-none">
                                                {{ $booking->title }}
                                            </a>
                                            <div class="text-muted" style="font-size: 0.72rem;">{{ $booking->booking_code }} &bull; {{ $booking->venue_name ?? 'Location TBA' }}</div>
                                        </td>
                                        <td class="text-muted">
                                            {{ $booking->starts_at->format('d M Y') }}
                                            <div style="font-size: 0.7rem;">{{ $booking->starts_at->format('h:i A') }}</div>
                                        </td>
                                        <td class="fw-semibold">
                                            {{ $booking->pivot->agreed_fee ? 'INR ' . number_format($booking->pivot->agreed_fee, 2) : 'Included in quote' }}
                                        </td>
                                        <td>{{ $booking->pivot->assignment_role ?? 'Artist' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : 'warning' }}-subtle text-{{ $booking->status === 'confirmed' ? 'success' : 'warning' }} badge-status">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">No bookings recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payouts & Net Earnings -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-cash-stack text-primary me-2"></i> Talent Payout History</h6>
                    <span class="badge bg-light text-secondary border">{{ $celebrity->payouts->count() }} Payouts</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead>
                                <tr>
                                    <th>Project</th>
                                    <th>Gross Fee</th>
                                    <th>Commission</th>
                                    <th>Net Payable</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($celebrity->payouts as $payout)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.projects.show', $payout->project) }}" class="fw-semibold text-dark text-decoration-none">
                                                {{ $payout->project->project_name }}
                                            </a>
                                            <div class="text-muted" style="font-size: 0.72rem;">{{ $payout->project->project_code }}</div>
                                        </td>
                                        <td>INR {{ number_format($payout->gross_amount, 2) }}</td>
                                        <td class="text-danger">- INR {{ number_format($payout->commission_amount, 2) }}</td>
                                        <td class="fw-bold text-success">INR {{ number_format($payout->net_payable, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $payout->status === 'paid' ? 'success' : 'warning' }}-subtle text-{{ $payout->status === 'paid' ? 'success' : 'warning' }} badge-status">
                                                {{ ucfirst($payout->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">No payouts processed yet.</td>
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

<!-- Modal: Add Service Rate -->
<div class="modal fade" id="addRateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.celebrities.rates.store', $celebrity) }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Service Rate Card</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold">Service Type</label>
                        <select name="service_type" class="form-select" required>
                            <option value="Brand Endorsement">Brand Endorsement</option>
                            <option value="Event Appearance">Event Appearance</option>
                            <option value="Social Media Promotion">Social Media Promotion</option>
                            <option value="TV Advertisement">TV Advertisement</option>
                            <option value="Photoshoot">Photoshoot</option>
                            <option value="Cameo / Keynote">Cameo / Keynote</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Currency</label>
                        <input type="text" name="currency" class="form-control" value="INR" readonly>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Standard Fee (INR)</label>
                        <input type="number" name="standard_fee" class="form-control" step="1000" min="0" required placeholder="e.g. 500000">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Effective From</label>
                        <input type="date" name="effective_from" class="form-control" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Effective To (Optional)</label>
                        <input type="date" name="effective_to" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Service Description / Scope</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Includes shoot hours, licensed rights, revisions..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-3">Save Rate Card</button>
            </div>
        </form>
    </div>
</div>
@endsection

