@extends('layouts.app')

@section('title', 'CRM Activities & Interactions')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Activities</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Client & Talent Interactions</h4>
            <p class="text-muted small mb-0">Record and track calls, pitch meetings, emails and follow-up tasks</p>
        </div>
        <button class="btn btn-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#logActivityModal">
            <i class="bi bi-telephone-plus me-1"></i> Log New Interaction
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Subject & Details</th>
                            <th>Related Record</th>
                            <th>Logged By</th>
                            <th>Activity Date</th>
                            <th>Follow-up</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $act)
                            <tr>
                                <td>
                                    @php
                                        $icon = match($act->activity_type) {
                                            'call' => 'bi-telephone text-primary',
                                            'email' => 'bi-envelope text-info',
                                            'meeting' => 'bi-people text-success',
                                            default => 'bi-check2-circle text-warning',
                                        };
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <i class="bi {{ $icon }} fs-5 me-2"></i>
                                        <span class="small fw-semibold text-capitalize">{{ $act->activity_type }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark small">{{ $act->subject }}</div>
                                    <div class="text-muted small text-truncate" style="max-width: 320px;">{{ $act->description }}</div>
                                </td>
                                <td class="small">
                                    @if($act->client)
                                        <div><i class="bi bi-building text-muted me-1"></i> {{ $act->client->company_name }}</div>
                                    @endif
                                    @if($act->lead)
                                        <div class="text-muted"><i class="bi bi-funnel text-muted me-1"></i> {{ $act->lead->lead_title }}</div>
                                    @endif
                                    @if($act->celebrity)
                                        <div class="text-primary"><i class="bi bi-person text-muted me-1"></i> {{ $act->celebrity->stage_name }}</div>
                                    @endif
                                </td>
                                <td class="small">{{ $act->user->name }}</td>
                                <td class="small text-muted">{{ $act->activity_at->format('d M Y, h:i A') }}</td>
                                <td class="small">
                                    @if($act->follow_up_at)
                                        <span class="badge bg-light text-dark border"><i class="bi bi-alarm me-1"></i> {{ $act->follow_up_at->format('d M Y') }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success badge-status text-capitalize">{{ $act->status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-clock-history fs-1 d-block mb-2 text-secondary"></i>
                                    No activities logged yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($activities->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Log Activity -->
<div class="modal fade" id="logActivityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.activities.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Log New CRM Activity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Activity Type</label>
                        <select name="activity_type" class="form-select" required>
                            <option value="call">Phone Call</option>
                            <option value="email">Email</option>
                            <option value="meeting">Pitch / Review Meeting</option>
                            <option value="follow-up">Follow-up</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="completed">Completed</option>
                            <option value="open">Open / Scheduled</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Subject</label>
                        <input type="text" name="subject" class="form-control" placeholder="e.g. Discussed TVC commercial rights" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Date & Time</label>
                        <input type="datetime-local" name="activity_at" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Next Follow-up (Optional)</label>
                        <input type="datetime-local" name="follow_up_at" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Discussion Summary</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Key outcomes, client feedback, action points..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary px-3">Save Activity</button>
            </div>
        </form>
    </div>
</div>
@endsection

