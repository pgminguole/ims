<div class="header mb-4 no-print">
    <div class="header-inner stunning-card d-flex align-items-center justify-content-between">
        <!-- Left Side: Toggle and Welcome -->
        <div class="d-flex align-items-center gap-3">
            <!-- Sidebar Toggle -->
            <button class="p-2 border-0 bg-transparent menu-toggle sidebar-mini-btn text-muted hover-bg-light rounded-3 transition-all" type="button">
                <i class="fa-solid fa-bars-staggered" style="font-size: 1.2rem;"></i>
            </button>

            <!-- Welcome Text -->
            <div class="welcome-text ps-3 d-flex flex-column justify-content-center">
                <span class="fw-bold text-dark lh-1 mb-1" style="font-size: 1.1rem; letter-spacing: -0.5px;">
                    Hi, {{ Auth::user()->first_name ?? 'User' }}
                </span>
                <span class="text-tiny text-muted text-uppercase fw-bold ls-1 text-nowrap">
                    Welcome to Asset Manager
                </span>
            </div>
        </div>

        <!-- Right Side: User Profile -->
        <div class="d-flex align-items-center">
            <div class="dropdown user-profile">
                <a class="nav-link dropdown-toggle d-flex align-items-center text-decoration-none dropdown-toggle-hide-arrow gap-3" href="#" role="button" data-bs-toggle="dropdown">
                    <div class="text-end d-none d-lg-block">
                        <div class="fw-bold text-dark text-small lh-1 mb-1">{{ Auth::user()->full_name }}</div>
                        <div class="badge-gold-light text-tiny text-uppercase fw-bold ls-1">{{ Auth::user()->assignedRole->name ?? 'User' }}</div>
                    </div>
                    <div class="avatar-wrapper position-relative">
                        <img src="{{ asset('images/profile.png') }}" class="avatar rounded-circle border border-2 border-white shadow-sm" alt="profile" style="width: 42px; height: 42px; object-fit: cover;">
                        <span class="status-indicator online"></span>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2 mt-3" style="min-width: 260px;">
                    <li class="px-2 py-3 border-bottom mb-2 bg-light rounded-4">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('images/profile.png') }}" class="avatar sm rounded-circle me-3 border border-2 border-white shadow-sm" alt="profile" style="width: 48px; height: 48px;">
                            <div class="overflow-hidden">
                                <div class="fw-bold text-dark text-small text-truncate mb-0">{{ Auth::user()->full_name }}</div>
                                <div class="text-tiny text-muted text-truncate">{{ Auth::user()->email }}</div>
                            </div>
                        </div>
                    </li>
                    <li><a class="dropdown-item rounded-3 text-small py-2 mb-1 fw-bold" href="#"><i class="fas fa-user-circle me-2 text-primary"></i> My Profile</a></li>
                    <li><hr class="dropdown-divider opacity-50"></li>
                    <li>
                        <a class="dropdown-item rounded-3 text-small py-2 fw-bold text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i> Sign Out
                        </a>
                    </li>
                </ul>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>