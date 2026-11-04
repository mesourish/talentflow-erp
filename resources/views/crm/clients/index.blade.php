@extends('layouts.app')

@section('title', 'Brands & Clients')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Clients</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Brand & Client Accounts</h4>
            <p class="text-muted small mb-0">Manage enterprise brands, advertising agencies, contacts and billing profiles</p>
        </div>
        <a href="{{ route('admin.clients.create') }}" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="bi bi-building-add me-1"></i> Register New Client
        </a>
    </div>

    <!-- Filters & Search -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.clients.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search by company name, code or email..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="industry" class="form-select">
                        <option value="">All Industries</option>
                        @foreach($industries as $ind)
                            <option value="{{ $ind }}" {{ request('industry') === $ind ? 'selected' : '' }}>{{ $ind }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="prospect" {{ request('status') === 'prospect' ? 'selected' : '' }}>Prospect</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                    @if(request()->anyFilled(['search', 'industry', 'status']))
                        <a href="{{ route('admin.clients.index') }}" class="btn btn-light border" title="Reset"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Client / Code</th>
                            <th>Type & Industry</th>
                            <th>Contact Info</th>
                            <th>Payment Terms</th>
                            <th>Engagement</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.clients.show', $client) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $client->company_name }}
                                    </a>
                                    <div class="text-muted small"><code>{{ $client->client_code }}</code></div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary me-1">{{ ucfirst($client->client_type) }}</span>
                                    <div class="text-muted small">{{ $client->industry ?? 'General Enterprise' }}</div>
                                </td>
                                <td>
                                    <div class="small"><i class="bi bi-envelope text-muted me-1"></i> {{ $client->email ?? 'N/A' }}</div>
                                    <div class="small text-muted"><i class="bi bi-telephone text-muted me-1"></i> {{ $client->phone ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">Net {{ $client->payment_terms_days }} Days</span>
                                </td>
                                <td>
                                    <div class="small">
                                        <span class="fw-semibold">{{ $client->projects_count }}</span> Projects &bull;
                                        <span class="fw-semibold">{{ $client->quotations_count }}</span> Quotes
                                    </div>
                                    <small class="text-muted">{{ $client->contacts_count }} Contacts</small>
                                </td>
                                <td>
                                    @php
                                        $badge = match($client->status) {
                                            'active' => 'bg-success-subtle text-success',
                                            'prospect' => 'bg-info-subtle text-info',
                                            default => 'bg-secondary-subtle text-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badge }} badge-status">{{ ucfirst($client->status) }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-light border" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-light border" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-building fs-1 d-block mb-2 text-secondary"></i>
                                    No clients found matching the selected criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($clients->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $clients->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

