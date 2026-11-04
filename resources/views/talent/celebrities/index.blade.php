@extends('layouts.app')

@section('title', 'Celebrity & Talent Roster')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Talent Roster</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Celebrity & Talent Directory</h4>
            <p class="text-muted small mb-0">Exclusive artist representation, social reach metrics, rate cards and real-time availability</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-tags me-1"></i> Manage Categories
            </a>
            <a href="{{ route('admin.celebrities.create') }}" class="btn btn-primary rounded-pill px-3 shadow-sm">
                <i class="bi bi-person-plus me-1"></i> Add Talent Profile
            </a>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.celebrities.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search by stage name, talent code, city..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="availability_status" class="form-select">
                        <option value="">All Availability</option>
                        <option value="available" {{ request('availability_status') === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="busy" {{ request('availability_status') === 'busy' ? 'selected' : '' }}>Busy / Shooting</option>
                        <option value="on_leave" {{ request('availability_status') === 'on_leave' ? 'selected' : '' }}>On Leave</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                    @if(request()->anyFilled(['search', 'category_id', 'availability_status']))
                        <a href="{{ route('admin.celebrities.index') }}" class="btn btn-light border" title="Reset"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Talent Cards Grid -->
    <div class="row g-4 mb-4">
        @forelse($celebrities as $celeb)
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    <div class="position-relative" style="height: 200px; background: #1e293b;">
                        @if($celeb->profile_image)
                            <img src="{{ $celeb->profile_image }}" alt="{{ $celeb->stage_name }}" class="w-100 h-100" style="object-fit: cover;">
                        @else
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-white-50">
                                <i class="bi bi-person fs-1"></i>
                            </div>
                        @endif
                        <span class="position-absolute top-0 start-0 m-2 badge bg-dark bg-opacity-75" style="font-size: 0.7rem;">
                            {{ $celeb->talent_code }}
                        </span>
                        @php
                            $availBadge = match($celeb->availability_status) {
                                'available' => 'bg-success',
                                'busy' => 'bg-warning text-dark',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <span class="position-absolute top-0 end-0 m-2 badge {{ $availBadge }}" style="font-size: 0.7rem;">
                            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> {{ ucfirst($celeb->availability_status) }}
                        </span>
                    </div>

                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <span class="badge bg-secondary-subtle text-secondary small mb-1">{{ $celeb->category->name }}</span>
                            <h5 class="fw-bold mb-1 text-dark text-truncate">
                                <a href="{{ route('admin.celebrities.show', $celeb) }}" class="text-dark text-decoration-none">
                                    {{ $celeb->stage_name }}
                                </a>
                            </h5>
                            <div class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i> {{ $celeb->base_city ?? 'Mumbai' }}</div>
                            <p class="text-muted small text-truncate-2 mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 0.8rem;">
                                {{ $celeb->biography ?? 'Leading professional artist in agency roster.' }}
                            </p>
                        </div>

                        <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.7rem;">Standard Rate From</small>
                                @php
                                    $minRate = $celeb->rates->min('standard_fee');
                                @endphp
                                <strong class="text-primary small">
                                    {{ $minRate ? 'INR ' . number_format($minRate / 100000, 1) . 'L' : 'On Request' }}
                                </strong>
                            </div>
                            <a href="{{ route('admin.celebrities.show', $celeb) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                Profile &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-person-x fs-1 text-secondary d-block mb-2"></i>
                <p class="text-muted">No celebrities found matching search query.</p>
            </div>
        @endforelse
    </div>

    @if($celebrities->hasPages())
        <div class="d-flex justify-content-center">
            {{ $celebrities->links() }}
        </div>
    @endif
</div>
@endsection

