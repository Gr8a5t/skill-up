<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') | SkillUp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon4.png?v=2') }}" type="image/png">
    <style>
        :root {
            --bg-deep: #0a0a0c;
            --bg-surface: #141417;
            --bg-panel: rgba(255, 255, 255, 0.03);
            --brand-primary: #ff4500;
            --brand-secondary: #ff8058;
            --accent-purple: #8e54e9;
            --text-main: #ffffff;
            --text-mut: #94a3b8;
            --border-color: rgba(255, 255, 255, 0.08);
            --glass-bg: rgba(20, 20, 23, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        html { font-size: 58%; } /* Scale down standard rem across the board */
        @media (min-width: 1600px) { html { font-size: 62.5%; } }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--bg-deep); 
            color: var(--text-main);
            overflow-x: hidden;
            line-height: 1.5;
        }

        .admin-wrapper { display: flex; min-height: 100vh; }

        /* Sidebar */
        .admin-sidebar {
            width: 240px;
            background: var(--bg-surface);
            border-right: 1px solid var(--border-color);
            position: fixed;
            height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 25px 15px;
            z-index: 100;
        }

        .admin-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
            text-decoration: none;
            margin-bottom: 40px;
            padding-left: 10px;
        }
        .logo-box {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #fff;
        }

        .nav-group { margin-bottom: 30px; }
        .nav-label { font-size: 1.1rem; color: var(--text-mut); text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700; margin-bottom: 15px; padding-left: 10px; }
        .admin-nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            color: var(--text-mut);
            text-decoration: none;
            font-size: 1.35rem;
            font-weight: 600;
            border-radius: 10px;
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 4px;
        }
        .admin-nav-link:hover { color: #fff; background: var(--bg-panel); }
        .admin-nav-link.active { 
            color: #fff; 
            background: rgba(255, 69, 0, 0.1); 
            position: relative;
        }
        .admin-nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 20px;
            background: var(--brand-primary);
            border-radius: 0 4px 4px 0;
        }
        .admin-nav-link ion-icon { font-size: 2rem; }

        /* Main Content Area */
        .admin-main {
            flex-grow: 1;
            margin-left: 240px;
            padding: 30px;
            max-width: 1440px;
            width: 100%;
        }

        /* Topbar */
        .admin-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            gap: 20px;
        }
        .search-wrap { position: relative; width: 100%; max-width: 400px; }
        .search-wrap ion-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-mut); font-size: 1.6rem; }
        .admin-search {
            width: 100%;
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            padding: 10px 15px 10px 45px;
            border-radius: 10px;
            color: #fff;
            outline: none;
            font-family: inherit;
            font-size: 1.3rem;
        }
        
        .admin-user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .notif-btn {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            color: var(--text-mut);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            cursor: pointer;
            position: relative;
        }
        .notif-dot { position: absolute; top: 12px; right: 12px; width: 8px; height: 8px; background: var(--brand-primary); border-radius: 50%; border: 2px solid var(--bg-surface); }
        .admin-ava { width: 44px; height: 44px; border-radius: 12px; object-fit: cover; border: 2px solid var(--border-color); }

        @media (max-width: 1024px) {
            .admin-sidebar { width: 70px; padding: 20px 10px; }
            .admin-logo span, .nav-label, .admin-nav-link span { display: none; }
            .admin-main { margin-left: 70px; padding: 20px; }
            .admin-logo { padding-left: 5px; margin-bottom: 30px; }
            .search-wrap { max-width: 250px; }
        }
        @media (max-width: 768px) {
            .admin-sidebar { display: none; }
            .admin-main { margin-left: 0; padding: 15px; }
            .admin-topbar { flex-direction: column-reverse; align-items: flex-start; gap: 15px; }
            .search-wrap { max-width: 100%; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <a href="{{ route('admin.dashboard') }}" class="admin-logo">
                <div class="logo-box">S</div>
                <span>SkillUp<span>.</span>Admin</span>
            </a>

            <div class="nav-group">
                <p class="nav-label">Menu</p>
                <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <ion-icon name="grid-outline"></ion-icon>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.courses') }}" class="admin-nav-link {{ request()->routeIs('admin.courses*') ? 'active' : '' }}">
                    <ion-icon name="book-outline"></ion-icon>
                    <span>Courses</span>
                </a>
                <a href="#" class="admin-nav-link">
                    <ion-icon name="people-outline"></ion-icon>
                    <span>Students</span>
                </a>
                <a href="#" class="admin-nav-link">
                    <ion-icon name="card-outline"></ion-icon>
                    <span>Sales</span>
                </a>
            </div>

            <div class="nav-group">
                <p class="nav-label">General</p>
                <a href="#" class="admin-nav-link">
                    <ion-icon name="analytics-outline"></ion-icon>
                    <span>Analytics</span>
                </a>
                <a href="#" class="admin-nav-link">
                    <ion-icon name="settings-outline"></ion-icon>
                    <span>Settings</span>
                </a>
                <a href="#" class="admin-nav-link">
                    <ion-icon name="help-circle-outline"></ion-icon>
                    <span>Help</span>
                </a>
            </div>
        </aside>

        <main class="admin-main">
            <header class="admin-topbar">
                <div class="search-wrap">
                    <ion-icon name="search-outline"></ion-icon>
                    <input type="text" class="admin-search" placeholder="Search analytics...">
                </div>
                
                <div class="admin-user-profile">
                    <div class="notif-btn">
                        <ion-icon name="notifications-outline"></ion-icon>
                        <div class="notif-dot"></div>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=Admin&background=ff4500&color=fff" alt="Admin" class="admin-ava">
                </div>
            </header>

            @yield('content')
        </main>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    @stack('scripts')
</body>
</html>
