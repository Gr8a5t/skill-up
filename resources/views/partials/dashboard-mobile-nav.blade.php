<nav class="mobile-nav">
    <a href="{{ route('dashboard') }}" class="mobile-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <ion-icon name="grid-outline"></ion-icon>
        <span>Home</span>
    </a>
    <a href="{{ route('courses') }}" class="mobile-nav-link {{ request()->routeIs('courses') ? 'active' : '' }}">
        <ion-icon name="book-outline"></ion-icon>
        <span>Courses</span>
    </a>
    <a href="{{ route('paths') }}" class="mobile-nav-link {{ request()->routeIs('paths') ? 'active' : '' }}">
        <ion-icon name="map-outline"></ion-icon>
        <span>Paths</span>
    </a>
    <a href="{{ route('dashboard.forum') }}" class="mobile-nav-link {{ request()->routeIs('dashboard.forum') ? 'active' : '' }}">
        <ion-icon name="people-outline"></ion-icon>
        <span>Forum</span>
    </a>
    <a href="javascript:void(0)" class="mobile-nav-link" id="mobileMoreBtn">
        <ion-icon name="menu-outline"></ion-icon>
        <span>More</span>
    </a>
</nav>
