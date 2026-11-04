<header class="top-navbar d-flex align-items-center justify-content-between px-3 px-md-4">
    <div class="d-flex align-items-center">
        <button id="sidebarToggleBtn" class="btn btn-sm btn-light border rounded-lg me-3 d-flex align-items-center justify-content-center p-1.5 shadow-none" type="button" aria-label="Toggle navigation" title="Toggle Sidebar">
            <i class="bi bi-list fs-5 text-dark"></i>
        </button>
        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none d-flex align-items-center me-3 me-md-4">
            <span class="badge bg-primary fs-6 me-2 p-2"><i class="bi bi-stars"></i></span>
            <span class="fs-5 fw-bold text-dark tracking-tight">TalentFlow <span class="text-primary">ERP</span></span>
        </a>
        <span class="badge bg-light text-secondary border d-none d-lg-inline-block px-2 py-1">
            <i class="bi bi-shield-check text-success me-1"></i> Celebrity & Talent Enterprise
        </span>
    </div>

    <div class="d-flex align-items-center gap-3">
        {{-- Quick Demo Role Switcher --}}
        @php
            $currentUser = auth()->user();
            $allUsers = \App\Models\User::with('roles')->get();
        @endphp
        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle rounded-pill px-3 py-1 d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-arrow-repeat text-primary me-2"></i>
                <span class="d-none d-sm-inline text-muted me-1">Role:</span>
                <strong class="text-dark">{{ $currentUser ? $currentUser->getRoleNames()->first() : 'Guest' }}</strong>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2" style="min-width: 280px;">
                <li class="dropdown-header fw-bold text-uppercase fs-xs">Switch Demo Role (1-Click)</li>
                @foreach($allUsers as $u)
                    <li>
                        <a class="dropdown-item rounded d-flex align-items-center py-2 {{ $currentUser && $currentUser->id === $u->id ? 'active bg-primary text-white' : '' }}" href="{{ route('quick-login', $u) }}">
                            <div class="avatar-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2 fs-xs" style="width: 28px; height: 28px;">
                                {{ substr($u->name, 0, 1) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold lh-1 fs-sm">{{ $u->name }}</div>
                                <small class="text-muted fs-xs {{ $currentUser && $currentUser->id === $u->id ? 'text-white-50' : '' }}">{{ $u->getRoleNames()->first() }}</small>
                            </div>
                            @if($currentUser && $currentUser->id === $u->id)
                                <i class="bi bi-check-lg ms-2"></i>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Expiring Contracts & Notifications Pill --}}
        @php
            $expiringCount = \App\Models\Contract::where('status', 'active')
                ->whereNotNull('expiry_date')
                ->where('expiry_date', '<=', now()->addDays(30))
                ->where('expiry_date', '>=', now())
                ->count();
        @endphp
        <a href="{{ route('admin.contracts.index', ['expiring_soon' => 1]) }}" class="btn btn-light position-relative p-2 rounded-circle border" title="Expiring Contracts Alerts">
            <i class="bi bi-bell fs-5 text-secondary"></i>
            @if($expiringCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light">
                    {{ $expiringCount }}
                </span>
            @endif
        </a>

        {{-- User Profile & Logout --}}
        <div class="dropdown">
            <button class="btn btn-light rounded-pill border py-1 ps-2 pe-3 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.85rem;">
                    {{ $currentUser ? substr($currentUser->name, 0, 1) : 'U' }}
                </div>
                <div class="text-start d-none d-lg-block">
                    <div class="fw-bold fs-sm lh-1">{{ $currentUser?->name }}</div>
                    <small class="text-muted fs-xs">{{ $currentUser?->email }}</small>
                </div>
                <i class="bi bi-chevron-down fs-xs text-muted"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2">
                <li><span class="dropdown-item-text text-muted fs-xs">Signed in as <strong>{{ $currentUser?->email }}</strong></span></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}"><i class="bi bi-gear me-2"></i> System Settings</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> Sign Out</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

