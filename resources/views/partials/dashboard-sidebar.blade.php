<aside class="sidebar" id="dashboardSidebar">
    <a href="/" class="sidebar-logo">
        <img src="{{ asset('fitlife-assets/images/uplogo.png') }}" alt="SkillUp Logo" style="width: 140px; height: auto;">
    </a>
    
    <div class="nav-section">
        <div class="nav-title">Overview</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><ion-icon name="grid-outline"></ion-icon> Dashboard</a>
        <a href="{{ route('courses') }}" class="nav-link {{ request()->routeIs('courses*') ? 'active' : '' }}"><ion-icon name="book-outline"></ion-icon> Courses</a>
        <a href="{{ route('paths') }}" class="nav-link {{ request()->routeIs('paths*') ? 'active' : '' }}"><ion-icon name="map-outline"></ion-icon> Paths</a>
        <a href="{{ route('dashboard.chats') }}" class="nav-link {{ request()->routeIs('dashboard.chats') ? 'active' : '' }}"><ion-icon name="chatbubbles-outline"></ion-icon> Chats</a>
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
        <a href="#" class="nav-link"><ion-icon name="settings-outline"></ion-icon> Setting</a>
        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
            @csrf
            <button type="submit" class="nav-link" style="width: 100%; border:none; background:none; cursor:pointer; color: var(--brand-primary);"><ion-icon name="log-out-outline"></ion-icon> Logout</button>
        </form>
    </div>
</aside>
