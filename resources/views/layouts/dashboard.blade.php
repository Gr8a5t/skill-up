<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | SkillUp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon4.png?v=2') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('fitlife-assets/css/style.css') }}">
    @include('partials.google-tag')
    <style>
        :root {
            --bg-body: #f6f7f8;
            --bg-surface: #ffffff;
            --text-main: #1c1c1c;
            --text-mut: #878a8c;
            --brand-primary: #ff4500;
            --brand-secondary: #e03d00;
            --border-color: #edeff1;
            --padding-side: 24px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { overflow-x: hidden; width: 100%; position: relative; }
        body { font-family: 'Rubik', sans-serif; background-color: var(--bg-body); color: var(--text-main); line-height: 1.5; }
        
        .layout-wrapper { display: flex; min-height: 100vh; overflow-x: hidden; position: relative; }
        
        /* Sidebar */
        .sidebar { width: 260px; background: var(--bg-surface); border-right: 1px solid var(--border-color); flex-shrink: 0; display: flex; flex-direction: column; overflow-y: auto; padding-bottom: 24px; scrollbar-width: none; -ms-overflow-style: none; }
        .sidebar::-webkit-scrollbar { display: none; }
        .sidebar-logo { padding: 30px var(--padding-side); text-decoration: none; display: flex; align-items: center; gap: 10px; font-size: 2rem; font-weight: 800; color: var(--text-main); }
        .sidebar-logo img { width: 140px; height: auto; }
        
        .nav-section { margin-top: 20px; }
        .nav-title { padding: 0 var(--padding-side); font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-mut); font-weight: 700; margin-bottom: 12px; }
        .nav-link { display: flex; align-items: center; gap: 14px; padding: 12px var(--padding-side); color: var(--text-mut); text-decoration: none; font-size: 1.4rem; font-weight: 700; transition: 0.2s; border-left: 3px solid transparent; }
        .nav-link:hover { color: var(--brand-primary); background: rgba(255, 69, 0, 0.04); }
        .nav-link.active { color: var(--brand-primary); background: rgba(255, 69, 0, 0.08); border-left-color: var(--brand-primary); }
        .nav-link ion-icon { font-size: 1.8rem; }
        
        .sidebar-bottom { margin-top: auto; padding-top: 20px; border-top: 1px solid var(--border-color); margin-top: 30px; }
        
        /* Friends list common */
        .friends-list { padding: 0 var(--padding-side); display: flex; flex-direction: column; gap: 16px; }
        .friend-item { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .friend-avatar { width: 36px; height: 36px; border-radius: 50%; background: #e0e9f8; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #1c1c1c; font-size: 1.2rem; flex-shrink: 0; }
        .friend-info { display: flex; flex-direction: column; }
        .friend-name { font-size: 1.3rem; font-weight: 700; color: var(--text-main); }
        .friend-role { font-size: 1.1rem; color: var(--text-mut); }
 
        /* Main Content */
        .main-col { flex-grow: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; position: relative; }
        
        /* Topbar */
        .topbar { height: 80px; background: var(--bg-surface); padding: 0 40px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border-color); flex-shrink: 0; }
        .search-bar { position: relative; width: 400px; }
        .search-bar ion-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); font-size: 1.8rem; color: var(--text-mut); }
        .search-input { width: 100%; padding: 12px 16px 12px 45px; border-radius: 30px; border: 1px solid var(--border-color); background: #f6f7f8; font-family: inherit; font-size: 1.4rem; transition: 0.2s; }
        
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .icon-btn { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; border: 1px solid var(--border-color); background: #fff; color: var(--text-main); font-size: 1.8rem; cursor: pointer; transition: 0.2s; text-decoration: none; }
        .icon-btn:hover, .icon-btn.active { border-color: var(--brand-primary); color: var(--brand-primary); }
        .user-profile { display: flex; align-items: center; gap: 12px; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--brand-primary); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.4rem; font-weight: 700; flex-shrink: 0; }
        .user-name { font-size: 1.4rem; font-weight: 700; color: var(--text-main); white-space: nowrap; }

        /* Mobile Adjustments */
        .mobile-nav { display: none; }

        @media (max-width: 992px) {
            .sidebar { position: fixed; left: -260px; height: 100vh; z-index: 1000; transition: 0.3s; box-shadow: 10px 0 30px rgba(0,0,0,0.1); padding-bottom: 120px; }
            .sidebar.open { left: 0; }
            .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); backdrop-filter: blur(4px); z-index: 999; }
            .sidebar-overlay.active { display: block; }
            
            .topbar { padding: 0 15px; }
            .search-bar { width: auto; flex-grow: 1; margin-right: 15px; }
            .search-input { padding: 10px 12px 10px 35px; font-size: 1.2rem; }
            .search-bar ion-icon { left: 12px; font-size: 1.5rem; }
            
            .topbar-right { gap: 10px; }
            .icon-btn { width: 35px; height: 35px; font-size: 1.6rem; }
            .user-name { display: none; }
            
            .mobile-nav {
                display: flex;
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100vw;
                max-width: 100%;
                background: var(--bg-surface);
                border-top: 1px solid var(--border-color);
                z-index: 1001;
                padding: 12px 0;
                justify-content: space-around;
                align-items: center;
                box-shadow: 0 -5px 15px rgba(0,0,0,0.05);
                padding-bottom: env(safe-area-inset-bottom, 12px);
            }
            .mobile-nav-link {
                display: flex;
                flex-direction: column;
                align-items: center;
                color: var(--text-mut);
                text-decoration: none;
                font-size: 1rem;
                font-weight: 700;
                gap: 2px;
                flex: 1;
                min-width: 0;
            }
            .mobile-nav-link.active { color: var(--brand-primary); }
            .mobile-nav-link ion-icon { font-size: 1.8rem; }
            
            body { padding-bottom: 70px; overflow-x: hidden; width: 100vw; }
        }

        @media (min-width: 993px) {
            .layout-wrapper { padding-left: 260px; }
            .sidebar { position: fixed; left: 0; top: 0; height: 100vh; }
        }
    </style>
    @livewireStyles
    @stack('styles')
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="layout-wrapper" style="{{ (isset($noSidebar) && $noSidebar) ? 'padding-left: 0;' : '' }}">
        @if(!isset($noSidebar) || !$noSidebar)
            @include('partials.dashboard-sidebar')
        @endif

        <main class="main-col">
            @if(!isset($noTopbar) || !$noTopbar)
                @include('partials.dashboard-topbar')
            @endif
            
            <div class="content-area">
                @yield('content')
            </div>
        </main>
    </div>

    @if(!isset($noSidebar) || !$noSidebar)
        @include('partials.dashboard-mobile-nav')
    @endif

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script>
        const sidebar = document.getElementById('dashboardSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const mobileMoreBtn = document.getElementById('mobileMoreBtn');

        const openSidebar = () => {
            sidebar.classList.add('open');
            overlay.classList.add('active');
        };

        const closeSidebar = () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        };

        if (mobileMoreBtn) mobileMoreBtn.addEventListener('click', openSidebar);
        if (overlay) overlay.addEventListener('click', closeSidebar);
    </script>
    @livewireScripts
    @stack('scripts')
</body>
</html>
