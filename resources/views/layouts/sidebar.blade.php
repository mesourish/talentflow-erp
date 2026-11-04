<aside class="sidebar" id="appSidebar">
    <div class="sidebar-nav-container py-2">
        <ul class="nav flex-column mb-auto">
            <li class="sidebar-heading">Core System</li>
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" title="Dashboard">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            <li class="sidebar-heading">CRM & Pipeline</li>
            <li class="nav-item">
                <a href="{{ route('admin.clients.index') }}" class="nav-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}" title="Brands & Clients">
                    <i class="bi bi-building"></i>
                    <span class="nav-text">Brands & Clients</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.leads.index') }}" class="nav-link {{ request()->routeIs('admin.leads.*') ? 'active' : '' }}" title="Sales Leads">
                    <i class="bi bi-funnel"></i>
                    <span class="nav-text">Sales Leads</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.activities.index') }}" class="nav-link {{ request()->routeIs('admin.activities.*') ? 'active' : '' }}" title="CRM Activities">
                    <i class="bi bi-telephone-outbound"></i>
                    <span class="nav-text">CRM Activities</span>
                </a>
            </li>

            <li class="sidebar-heading">Celebrity & Talent</li>
            <li class="nav-item">
                <a href="{{ route('admin.celebrities.index') }}" class="nav-link {{ request()->routeIs('admin.celebrities.*') ? 'active' : '' }}" title="Talent Roster">
                    <i class="bi bi-person-badge"></i>
                    <span class="nav-text">Talent Roster</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" title="Talent Categories">
                    <i class="bi bi-tags"></i>
                    <span class="nav-text">Talent Categories</span>
                </a>
            </li>

            <li class="sidebar-heading">Commercials & Workflows</li>
            <li class="nav-item">
                <a href="{{ route('admin.quotations.index') }}" class="nav-link {{ request()->routeIs('admin.quotations.*') ? 'active' : '' }}" title="Quotations">
                    <i class="bi bi-file-earmark-spreadsheet"></i>
                    <span class="nav-text">Quotations</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.projects.index') }}" class="nav-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}" title="Campaigns & Projects">
                    <i class="bi bi-kanban"></i>
                    <span class="nav-text">Campaigns & Projects</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.bookings.index') }}" class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" title="Booking Calendar">
                    <i class="bi bi-calendar-event"></i>
                    <span class="nav-text">Booking Calendar</span>
                </a>
            </li>

            <li class="sidebar-heading">Legal & Compliance</li>
            <li class="nav-item">
                <a href="{{ route('admin.contracts.index') }}" class="nav-link {{ request()->routeIs('admin.contracts.*') ? 'active' : '' }}" title="Contracts & Rights">
                    <i class="bi bi-file-earmark-lock"></i>
                    <span class="nav-text">Contracts & Rights</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.documents.index') }}" class="nav-link {{ request()->routeIs('admin.documents.*') ? 'active' : '' }}" title="Secure DMS">
                    <i class="bi bi-folder2-open"></i>
                    <span class="nav-text">Secure DMS</span>
                </a>
            </li>

            <li class="sidebar-heading">Finance & Accounting</li>
            <li class="nav-item">
                <a href="{{ route('admin.claims.index') }}" class="nav-link {{ request()->routeIs('admin.claims.*') ? 'active' : '' }}" title="Claims & Expenses">
                    <i class="bi bi-receipt"></i>
                    <span class="nav-text">Claims & Expenses</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.invoices.index') }}" class="nav-link {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}" title="Client Invoices">
                    <i class="bi bi-receipt-cutoff"></i>
                    <span class="nav-text">Client Invoices</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.commissions.index') }}" class="nav-link {{ request()->routeIs('admin.commissions.*') ? 'active' : '' }}" title="Commissions & Payouts">
                    <i class="bi bi-cash-stack"></i>
                    <span class="nav-text">Commissions & Payouts</span>
                </a>
            </li>

            <li class="sidebar-heading">Analytics & Forecasts</li>
            <li class="nav-item">
                <a href="{{ route('admin.reports.forecast') }}" class="nav-link {{ request()->routeIs('admin.reports.forecast') ? 'active' : '' }}" title="Revenue Forecast">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span class="nav-text">Revenue Forecast</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.reports.projects') }}" class="nav-link {{ request()->routeIs('admin.reports.projects') ? 'active' : '' }}" title="Project Margins">
                    <i class="bi bi-pie-chart"></i>
                    <span class="nav-text">Project Margins</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.reports.finance') }}" class="nav-link {{ request()->routeIs('admin.reports.finance') ? 'active' : '' }}" title="Financial Ledger">
                    <i class="bi bi-wallet2"></i>
                    <span class="nav-text">Financial Ledger</span>
                </a>
            </li>

            <li class="sidebar-heading">Administration</li>
            <li class="nav-item">
                <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" title="Access & Settings">
                    <i class="bi bi-sliders"></i>
                    <span class="nav-text">Access & Settings</span>
                </a>
            </li>
        </ul>
    </div>

    {{-- Bottom Collapse Toggle (Desktop) --}}
    <div class="sidebar-footer">
        <button id="sidebarCollapseBtn" type="button" class="btn btn-sm w-100 d-flex align-items-center justify-content-center text-secondary py-2 border-0" title="Collapse / Expand Sidebar">
            <i class="bi bi-chevron-bar-left fs-6 collapse-icon"></i>
            <span class="nav-text ms-2 small fw-semibold">Collapse</span>
        </button>
    </div>
</aside>
