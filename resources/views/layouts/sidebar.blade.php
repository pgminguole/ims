<div class="sidebar">
    <div class="sidebar-content">
        <!-- Logo -->
        <a href="{{ route('dashboard') }}" class="sidebar-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </a>

        <!-- Navigation Menu -->
        <nav class="sidebar-nav">
            <ul class="nav-list">
                <!-- Dashboard -->
                <li class="nav-item active">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Reports -->
                <li class="nav-item">
                    <a href="{{ route('reports') }}" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reports</span>
                    </a>
                </li>

                <!-- Assets Management -->
                <li class="nav-item has-submenu">
                    <a href="#assetsMenu" class="nav-link" data-toggle="collapse">
                        <i class="fas fa-laptop"></i>
                        <span>Assets</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    <ul class="submenu collapse" id="assetsMenu">
                        <li><a href="{{ route('assets.index') }}">All Assets</a></li>
                        <li><a href="{{ route('assets.create') }}">Add New Asset</a></li>
                        <li><a href="{{ route('assets.assigned') }}">Assigned Assets</a></li>
                        <li><a href="{{ route('assets.available') }}">Available Assets</a></li>
                    </ul>
                </li>

                <!-- Asset Assignments -->
                <li class="nav-item">
                    <a href="{{ route('assignments.index') }}" class="nav-link">
                        <i class="fas fa-user-check"></i>
                        <span>Asset Assignments</span>
                    </a>
                </li>

                <!-- Settings -->
                <li class="nav-item has-submenu">
                    <a href="#settingsMenu" class="nav-link" data-toggle="collapse">
                        <i class="fas fa-cogs"></i>
                        <span>Settings</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    <ul class="submenu collapse" id="settingsMenu">
                        <li><a href="{{ route('categories') }}">Categories</a></li>
                        <li><a href="{{ route('regions.index') }}">Regions</a></li>
                        <li><a href="{{ route('courts') }}">Courts</a></li>
                        <li><a href="{{ route('users') }}">Users</a></li>
                        <li><a href="{{ route('locations') }}">Locations</a></li>
                    </ul>
                </li>
            </ul>
        </nav>

        <!-- Toggle Button -->
        <button type="button" class="btn btn-link sidebar-mini-btn text-muted">
            <span><i class="icofont-bubble-right"></i></span>
        </button>

        <!-- User Info -->
        <div class="sidebar-user">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="user-details">
                    <span class="user-label">Logged in as</span>
                    <span class="user-name">{{ auth()->user()->first_name }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.sidebar {
    width: 260px;
    height: 100vh;
    background: linear-gradient(180deg, #1a3a52 0%, #0d1f2d 100%);
    position: fixed;
    left: 0;
    top: 0;
    display: flex;
    flex-direction: column;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    z-index: 1000;
}

.sidebar-content {
    display: flex;
    flex-direction: column;
    height: 100%;
    padding: 0;
}

.sidebar-logo {
    padding: 20px 24px;
    display: block;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-logo img {
    width: 100%;
    height: auto;
    display: block;
    max-width: 200px;
}

.sidebar-nav {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 16px 0;
}

.sidebar-nav::-webkit-scrollbar {
    width: 4px;
}

.sidebar-nav::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar-nav::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
}

.nav-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.nav-item {
    margin: 4px 12px;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 10px 14px;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-size: 13px;
    font-weight: 500;
    position: relative;
}

.nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
    text-decoration: none;
}

.nav-item.active .nav-link {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff;
}

.nav-link i {
    width: 18px;
    font-size: 14px;
    margin-right: 10px;
    text-align: center;
}

.nav-link span {
    flex: 1;
}

.submenu-arrow {
    font-size: 10px;
    margin-left: auto;
    transition: transform 0.3s ease;
}

.nav-link[aria-expanded="true"] .submenu-arrow {
    transform: rotate(180deg);
}

.submenu {
    list-style: none;
    padding: 0;
    margin: 4px 0 0 0;
    background: rgba(0, 0, 0, 0.2);
    border-radius: 8px;
    overflow: hidden;
}

.submenu li {
    margin: 0;
}

.submenu a {
    display: block;
    padding: 8px 14px 8px 42px;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    font-size: 12px;
    transition: all 0.2s ease;
}

.submenu a:hover {
    background: rgba(255, 255, 255, 0.05);
    color: #ffffff;
    text-decoration: none;
}

.sidebar-mini-btn {
    margin: 12px;
    padding: 8px;
    color: rgba(255, 255, 255, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    transition: all 0.3s ease;
}

.sidebar-mini-btn:hover {
    background: rgba(255, 255, 255, 0.05);
    color: rgba(255, 255, 255, 0.8);
    border-color: rgba(255, 255, 255, 0.2);
}

.sidebar-user {
    padding: 16px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(0, 0, 0, 0.15);
}

.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.user-avatar i {
    font-size: 24px;
    color: rgba(255, 255, 255, 0.8);
}

.user-details {
    display: flex;
    flex-direction: column;
    flex: 1;
}

.user-label {
    font-size: 11px;
    color: rgba(255, 255, 255, 0.5);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.user-name {
    font-size: 14px;
    color: #ffffff;
    font-weight: 600;
    margin-top: 2px;
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }
    
    .sidebar.show {
        transform: translateX(0);
    }
}
</style>