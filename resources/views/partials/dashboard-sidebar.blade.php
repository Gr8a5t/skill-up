<aside class="sidebar" id="dashboardSidebar">
    <div style="display: flex; align-items: center; justify-content: space-between; padding: 24px var(--padding-side) 20px;">
        <a href="/" class="sidebar-logo" style="padding: 0; margin: 0;">
            <img src="{{ asset('fitlife-assets/images/uplogo.png') }}" alt="SkillUp Logo" class="sidebar-logo-img" style="width: 120px; height: auto;">
        </a>
        <button id="sidebarToggleBtn" style="background: none; border: none; color: var(--text-mut); cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 4px; transition: 0.2s;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg>
        </button>
    </div>
    
    <div class="nav-section">
        <div class="nav-title">Overview</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><ion-icon name="grid-outline"></ion-icon> <span class="nav-text">Dashboard</span></a>
        <a href="{{ route('courses') }}" class="nav-link {{ request()->routeIs('courses*') ? 'active' : '' }}"><ion-icon name="book-outline"></ion-icon> <span class="nav-text">Courses</span></a>
        <a href="{{ route('paths') }}" class="nav-link {{ request()->routeIs('paths*') ? 'active' : '' }}"><ion-icon name="map-outline"></ion-icon> <span class="nav-text">Paths</span></a>
        <a href="{{ route('dashboard.forum') }}" class="nav-link {{ request()->routeIs('dashboard.forum') ? 'active' : '' }}"><ion-icon name="people-outline"></ion-icon> <span class="nav-text">Forum</span></a>
    </div>
    
    <div class="nav-section">
        <div class="nav-title">Friends</div>
        <div class="friends-list">
            <a href="#" class="friend-item">
                <div class="friend-avatar">BM</div>
                <div class="friend-info">
                    <span class="friend-name">Bagas Mahpie</span>
                    <span class="friend-role">Friend</span>
                </div>
            </a>
            <a href="#" class="friend-item">
                <div class="friend-avatar">SD</div>
                <div class="friend-info">
                    <span class="friend-name">Sir Dandy</span>
                    <span class="friend-role">Old Friend</span>
                </div>
            </a>
        </div>
    </div>
    
    <div class="sidebar-bottom">
        <div class="nav-title">Settings</div>
        <a href="#" class="nav-link"><ion-icon name="settings-outline"></ion-icon> <span class="nav-text">Setting</span></a>
        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
            @csrf
            <button type="submit" class="nav-link" style="width: 100%; border:none; background:none; cursor:pointer; color: var(--brand-primary);"><ion-icon name="log-out-outline"></ion-icon> <span class="nav-text">Logout</span></button>
        </form>
    </div>
</aside>
