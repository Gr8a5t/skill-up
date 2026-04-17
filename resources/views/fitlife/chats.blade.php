<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Chats | SkillUp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon4.png?v=2') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('fitlife-assets/css/style.css') }}">
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
            
            /* Chat Specific */
            --chat-bg: #ffffff;
            --server-rail-bg: #f6f7f8;
            --channel-bg: #ffffff;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Rubik', sans-serif; background-color: var(--bg-body); color: var(--text-main); line-height: 1.5; height: 100vh; overflow: hidden; }
        
        .layout-wrapper { display: flex; height: 100vh; }
        
        /* Sidebar (Same as Dashboard) */
        .sidebar { width: 260px; background: var(--bg-surface); border-right: 1px solid var(--border-color); flex-shrink: 0; display: flex; flex-direction: column; padding-bottom: 24px; }
        .sidebar-logo { padding: 30px var(--padding-side); text-decoration: none; display: flex; align-items: center; gap: 10px; font-size: 2rem; font-weight: 800; color: var(--text-main); }
        .sidebar-logo img { width: 140px; height: auto; }
        
        .nav-section { margin-top: 20px; }
        .nav-title { padding: 0 var(--padding-side); font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-mut); font-weight: 700; margin-bottom: 12px; }
        .nav-link { display: flex; align-items: center; gap: 14px; padding: 12px var(--padding-side); color: var(--text-mut); text-decoration: none; font-size: 1.4rem; font-weight: 700; transition: 0.2s; border-left: 3px solid transparent; }
        .nav-link:hover { color: var(--brand-primary); background: rgba(255, 69, 0, 0.04); }
        .nav-link.active { color: var(--brand-primary); background: rgba(255, 69, 0, 0.08); border-left-color: var(--brand-primary); }
        .nav-link ion-icon { font-size: 1.8rem; }
        
        .sidebar-bottom { margin-top: auto; padding-top: 20px; border-top: 1px solid var(--border-color); }

        .friends-list { padding: 0 var(--padding-side); display: flex; flex-direction: column; gap: 16px; }
        .friend-item { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .friend-avatar { width: 36px; height: 36px; border-radius: 50%; background: #e0e9f8; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #1c1c1c; font-size: 1.2rem; flex-shrink: 0; }
        .friend-info { display: flex; flex-direction: column; }
        .friend-name { font-size: 1.3rem; font-weight: 700; color: var(--text-main); }
        .friend-role { font-size: 1.1rem; color: var(--text-mut); }

        /* Main Column */
        .main-col { flex-grow: 1; display: flex; flex-direction: column; min-width: 0; }
        
        /* Topbar */
        .topbar { height: 80px; background: var(--bg-surface); padding: 0 40px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border-color); flex-shrink: 0; }
        .search-bar { position: relative; width: 400px; }
        .search-bar ion-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); font-size: 1.8rem; color: var(--text-mut); }
        .search-input { width: 100%; padding: 12px 16px 12px 45px; border-radius: 30px; border: 1px solid var(--border-color); background: var(--bg-body); font-family: inherit; font-size: 1.4rem; transition: 0.2s; }
        
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .icon-btn { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; border: 1px solid var(--border-color); background: #fff; color: var(--text-main); font-size: 1.8rem; cursor: pointer; transition: 0.2s; text-decoration: none;}
        .user-profile { display: flex; align-items: center; gap: 12px; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--brand-primary); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.4rem; font-weight: 700; }
        
        /* Discord-Style Chat Layout */
        .chat-layout { display: flex; flex-grow: 1; overflow: hidden; }
        
        /* 1. Server Rail */
        .server-rail { width: 72px; background: var(--server-rail-bg); border-right: 1px solid var(--border-color); display: flex; flex-direction: column; align-items: center; padding: 12px 0; gap: 12px; flex-shrink: 0; overflow-y: auto; }
        .server-icon { width: 48px; height: 48px; border-radius: 50%; background: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 700; color: var(--text-main); cursor: pointer; transition: 0.2s; border: 1px solid var(--border-color); position: relative;}
        .server-icon:hover { border-radius: 16px; background: var(--brand-primary); color: #fff; border-color: var(--brand-primary); }
        .server-icon.active { border-radius: 16px; background: var(--brand-primary); color: #fff; border-color: var(--brand-primary); }
        .server-icon.active::before { content: ''; position: absolute; left: -14px; top: 50%; transform: translateY(-50%); width: 4px; height: 32px; background: var(--brand-primary); border-radius: 0 4px 4px 0; }
        
        /* 2. Channel Pane */
        .channel-pane { width: 240px; background: var(--channel-bg); border-right: 1px solid var(--border-color); display: flex; flex-direction: column; flex-shrink: 0; }
        .channel-header { padding: 20px; border-bottom: 1px solid var(--border-color); font-weight: 800; font-size: 1.5rem; display: flex; align-items: center; justify-content: space-between; }
        .channel-list { padding: 12px; overflow-y: auto; flex-grow: 1; }
        .channel-item { display: flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 8px; color: var(--text-mut); text-decoration: none; font-size: 1.4rem; font-weight: 500; margin-bottom: 2px; transition: 0.2s; }
        .channel-item ion-icon { font-size: 1.8rem; opacity: 0.6; }
        .channel-item:hover { background: rgba(0,0,0,0.03); color: var(--text-main); }
        .channel-item.active { background: rgba(255, 69, 0, 0.08); color: var(--brand-primary); font-weight: 700; }
        
        .channel-category { padding: 16px 12px 6px; font-size: 1.1rem; font-weight: 800; color: var(--text-mut); text-transform: uppercase; letter-spacing: 0.5px; }

        /* User Bar at bottom of channels */
        .user-bar { height: 60px; background: #fbfbfc; border-top: 1px solid var(--border-color); display: flex; align-items: center; padding: 0 12px; gap: 10px; flex-shrink: 0; }
        .ub-info { flex-grow: 1; display: flex; flex-direction: column; min-width: 0; }
        .ub-name { font-size: 1.2rem; font-weight: 800; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .ub-status { font-size: 1.1rem; color: var(--text-mut); }
        .ub-actions { display: flex; align-items: center; gap: 4px; }
        .ub-btn { width: 32px; height: 32px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; color: var(--text-mut); cursor: pointer; }
        .ub-btn:hover { background: rgba(0,0,0,0.05); color: var(--text-main); }
        
        /* 3. Chat Main */
        .chat-main { flex-grow: 1; display: flex; flex-direction: column; background: #fff; position: relative; min-width: 0; }
        .chat-header { height: 60px; border-bottom: 1px solid var(--border-color); padding: 0 24px; display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
        .chat-header h3 { font-size: 1.6rem; font-weight: 800; }
        .chat-header p { font-size: 1.2rem; color: var(--text-mut); font-weight: 500; }
        
        .message-feed { flex-grow: 1; overflow-y: auto; padding: 24px; display: flex; flex-direction: column; gap: 24px; }
        .message { display: flex; gap: 16px; }
        .msg-avatar { width: 44px; height: 44px; border-radius: 50%; background: #f0f4f8; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-weight: 800; color: var(--text-main); font-size: 1.4rem; overflow: hidden; border: 1px solid var(--border-color); }
        .msg-content { flex-grow: 1; }
        .msg-meta { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
        .msg-user { font-weight: 800; font-size: 1.4rem; color: var(--text-main); }
        .msg-time { font-size: 1.1rem; color: var(--text-mut); font-weight: 500; }
        .msg-body { font-size: 1.4rem; color: #3c3c3c; line-height: 1.6; }
        
        /* Chat Input */
        .chat-input-area { padding: 20px 24px 30px; background: #fff; flex-shrink: 0; }
        .input-wrapper { background: var(--bg-body); border-radius: 12px; padding: 4px 16px; display: flex; align-items: center; gap: 12px; border: 1px solid var(--border-color); transition: 0.2s; }
        .input-wrapper:focus-within { border-color: var(--brand-primary); background: #fff; box-shadow: 0 4px 12px rgba(255,69,0,0.05); }
        .chat-input { flex-grow: 1; border: none; background: transparent; padding: 12px 0; font-family: inherit; font-size: 1.4rem; color: var(--text-main); }
        .chat-input:focus { outline: none; }
        .input-btn { background: none; border: none; color: var(--text-mut); font-size: 2rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
        .input-btn:hover { color: var(--brand-primary); }
        
        /* Friends Pane (Optional/Auto-hide) */
        .members-pane { width: 220px; background: var(--bg-surface); border-left: 1px solid var(--border-color); flex-shrink: 0; padding: 20px; display: flex; flex-direction: column; gap: 20px; }
        @media (max-width: 1200px) { .members-pane { display: none; } }

        .chat-header-actions { display: flex; align-items: center; gap: 16px; margin-left: auto; }
        .ch-icon { font-size: 2rem; color: var(--text-mut); cursor: pointer; transition: 0.2s; }
        .ch-icon:hover { color: var(--text-main); }
        .ch-search { position: relative; width: 180px; }
        .ch-search input { width: 100%; border: 1px solid var(--border-color); background: var(--bg-body); padding: 6px 10px 6px 30px; border-radius: 15px; font-size: 1.2rem; font-family: inherit; }
        .ch-search ion-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 1.4rem; color: var(--text-mut); }
        
        @media (max-width: 900px) {
            .sidebar { position: fixed; left: -260px; z-index: 1000; transition: 0.3s; }
            .sidebar.open { left: 0; }
            .channel-pane { display: none; }
        }
    </style>
</head>
<body>

<div class="layout-wrapper">
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
        
        <div class="nav-section">
            <div class="nav-title">Friends</div>
            <div class="friends-list">
                <a href="#" class="friend-item">
                    <div class="friend-avatar">BM</div>
                    <div class="friend-info">
                        <span class="friend-name" style="font-size: 1.3rem; font-weight: 700; color: var(--text-main);">Bagas Mahpie</span>
                        <span class="friend-role" style="font-size: 1.1rem; color: var(--text-mut);">Friend</span>
                    </div>
                </a>
                <a href="#" class="friend-item">
                    <div class="friend-avatar">SD</div>
                    <div class="friend-info">
                        <span class="friend-name" style="font-size: 1.3rem; font-weight: 700; color: var(--text-main);">Sir Dandy</span>
                        <span class="friend-role" style="font-size: 1.1rem; color: var(--text-mut);">Old Friend</span>
                    </div>
                </a>
            </div>
        </div>
        
        <div class="nav-section">
            <div class="nav-title">Settings</div>
            <a href="#" class="nav-link"><ion-icon name="settings-outline"></ion-icon> Setting</a>
            <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" class="nav-link" style="width: 100%; border:none; background:none; cursor:pointer; color: var(--brand-primary);"><ion-icon name="log-out-outline"></ion-icon> Logout</button>
            </form>
        </div>
    </aside>

    <main class="main-col">
        <!-- Topbar -->
        <header class="topbar">
            <div class="search-bar">
                <ion-icon name="search-outline"></ion-icon>
                <input type="text" class="search-input" placeholder="Search messages or people...">
            </div>
            <div class="topbar-right">
                <a href="#" class="icon-btn"><ion-icon name="notifications-outline"></ion-icon></a>
                <div class="user-profile">
                    <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                </div>
            </div>
        </header>

        <!-- Discord Layout Area -->
        <div class="chat-layout">
            <!-- 1. Server Rail -->
            <div class="server-rail">
                @foreach($communities as $comm)
                <div class="server-icon {{ $loop->first ? 'active' : '' }}" title="{{ $comm['name'] }}" style="--hover-color: {{ $comm['color'] }}">
                    {{ $comm['initial'] }}
                </div>
                @endforeach
                <div class="server-icon" style="border-style: dashed; color: var(--text-mut);">
                    <ion-icon name="add-outline"></ion-icon>
                </div>
            </div>

            <!-- 2. Channel Pane -->
            <div class="channel-pane">
                <div class="channel-header">
                    <span># Discussion</span>
                    <ion-icon name="chevron-down-outline"></ion-icon>
                </div>
                <div class="channel-list">
                    <div class="channel-category">Overview</div>
                    @foreach($channels as $channel)
                    <a href="#" class="channel-item {{ $channel['name'] == 'general-chat' ? 'active' : '' }}">
                        <ion-icon name="at-outline"></ion-icon> {{ $channel['name'] }}
                    </a>
                    @if($loop->index == 1)
                    <div class="channel-category">Projects</div>
                    @endif
                    @endforeach
                </div>

                <div class="user-bar">
                    <div class="user-avatar" style="width: 32px; height: 32px; font-size: 1.1rem;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <div class="ub-info">
                        <span class="ub-name">{{ auth()->user()->name }}</span>
                        <span class="ub-status">#{{ auth()->user()->id }}</span>
                    </div>
                    <div class="ub-actions">
                        <div class="ub-btn"><ion-icon name="mic-outline"></ion-icon></div>
                        <div class="ub-btn"><ion-icon name="headset-outline"></ion-icon></div>
                        <div class="ub-btn"><ion-icon name="settings-outline"></ion-icon></div>
                    </div>
                </div>
            </div>

            <!-- 3. Chat Feed -->
            <div class="chat-main">
                <div class="chat-header">
                    <ion-icon name="at-outline" style="font-size: 2rem; color: var(--text-mut);"></ion-icon>
                    <div>
                        <h3>general-chat</h3>
                    </div>
                    
                    <div class="chat-header-actions">
                        <ion-icon name="notifications-outline" class="ch-icon"></ion-icon>
                        <ion-icon name="pin-outline" class="ch-icon"></ion-icon>
                        <ion-icon name="people-outline" class="ch-icon"></ion-icon>
                        <div class="ch-search">
                            <ion-icon name="search-outline"></ion-icon>
                            <input type="text" placeholder="Search">
                        </div>
                        <ion-icon name="help-circle-outline" class="ch-icon"></ion-icon>
                    </div>
                </div>

                <div class="message-feed">
                    @foreach($messages as $msg)
                    <div class="message">
                        <div class="msg-avatar">{{ $msg['avatar'] }}</div>
                        <div class="msg-content">
                            <div class="msg-meta">
                                <span class="msg-user">{{ $msg['username'] }}</span>
                                <span class="msg-time">{{ $msg['time'] }}</span>
                            </div>
                            <div class="msg-body">{{ $msg['content'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="chat-input-area">
                    <div class="input-wrapper">
                        <button class="input-btn"><ion-icon name="add-circle-outline"></ion-icon></button>
                        <input type="text" class="chat-input" placeholder="Message #general-chat">
                        <button class="input-btn"><ion-icon name="happy-outline"></ion-icon></button>
                        <button class="input-btn" style="color: var(--brand-primary);"><ion-icon name="send"></ion-icon></button>
                    </div>
                </div>
            </div>

            <!-- 4. Members Pane -->
            <div class="members-pane">
                <div class="nav-title" style="padding:0">Online — 4</div>
                <div class="friends-list" style="padding:0">
                    <div class="friend-item">
                        <div class="friend-avatar" style="width:32px; height:32px; font-size:1rem;">PS</div>
                        <span class="friend-name" style="font-size:1.2rem">Padhang Satrio</span>
                    </div>
                    <div class="friend-item">
                        <div class="friend-avatar" style="width:32px; height:32px; font-size:1rem;">BM</div>
                        <span class="friend-name" style="font-size:1.2rem">Bagas Mahpie</span>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>
</html>
