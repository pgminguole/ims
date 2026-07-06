<div class="sidebar-modern">
    <!-- Logo Area -->
    <a href="{{ optional(auth()->user()->assignedRole)->name === 'auditor' ? route('auditor.dashboard') : route('dashboard') }}" class="sidebar-logo-area text-decoration-none">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="sidebar-logo-img">
        <span class="sidebar-brand">Asset Manager</span>
    </a>

    <!-- Navigation -->
    <nav class="flex-grow-1 overflow-auto" style="scrollbar-width: thin;">
        
        @if(auth()->check() && optional(auth()->user()->assignedRole)->name === 'auditor')
            <!-- Auditor Menu -->
            <div class="nav-section-title">Audit</div>
            <a href="{{ route('auditor.dashboard') }}" class="nav-item-modern {{ request()->routeIs('auditor.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            
            <a href="{{ route('auditor.reports.index') }}" class="nav-item-modern {{ request()->routeIs('auditor.reports.*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i> Audit Reports
            </a>

            <div class="nav-section-title">Management</div>
            <a href="{{ route('auditor.assets.index') }}" class="nav-item-modern {{ request()->routeIs('auditor.assets.*') ? 'active' : '' }}">
                <i class="fas fa-laptop"></i> Assets
            </a>
        @else
            <!-- Regular User Menu -->
            <div class="nav-section-title">Overview</div>
            <a href="{{ route('dashboard') }}" class="nav-item-modern {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            
            <a href="{{ route('reports') }}" class="nav-item-modern {{ request()->routeIs('reports') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i> Reports
            </a>

            <div class="nav-section-title">Asset Tracking</div>
            <a href="{{ route('assets.index') }}" class="nav-item-modern {{ request()->routeIs('assets.*') ? 'active' : '' }}">
                <i class="fas fa-boxes"></i> Inventory
            </a>
            
            <a href="{{ route('assignments.index') }}" class="nav-item-modern {{ request()->routeIs('assignments.*') ? 'active' : '' }}">
                <i class="fas fa-user-check"></i> Official Assignments
            </a>

            <a href="{{ route('dts-assignments.index') }}" class="nav-item-modern {{ request()->routeIs('dts-assignments.*') ? 'active' : '' }}">
                <i class="fas fa-project-diagram"></i> DTS Assignments
            </a>

            <a href="{{ route('obsolete-assets.index') }}" class="nav-item-modern {{ request()->routeIs('obsolete-assets.*') ? 'active' : '' }}">
                <i class="fas fa-archive"></i> Obsolete Items
            </a>

            <div class="nav-section-title">Administration</div>
            @can('view_users')
            <a href="{{ route('users') }}" class="nav-item-modern {{ request()->routeIs('users') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Users
            </a>
            @endcan
            
            @can('edit_users')
            <a href="{{ route('users.fix-roles') }}" class="nav-item-modern {{ request()->routeIs('users.fix-roles*') ? 'active' : '' }}">
                <i class="fas fa-user-cog"></i> Fix Roles
            </a>
            @endcan
            
            @can('view_courts')
            <a href="{{ route('courts') }}" class="nav-item-modern {{ request()->routeIs('courts') ? 'active' : '' }}">
                <i class="fas fa-gavel"></i> Courts
            </a>
            @endcan

            @can('view_offices')
            <a href="{{ route('offices.index') }}" class="nav-item-modern {{ request()->routeIs('offices.*') ? 'active' : '' }}">
                <i class="fas fa-building"></i> Departments
            </a>
            @endcan
            
            @can('view_locations')
            <a href="{{ route('locations') }}" class="nav-item-modern {{ request()->routeIs('locations') ? 'active' : '' }}">
                <i class="fas fa-map-marker-alt"></i> Locations
            </a>
            @endcan

            @if(auth()->user()->hasRole('super_admin'))
            <a href="{{ route('regional-admins.index') }}" class="nav-item-modern {{ request()->routeIs('regional-admins.*') ? 'active' : '' }}">
                <i class="fas fa-user-shield"></i> Regional ICT Admins
            </a>
            @endif
            
            <div class="nav-section-title">Configuration</div>
            
            <a href="{{ route('categories') }}" class="nav-item-modern {{ request()->routeIs('categories') ? 'active' : '' }}">
                <i class="fas fa-tags"></i> Categories
            </a>
            
            <a href="{{ route('regions.index') }}" class="nav-item-modern {{ request()->routeIs('regions.*') ? 'active' : '' }}">
                <i class="fas fa-map"></i> Regions
            </a>
            
            <a href="{{ route('settings.index') }}" class="nav-item-modern {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="fas fa-cogs"></i> Settings
            </a>
        @endif
        
    </nav>

    <!-- User Profile (Bottom) -->
    <div class="mt-auto pt-3 border-top border-light">
        <div class="d-flex align-items-center gap-2 px-2">
            <div class="user-avatar-sm rounded-circle bg-warning d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; font-size: 0.8rem;">
                {{ substr(auth()->user()->first_name, 0, 1) }}
            </div>
            <div class="overflow-hidden">
                <div class="text-dark fw-bold text-small text-truncate">{{ auth()->user()->first_name }}</div>
                <div class="text-muted text-tiny text-truncate">{{ optional(auth()->user()->assignedRole)->name ?? 'User' }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="ms-auto">
                @csrf
                <button type="submit" class="btn btn-link text-muted p-0" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</div>