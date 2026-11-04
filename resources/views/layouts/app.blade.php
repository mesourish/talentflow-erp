<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TalentFlow ERP') - Celebrity & Talent Management Platform</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">

    <!-- Tailwind CSS (preflight: false to preserve Bootstrap layout & navbar) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            corePlugins: {
                preflight: false,
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        /* Global SVG Sizing Guard: Prevents unconstrained SVG expansion across all views */
        svg {
            display: inline-block;
            vertical-align: middle;
            max-width: 100%;
            flex-shrink: 0;
        }

        /* Standard Tailwind SVG dimension rules with whitespace-aware and substring matching */
        svg.w-3, svg[class~="w-3"], svg[class*="w-3"] { width: 0.75rem !important; max-width: 0.75rem !important; }
        svg.h-3, svg[class~="h-3"], svg[class*="h-3"] { height: 0.75rem !important; max-height: 0.75rem !important; }

        svg.w-3\.5, svg[class~="w-3.5"], svg[class*="w-3.5"] { width: 0.875rem !important; max-width: 0.875rem !important; }
        svg.h-3\.5, svg[class~="h-3.5"], svg[class*="h-3.5"] { height: 0.875rem !important; max-height: 0.875rem !important; }

        svg.w-4, svg[class~="w-4"], svg[class*="w-4"] { width: 1rem !important; max-width: 1rem !important; }
        svg.h-4, svg[class~="h-4"], svg[class*="h-4"] { height: 1rem !important; max-height: 1rem !important; }

        svg.w-5, svg[class~="w-5"], svg[class*="w-5"] { width: 1.25rem !important; max-width: 1.25rem !important; }
        svg.h-5, svg[class~="h-5"], svg[class*="h-5"] { height: 1.25rem !important; max-height: 1.25rem !important; }

        svg.w-6, svg[class~="w-6"], svg[class*="w-6"] { width: 1.5rem !important; max-width: 1.5rem !important; }
        svg.h-6, svg[class~="h-6"], svg[class*="h-6"] { height: 1.5rem !important; max-height: 1.5rem !important; }

        svg.w-8, svg[class~="w-8"], svg[class*="w-8"] { width: 2rem !important; max-width: 2rem !important; }
        svg.h-8, svg[class~="h-8"], svg[class*="h-8"] { height: 2rem !important; max-height: 2rem !important; }

        svg.w-10, svg[class~="w-10"], svg[class*="w-10"] { width: 2.5rem !important; max-width: 2.5rem !important; }
        svg.h-10, svg[class~="h-10"], svg[class*="h-10"] { height: 2.5rem !important; max-height: 2.5rem !important; }

        svg.w-12, svg[class~="w-12"], svg[class*="w-12"] { width: 3rem !important; max-width: 3rem !important; }
        svg.h-12, svg[class~="h-12"], svg[class*="h-12"] { height: 3rem !important; max-height: 3rem !important; }

        /* Button & Link Icon Protection: If an icon is placed inside a button or small container without explicit size, prevent it from blowing up */
        button svg:not([class*="w-"]), a svg:not([class*="w-"]), .btn svg:not([class*="w-"]) {
            width: 1.15rem;
            height: 1.15rem;
            max-width: 1.15rem;
            max-height: 1.15rem;
        }

        /* FullCalendar Custom Theme */
        .fc {
            --fc-border-color: #e2e8f0;
            --fc-button-bg-color: #4f46e5;
            --fc-button-border-color: #4f46e5;
            --fc-button-hover-bg-color: #4338ca;
            --fc-button-hover-border-color: #4338ca;
            --fc-button-active-bg-color: #3730a3;
            --fc-button-active-border-color: #3730a3;
            --fc-today-bg-color: #f5f3ff;
            font-family: inherit;
        }
        .fc .fc-toolbar-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
        }
        .fc .fc-button {
            font-size: 0.85rem;
            font-weight: 600;
            padding: 0.4rem 0.85rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .fc .fc-event {
            border-radius: 6px;
            padding: 2px 6px;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0,0,0,0.06);
            border: none !important;
        }
        :root {
            --tf-primary: #1e3a8a;
            --tf-primary-dark: #0f172a;
            --tf-accent: #3b82f6;
            --tf-gold: #f59e0b;
            --tf-bg-light: #f8fafc;
            --tf-sidebar-width: 260px;
            --tf-sidebar-collapsed-width: 72px;
            --tf-header-height: 65px;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--tf-bg-light);
            color: #1e293b;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        /* Top Navbar: Fixed at top of viewport */
        .top-navbar {
            height: var(--tf-header-height);
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
        }

        /* App Layout Wrapper */
        .app-layout-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
            position: relative;
        }

        /* Sidebar: Fixed, full height below header, independent scroll */
        .sidebar {
            width: var(--tf-sidebar-width);
            height: calc(100vh - var(--tf-header-height));
            position: fixed;
            top: var(--tf-header-height);
            left: 0;
            bottom: 0;
            background: #0f172a;
            color: #94a3b8;
            z-index: 1020;
            display: flex;
            flex-direction: column;
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1), transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
        }

        /* Inner scrollable list container for sidebar nav items */
        .sidebar-nav-container {
            flex: 1 1 auto;
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: thin;
            scrollbar-color: #334155 transparent;
        }

        .sidebar-nav-container::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-nav-container::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-nav-container::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }
        .sidebar-nav-container::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }

        .sidebar .nav-link {
            color: #cbd5e1;
            font-size: 0.88rem;
            font-weight: 500;
            padding: 0.65rem 1.15rem;
            display: flex;
            align-items: center;
            border-radius: 8px;
            margin: 2px 10px;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .sidebar .nav-link i {
            font-size: 1.15rem;
            margin-right: 0.85rem;
            width: 22px;
            text-align: center;
            flex-shrink: 0;
        }

        .sidebar .nav-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
        }

        .sidebar .nav-link.active {
            color: #ffffff;
            background: #2563eb;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }

        .sidebar-heading {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            padding: 1.15rem 1.25rem 0.4rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .sidebar-footer {
            flex-shrink: 0;
            background: #09101d;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
            padding: 0.65rem 0.75rem;
        }

        .sidebar-footer .collapse-icon {
            transition: transform 0.25s ease;
        }

        /* Content Wrapper: Margin accounts for fixed sidebar */
        .content-wrapper {
            margin-left: var(--tf-sidebar-width);
            padding-top: var(--tf-header-height);
            min-height: 100vh;
            width: calc(100% - var(--tf-sidebar-width));
            display: flex;
            flex-direction: column;
            transition: margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1), width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .main-content {
            flex: 1 0 auto;
            padding: 1.75rem;
            background-color: var(--tf-bg-light);
            min-width: 0;
        }

        /* Collapsed Sidebar Styles (Desktop) */
        body.sidebar-collapsed .sidebar {
            width: var(--tf-sidebar-collapsed-width);
        }
        body.sidebar-collapsed .content-wrapper {
            margin-left: var(--tf-sidebar-collapsed-width);
            width: calc(100% - var(--tf-sidebar-collapsed-width));
        }
        body.sidebar-collapsed .sidebar-heading,
        body.sidebar-collapsed .sidebar .nav-text,
        body.sidebar-collapsed .sidebar-footer .nav-text {
            display: none !important;
        }
        body.sidebar-collapsed .sidebar .nav-link {
            justify-content: center;
            padding: 0.7rem 0.5rem;
            margin: 3px 8px;
        }
        body.sidebar-collapsed .sidebar .nav-link i {
            margin-right: 0 !important;
            font-size: 1.25rem;
        }
        body.sidebar-collapsed .collapse-icon {
            transform: rotate(180deg);
        }

        /* Responsive Mobile Drawer */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                width: var(--tf-sidebar-width) !important;
                box-shadow: none;
            }
            .content-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
            }
            body.sidebar-open .sidebar {
                transform: translateX(0);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
            }
            .sidebar-backdrop {
                position: fixed;
                inset: 0;
                top: var(--tf-header-height);
                background: rgba(15, 23, 42, 0.5);
                z-index: 1019;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.25s ease;
                backdrop-filter: blur(2px);
            }
            body.sidebar-open .sidebar-backdrop {
                opacity: 1;
                pointer-events: auto;
            }
        }

        .card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);
            background: #ffffff;
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
            padding: 1rem 1.25rem;
        }

        .kpi-card {
            border-radius: 12px;
            padding: 1.25rem;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.07);
        }

        .kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }

        .table thead th {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            background: #f8fafc;
            border-top: none;
            padding: 0.75rem 1rem;
        }

        .badge-status {
            font-weight: 600;
            font-size: 0.75rem;
            padding: 0.35em 0.75em;
            border-radius: 20px;
        }

        .avatar-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
    @stack('styles')
