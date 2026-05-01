<header class="topbar">
    <div class="search-bar">
        <ion-icon name="search-outline"></ion-icon>
        <input type="text" class="search-input" placeholder="Search and learn....">
    </div>
    <div class="topbar-right">
        <a href="{{ route('dashboard.chats') }}" class="icon-btn {{ request()->routeIs('dashboard.chats') ? 'active' : '' }}"><ion-icon name="chatbubbles-outline"></ion-icon></a>
        <a href="#" class="icon-btn"><ion-icon name="notifications-outline"></ion-icon></a>
        <div class="user-profile">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <span class="user-name">{{ auth()->user()->name }}</span>
        </div>
    </div>
</header>
