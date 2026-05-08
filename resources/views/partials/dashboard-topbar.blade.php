<header class="topbar">
    <form action="{{ route('courses') }}" method="GET" class="search-bar">
        <ion-icon name="search-outline"></ion-icon>
        <input type="text" name="search" class="search-input" placeholder="Search and learn...." value="{{ request('search') }}">
    </form>
    <div class="topbar-right">
        <a href="{{ route('dashboard.chats') }}" class="icon-btn {{ request()->routeIs('dashboard.chats') ? 'active' : '' }}"><ion-icon name="chatbubbles-outline"></ion-icon></a>
        <a href="#" class="icon-btn"><ion-icon name="notifications-outline"></ion-icon></a>
        <a href="{{ route('profile.show', auth()->user()) }}" class="user-profile" style="text-decoration:none;">
            <div class="user-avatar" style="overflow:hidden;">
                @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" alt="Avatar" style="width:100%; height:100%; object-fit:cover;">
                @else
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                @endif
            </div>
            <span class="user-name">{{ auth()->user()->name }}</span>
        </a>
    </div>
</header>