</head>
<body>
    <script>
        (function() {
            try {
                if (localStorage.getItem('talentflow_sidebar_collapsed') === 'true' && window.innerWidth >= 992) {
                    document.body.classList.add('sidebar-collapsed');
                }
            } catch(e) {}
        })();
    </script>

    @include('layouts.header')

    <div class="app-layout-wrapper">
        @include('layouts.sidebar')
        <div id="sidebarBackdrop" class="sidebar-backdrop"></div>

        <div class="content-wrapper">
            <main class="main-content">
                @include('layouts.breadcrumb')

                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                        <i class="bi bi-exclamation-circle-fill fs-5 me-2"></i>
                        <div>{{ session('warning') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(isset($errors) && $errors->any())
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <h6 class="alert-heading fw-bold mb-1"><i class="bi bi-x-circle-fill me-1"></i> Please check form inputs:</h6>
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>

            @include('layouts.footer')
        </div>
    </div>

    <!-- Scripts: jQuery, Bootstrap 5, Chart.js, FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Sidebar Toggle and Collapse Controller
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            const collapseBtn = document.getElementById('sidebarCollapseBtn');
            const backdrop = document.getElementById('sidebarBackdrop');

            function toggleSidebar() {
                if (window.innerWidth >= 992) {
                    document.body.classList.toggle('sidebar-collapsed');
                    const isCollapsed = document.body.classList.contains('sidebar-collapsed');
                    try {
                        localStorage.setItem('talentflow_sidebar_collapsed', isCollapsed ? 'true' : 'false');
                    } catch(e) {}
                } else {
                    document.body.classList.toggle('sidebar-open');
                }

                if (window.bookingCalendarInstance) {
                    setTimeout(() => window.bookingCalendarInstance.updateSize(), 260);
                }
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', toggleSidebar);
            }
            if (collapseBtn) {
                collapseBtn.addEventListener('click', toggleSidebar);
            }
            if (backdrop) {
                backdrop.addEventListener('click', function() {
                    document.body.classList.remove('sidebar-open');
                });
            }

            // Auto-close drawer on link click in mobile view
            document.querySelectorAll('#appSidebar .nav-link').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 992) {
                        document.body.classList.remove('sidebar-open');
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>

