<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chats | SkillUp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon4.png?v=2') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('fitlife-assets/css/style.css') }}">
    @include('partials.google-tag')
    <style>
        :root {
            --bg-body: #ffffff;
            --bg-surface: #ffffff;
            --text-main: #050505;
            --text-mut: #65676b;
            --brand-primary: #ff4500;
            --border-color: #ededed;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Rubik', sans-serif; background-color: var(--bg-body); color: var(--text-main); line-height: 1.5; }

        .layout-wrapper { max-width: 600px; margin: 0 auto; min-height: 100vh; background: #fff; box-shadow: 0 0 20px rgba(0,0,0,0.05); }

        /* Sidebar (Desktop Hidden) */
        .sidebar { width: 260px; background: #fff; border-right: 1px solid var(--border-color); flex-shrink: 0; display: none; flex-direction: column; height: 100vh; position: fixed; left: -260px; z-index: 2000; transition: 0.3s; }
        .sidebar.open { left: 0; }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); backdrop-filter: blur(4px); z-index: 1999; }
        .sidebar-overlay.active { display: block; }
        
        .sidebar-logo { padding: 30px 24px; text-decoration: none; display: block; }
        .nav-link { display: flex; align-items: center; gap: 14px; padding: 12px 24px; color: var(--text-mut); text-decoration: none; font-size: 1.4rem; font-weight: 700; transition: 0.2s; }
        .nav-link:hover, .nav-link.active { color: var(--brand-primary); background: rgba(255, 69, 0, 0.05); }
        .nav-title { padding: 20px 24px 10px; font-size: 1.1rem; text-transform: uppercase; color: var(--text-mut); font-weight: 700; }

        /* Chat UI Container */
        .chat-container { display: flex; flex-direction: column; }
        
        .chat-header { 
            padding: 16px 20px; 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            border-bottom: 1px solid var(--border-color); 
            background: #fff; 
            position: sticky; 
            top: 0; 
            z-index: 100;
        }
        .chat-title { font-size: 2rem; font-weight: 500; color: #000; }
        .header-actions { display: flex; align-items: center; gap: 20px; }
        .mark-read { color: var(--brand-primary); font-size: 1.3rem; font-weight: 500; text-decoration: none; }
        .filter-dropdown { display: flex; align-items: center; gap: 6px; color: var(--text-mut); font-size: 1.3rem; cursor: pointer; font-weight: 500; }

        .chat-list { display: flex; flex-direction: column; }
        .chat-item { 
            display: flex; 
            padding: 16px 20px; 
            gap: 14px; 
            border-bottom: 1px solid #f2f2f2; 
            text-decoration: none; 
            color: inherit; 
            transition: background 0.2s;
            align-items: center;
        }
        .chat-item:hover { background: #fafafa; }
        
        .chat-avatar { 
            width: 52px; 
            height: 52px; 
            border-radius: 50%; 
            flex-shrink: 0; 
            background: #f0f2f5; 
            overflow: hidden; 
            border: 1px solid #eee;
        }
        .chat-avatar img { width: 100%; height: 100%; object-fit: cover; }
        
        .chat-content { flex-grow: 1; min-width: 0; }
        .chat-meta { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
        .chat-name { font-size: 1.5rem; color: #050505; font-weight: 400; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .chat-item.unread .chat-name { font-weight: 700; }
        .chat-time { font-size: 1.2rem; color: var(--text-mut); }
        .chat-preview { font-size: 1.4rem; color: var(--text-mut); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .chat-item.unread .chat-preview { color: #050505; font-weight: 500; }

        .unread-dot { 
            width: 12px; 
            height: 12px; 
            border-radius: 50%; 
            background: var(--brand-primary); 
            flex-shrink: 0; 
            margin-left: 10px;
        }

        /* Bottom Nav (Mobile) */
        .mobile-nav { 
            display: flex; 
            position: fixed; 
            bottom: 0; 
            left: 50%; 
            transform: translateX(-50%);
            width: 100%; 
            max-width: 600px;
            background: #fff; 
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
            font-size: 1.1rem; 
            font-weight: 700; 
            gap: 4px; 
        }
        .mobile-nav-link.active { color: var(--brand-primary); }
        .mobile-nav-link ion-icon { font-size: 2.2rem; }
        
        body { padding-bottom: 90px; }

        @media (min-width: 993px) {
            .sidebar { display: flex; left: 0; }
            .layout-wrapper { margin-left: 260px; max-width: none; }
            .mobile-nav { display: none; }
            body { padding-bottom: 0; }
        }

        /* Topbar Styles (Restored) */
        .topbar { height: 80px; background: var(--bg-surface); padding: 0 40px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border-color); flex-shrink: 0; }
        .search-bar { position: relative; width: 400px; }
        .search-bar ion-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); font-size: 1.8rem; color: var(--text-mut); }
        .search-input { width: 100%; padding: 12px 16px 12px 45px; border-radius: 30px; border: 1px solid var(--border-color); background: #f6f7f8; font-family: inherit; font-size: 1.4rem; transition: 0.2s; }
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .icon-btn { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; border: 1px solid var(--border-color); background: #fff; color: #000; font-size: 1.8rem; cursor: pointer; transition: 0.2s; text-decoration: none;}
        .user-profile { display: flex; align-items: center; gap: 12px; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--brand-primary); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.4rem; font-weight: 700; }
        @media (max-width: 992px) {
            .topbar { padding: 0 20px; }
            .search-bar { display: none; }
        }
    </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<aside class="sidebar" id="dashboardSidebar">
    <a href="/" class="sidebar-logo">
        <img src="{{ asset('fitlife-assets/images/uplogo.png') }}" alt="SkillUp Logo" style="width: 140px; height: auto;">
    </a>
    
    <div class="nav-section">
        <div class="nav-title">Overview</div>
        <a href="{{ route('dashboard') }}" class="nav-link"><ion-icon name="grid-outline"></ion-icon> Dashboard</a>
        <a href="{{ route('courses') }}" class="nav-link"><ion-icon name="book-outline"></ion-icon> Courses</a>
        <a href="{{ route('paths') }}" class="nav-link"><ion-icon name="map-outline"></ion-icon> Paths</a>
        <a href="{{ route('dashboard.chats') }}" class="nav-link active"><ion-icon name="chatbubbles-outline"></ion-icon> Chats</a>
    </div>

    <div class="nav-section" style="margin-top: auto; border-top: 1px solid var(--border-color); padding-top: 20px;">
        <a href="#" class="nav-link"><ion-icon name="settings-outline"></ion-icon> Settings</a>
        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
            @csrf
            <button type="submit" class="nav-link" style="width: 100%; border:none; background:none; cursor:pointer; color: var(--brand-primary);"><ion-icon name="log-out-outline"></ion-icon> Logout</button>
        </form>
    </div>
</aside>

<div class="layout-wrapper">
    <!-- Topbar Restored -->
    <header class="topbar">
        <div class="search-bar">
            <ion-icon name="search-outline"></ion-icon>
            <input type="text" class="search-input" placeholder="Search your messages....">
        </div>
        <div class="topbar-right">
            <a href="{{ route('dashboard.chats') }}" class="icon-btn" style="color: var(--brand-primary); background: rgba(255, 69, 0, 0.1); border-color: var(--brand-primary);"><ion-icon name="chatbubbles-outline"></ion-icon></a>
            <a href="#" class="icon-btn"><ion-icon name="notifications-outline"></ion-icon></a>
            <div class="user-profile">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            </div>
        </div>
    </header>

    <main class="chat-container">
        <header class="chat-header">
            <h1 class="chat-title">Chats</h1>
            <div class="header-actions">
                <a href="#" class="mark-read">Mark all as read</a>
                <div class="filter-dropdown">
                    All <ion-icon name="chevron-down-outline"></ion-icon>
                </div>
            </div>
        </header>

        <div class="chat-list">
            @foreach($conversations as $chat)
            <a href="#" class="chat-item {{ $chat['unread'] ? 'unread' : '' }}">
                <div class="chat-avatar">
                    <img src="{{ $chat['avatar'] }}" alt="{{ $chat['name'] }}">
                </div>
                <div class="chat-content">
                    <div class="chat-meta">
                        <span class="chat-name">{{ $chat['name'] }}</span>
                        <span style="color: var(--text-mut); font-size: 1rem;">•</span>
                        <span class="chat-time">{{ $chat['time'] }}</span>
                    </div>
                    <div class="chat-preview">{{ $chat['message'] }}</div>
                </div>
                @if($chat['unread'])
                <div class="unread-dot"></div>
                @endif
            </a>
            @endforeach
        </div>
    </main>
</div>

<!-- Mobile Bottom Navigation -->
<nav class="mobile-nav">
    <a href="{{ route('dashboard') }}" class="mobile-nav-link">
        <ion-icon name="grid-outline"></ion-icon>
        <span>Home</span>
    </a>
    <a href="{{ route('courses') }}" class="mobile-nav-link">
        <ion-icon name="book-outline"></ion-icon>
        <span>Courses</span>
    </a>
    <a href="{{ route('paths') }}" class="mobile-nav-link">
        <ion-icon name="map-outline"></ion-icon>
        <span>Paths</span>
    </a>
    <a href="{{ route('dashboard.chats') }}" class="mobile-nav-link active">
        <ion-icon name="chatbubbles-outline"></ion-icon>
        <span>Chats</span>
    </a>
    <a href="javascript:void(0)" class="mobile-nav-link" id="mobileMoreBtn">
        <ion-icon name="menu-outline"></ion-icon>
        <span>More</span>
    </a>
</nav>

<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<script>
    const sidebar = document.getElementById('dashboardSidebar');
    const mobileMoreBtn = document.getElementById('mobileMoreBtn');
    const overlay = document.getElementById('sidebarOverlay');

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
</body>
</html>
