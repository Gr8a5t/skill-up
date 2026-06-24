<header class="header {{ request()->routeIs('home') ? '' : 'active' }}" @if (request()->routeIs('home')) data-header @endif>
    <div class="container">
        <a href="/" class="logo">
            

            
            <img src="{{ asset('fitlife-assets/images/uplogo.png') }}" alt="">
        </a>

        <nav class="navbar" data-navbar>

            <button class="nav-close-btn" aria-label="close menu" data-nav-toggler>
                <ion-icon name="close-sharp" aria-hidden="true"></ion-icon>
            </button>

            <ul class="navbar-list">
                <li>
                    <a href="/" class="navbar-link {{ request()->routeIs('home') ? 'active' : '' }}" data-nav-link>
                        Home
                    </a>
                </li>
                <li>
                    <a href="{{ route('about') }}" class="navbar-link {{ request()->routeIs('about') ? 'active' : '' }}" data-nav-link>About Us</a>
                </li>
                <li>
                    <a href="{{ route('paths') }}" class="navbar-link {{ request()->routeIs('paths') ? 'active' : '' }}"
                        data-nav-link>Paths</a>
                </li>
                <li>
                    <a href="{{ route('courses') }}"
                        class="navbar-link {{ request()->routeIs('courses*') ? 'active' : '' }}" data-nav-link>
                        Courses
                    </a>
                </li>
                <li>
                    <a href="{{ route('dashboard.chats') }}" class="navbar-link {{ request()->routeIs('dashboard.chats') ? 'active' : '' }}" data-nav-link>Chats</a>
                </li>
                <li>
                    <a href="#" class="navbar-link" data-nav-link style="display: flex; align-items: center; gap: 6px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                            <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                            <path d="M3 3v5h5" />
                            <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16" />
                            <path d="M16 21v-5h5" />
                            <path d="M14 4.5 Q14 8.5 18 8.5 Q14 8.5 14 12.5 Q14 8.5 10 8.5 Q14 8.5 14 4.5 Z" fill="currentColor" stroke="none" />
                            <path d="M8.5 12.5 Q8.5 14.5 10.5 14.5 Q8.5 14.5 8.5 16.5 Q8.5 14.5 6.5 14.5 Q8.5 14.5 8.5 12.5 Z" fill="currentColor" stroke="none" />
                        </svg>
                        <span>AI</span>
                    </a>
                </li>
            </ul>

        </nav>

        <div class="header-actions" style="display: flex; gap: 15px; align-items: center;">
            @if(request()->routeIs('home'))
                <a href="{{ route('register') }}" class="btn btn-secondary" style="padding: 8px 20px; font-size: 1.4rem;">Join SkillUp</a>
            @else
                @auth
                    <span class="auth-username" style="font-size: 1.4rem; font-weight: 700; color: var(--oxford-blue);">Hi, {{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn btn-secondary" style="padding: 8px 20px; font-size: 1.4rem;">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="navbar-link" style="font-weight: 700; color: var(--oxford-blue); text-decoration: none; font-size: 1.5rem;">Log In</a>
                    <a href="{{ route('register') }}" class="btn btn-secondary" style="padding: 8px 20px; font-size: 1.4rem;">Join</a>
                @endauth
            @endif
        </div>

        <button class="nav-open-btn" aria-label="open menu" data-nav-toggler>
            <span class="line"></span>
            <span class="line"></span>
            <span class="line"></span>
        </button>

    </div>
</header>
