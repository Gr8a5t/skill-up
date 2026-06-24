<header class="topbar">
    <form action="{{ route('courses') }}" method="GET" class="search-bar">
        <ion-icon name="search-outline"></ion-icon>
        <input type="text" name="search" class="search-input" placeholder="Search and learn...." value="{{ request('search') }}">
    </form>
    <div class="topbar-right">
        <a href="{{ route('dashboard.chats') }}" class="icon-btn {{ request()->routeIs('dashboard.chats') ? 'active' : '' }}" style="position: relative;">
            <ion-icon name="chatbubbles-outline"></ion-icon>
            <livewire:chat-badge />
        </a>
        <a href="#" class="icon-btn" title="AI Assistant">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 1.8rem; height: 1.8rem;">
                <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                <path d="M3 3v5h5" />
                <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16" />
                <path d="M16 21v-5h5" />
                <path d="M14 4.5 Q14 8.5 18 8.5 Q14 8.5 14 12.5 Q14 8.5 10 8.5 Q14 8.5 14 4.5 Z" fill="currentColor" stroke="none" />
                <path d="M8.5 12.5 Q8.5 14.5 10.5 14.5 Q8.5 14.5 8.5 16.5 Q8.5 14.5 6.5 14.5 Q8.5 14.5 8.5 12.5 Z" fill="currentColor" stroke="none" />
            </svg>
        </a>
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
