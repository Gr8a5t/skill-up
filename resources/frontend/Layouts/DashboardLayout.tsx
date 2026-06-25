import React, { useState, useEffect } from 'react';
import { Link, usePage, Head, router } from '@inertiajs/react';

interface Follower {
    id: number;
    route_key: string;
    name: string;
    avatar?: string;
}

interface AuthState {
    user: {
        id: number;
        route_key: string;
        name: string;
        email: string;
        avatar?: string;
        followers: Follower[];
    } | null;
}

interface SharedProps {
    auth: AuthState;
    unreadChatCount: number;
    flash: {
        success: string | null;
        error: string | null;
    };
    [key: string]: any;
}

interface DashboardLayoutProps {
    children: React.ReactNode;
    title?: string;
    noSidebar?: boolean;
    noTopbar?: boolean;
}

export default function DashboardLayout({ children, title, noSidebar = false, noTopbar = false }: DashboardLayoutProps) {
    const { url, props } = usePage();
    const sharedProps = props as unknown as SharedProps;
    const auth = sharedProps.auth;
    const unreadChatCount = sharedProps.unreadChatCount || 0;

    const [isSidebarCollapsed, setIsSidebarCollapsed] = useState(false);
    const [isMobileSidebarOpen, setIsMobileSidebarOpen] = useState(false);
    const [searchQuery, setSearchQuery] = useState('');

    const isDashboard = url === '/user-dashboard';
    const isCourses = url.startsWith('/courses');
    const isPaths = url.startsWith('/paths');
    const isForum = url === '/forum';
    const isChats = url === '/chats';

    const handleSearchSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        router.get('/courses', { search: searchQuery });
    };

    return (
        <>
            {title && <Head title={`${title} | SkillUp`} />}

            <style dangerouslySetInnerHTML={{__html: `
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
                .sidebar { width: 260px; background: var(--bg-surface); border-right: 1px solid var(--border-color); flex-shrink: 0; display: flex; flex-direction: column; overflow-y: auto; padding-bottom: 24px; scrollbar-width: none; -ms-overflow-style: none; transition: width 0.3s ease; }
                .sidebar::-webkit-scrollbar { display: none; }
                .sidebar-logo { text-decoration: none; display: flex; align-items: center; gap: 10px; font-size: 2rem; font-weight: 800; color: var(--text-main); }
                
                .nav-section { margin-top: 20px; }
                .nav-title { padding: 0 var(--padding-side); font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-mut); font-weight: 700; margin-bottom: 12px; transition: opacity 0.3s; }
                .nav-link { display: flex; align-items: center; gap: 14px; padding: 12px var(--padding-side); color: var(--text-mut); text-decoration: none; font-size: 1.4rem; font-weight: 700; transition: 0.2s; border-left: 3px solid transparent; white-space: nowrap; overflow: hidden; }
                .nav-link:hover { color: var(--brand-primary); background: rgba(255, 69, 0, 0.04); }
                .nav-link.active { color: var(--brand-primary); background: rgba(255, 69, 0, 0.08); border-left-color: var(--brand-primary); }
                .nav-link ion-icon { font-size: 2.0rem; flex-shrink: 0; transition: font-size 0.3s ease; }
                .nav-text { transition: opacity 0.3s; }
                
                .sidebar-bottom { margin-top: auto; padding-top: 20px; border-top: 1px solid var(--border-color); margin-top: 30px; }
                
                /* Friends list common */
                .friends-list { padding: 0 var(--padding-side); display: flex; flex-direction: column; gap: 16px; }
                .friend-item { display: flex; align-items: center; gap: 12px; text-decoration: none; overflow: hidden; }
                .friend-avatar { width: 40px; height: 40px; border-radius: 50%; background: #e0e9f8; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #1c1c1c; font-size: 1.3rem; flex-shrink: 0; }
                .friend-info { display: flex; flex-direction: column; white-space: nowrap; transition: opacity 0.3s; }
                .friend-name { font-size: 1.3rem; font-weight: 700; color: var(--text-main); }
                .friend-role { font-size: 1.1rem; color: var(--text-mut); }

                /* Collapsed Sidebar Styles */
                .sidebar.collapsed { width: 80px; }
                .sidebar.collapsed .sidebar-logo-img { display: none; }
                .sidebar.collapsed .nav-title { opacity: 0; height: 0; overflow: hidden; margin: 0; padding: 0; }
                .sidebar.collapsed .nav-text { opacity: 0; width: 0; }
                .sidebar.collapsed .friend-info { opacity: 0; width: 0; }
                .sidebar.collapsed .nav-link { justify-content: center; padding: 14px 0; }
                .sidebar.collapsed .nav-link ion-icon { font-size: 2.4rem; }
                .sidebar.collapsed .friends-list { align-items: center; padding: 0; }
                .layout-wrapper { display: flex; min-height: 100vh; overflow-x: hidden; position: relative; transition: padding-left 0.3s ease; }
         
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
            `}} />

            {/* Sidebar Mobile Overlay */}
            <div 
                className={`sidebar-overlay ${isMobileSidebarOpen ? 'active' : ''}`} 
                onClick={() => setIsMobileSidebarOpen(false)}
            />

            <div 
                className="layout-wrapper" 
                style={{ 
                    paddingLeft: noSidebar ? '0px' : (isSidebarCollapsed ? '80px' : ''),
                    transition: 'padding-left 0.3s ease'
                }}
            >
                {/* Sidebar */}
                {!noSidebar && (
                    <aside className={`sidebar ${isSidebarCollapsed ? 'collapsed' : ''} ${isMobileSidebarOpen ? 'open' : ''}`} id="dashboardSidebar">
                        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', padding: '24px var(--padding-side) 20px' }}>
                            <Link href="/" className="sidebar-logo" style={{ padding: 0, margin: 0 }}>
                                <img src="/fitlife-assets/images/uplogo.png" alt="SkillUp Logo" className="sidebar-logo-img" style={{ width: '120px', height: 'auto' }} />
                            </Link>
                            <button 
                                onClick={() => setIsSidebarCollapsed(!isSidebarCollapsed)}
                                style={{ background: 'none', border: 'none', color: 'var(--text-mut)', cursor: 'pointer', display: 'flex', alignItems: 'center', justifyContent: 'center', padding: '4px', transition: '0.2s' }}
                            >
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg>
                            </button>
                        </div>
                        
                        <div className="nav-section">
                            <div className="nav-title">Overview</div>
                            <Link href="/user-dashboard" className={`nav-link ${isDashboard ? 'active' : ''}`}>
                                <ion-icon name="grid-outline"></ion-icon> 
                                <span className="nav-text">Dashboard</span>
                            </Link>
                            <Link href="/courses" className={`nav-link ${isCourses ? 'active' : ''}`}>
                                <ion-icon name="book-outline"></ion-icon> 
                                <span className="nav-text">Courses</span>
                            </Link>
                            <Link href="/paths" className={`nav-link ${isPaths ? 'active' : ''}`}>
                                <ion-icon name="map-outline"></ion-icon> 
                                <span className="nav-text">Paths</span>
                            </Link>
                            <Link href="/forum" className={`nav-link ${isForum ? 'active' : ''}`}>
                                <ion-icon name="people-outline"></ion-icon> 
                                <span className="nav-text">Forum</span>
                            </Link>
                        </div>
                        
                        <div className="nav-section">
                            <div className="nav-title">Followers</div>
                            <div className="friends-list">
                                {auth?.user?.followers && auth.user.followers.length > 0 ? (
                                    auth.user.followers.map(follower => (
                                        <Link key={follower.id} href={`/profile/${follower.route_key}`} className="friend-item">
                                            {follower.avatar ? (
                                                <img src={follower.avatar} className="friend-avatar" style={{ objectFit: 'cover' }} alt="" />
                                            ) : (
                                                <div className="friend-avatar" style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', background: 'var(--brand-primary)', color: 'white', fontWeight: 'bold' }}>
                                                    {follower.name.substring(0, 2)}
                                                </div>
                                            )}
                                            <div className="friend-info">
                                                <span className="friend-name">{follower.name}</span>
                                                <span className="friend-role">Follower</span>
                                            </div>
                                        </Link>
                                    ))
                                ) : (
                                    <p style={{ padding: '0 var(--padding-side)', color: 'var(--text-mut)', fontSize: '1.2rem' }}>No followers yet.</p>
                                )}
                            </div>
                        </div>
                        
                        <div className="sidebar-bottom" style={{ marginTop: 'auto' }}>
                            <div className="nav-title">Settings</div>
                            <Link href="/profile/edit" className={`nav-link ${url === '/profile/edit' ? 'active' : ''}`}>
                                <ion-icon name="settings-outline"></ion-icon> 
                                <span className="nav-text">Setting</span>
                            </Link>
                            <Link method="post" as="button" href="/logout" className="nav-link" style={{ width: '100%', border: 'none', background: 'none', cursor: 'pointer', color: 'var(--brand-primary)', textAlign: 'left' }}>
                                <ion-icon name="log-out-outline"></ion-icon> 
                                <span className="nav-text">Logout</span>
                            </Link>
                        </div>
                    </aside>
                )}

                {/* Main Column */}
                <main className="main-col">
                    {/* Topbar */}
                    {!noTopbar && (
                        <header className="topbar">
                            <form onSubmit={handleSearchSubmit} className="search-bar">
                                <ion-icon name="search-outline"></ion-icon>
                                <input 
                                    type="text" 
                                    className="search-input" 
                                    placeholder="Search and learn...." 
                                    value={searchQuery}
                                    onChange={e => setSearchQuery(e.target.value)}
                                />
                            </form>
                            <div className="topbar-right">
                                <Link href="/chats" className={`icon-btn ${isChats ? 'active' : ''}`} style={{ position: 'relative' }}>
                                    <ion-icon name="chatbubbles-outline"></ion-icon>
                                    {unreadChatCount > 0 && (
                                        <span className="badge" style={{
                                            position: 'absolute',
                                            top: '-5px',
                                            right: '-5px',
                                            background: 'var(--brand-primary)',
                                            color: '#fff',
                                            borderRadius: '50%',
                                            padding: '2px 6px',
                                            fontSize: '1rem',
                                            fontWeight: 'bold',
                                            lineHeight: 1
                                        }}>
                                            {unreadChatCount}
                                        </span>
                                    )}
                                </Link>
                                <a href="#" className="icon-btn" title="AI Assistant">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" style={{ width: '1.8rem', height: '1.8rem' }}>
                                        <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                                        <path d="M3 3v5h5" />
                                        <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16" />
                                        <path d="M16 21v-5h5" />
                                        <path d="M14 4.5 Q14 8.5 18 8.5 Q14 8.5 14 12.5 Q14 8.5 10 8.5 Q14 8.5 14 4.5 Z" fill="currentColor" stroke="none" />
                                        <path d="M8.5 12.5 Q8.5 14.5 10.5 14.5 Q8.5 14.5 8.5 16.5 Q8.5 14.5 6.5 14.5 Q8.5 14.5 8.5 12.5 Z" fill="currentColor" stroke="none" />
                                    </svg>
                                </a>
                                <a href="#" className="icon-btn"><ion-icon name="notifications-outline"></ion-icon></a>
                                {auth?.user && (
                                    <Link href={`/profile/${auth.user.route_key}`} className="user-profile" style={{ textDecoration: 'none' }}>
                                        <div className="user-avatar" style={{ overflow: 'hidden' }}>
                                            {auth.user.avatar ? (
                                                <img src={auth.user.avatar} alt="Avatar" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                                            ) : (
                                                auth.user.name.substring(0, 1).toUpperCase()
                                            )}
                                        </div>
                                        <span className="user-name">{auth.user.name}</span>
                                    </Link>
                                )}
                            </div>
                        </header>
                    )}
                    
                    {children}
                </main>
            </div>

            {/* Mobile Nav */}
            {!noSidebar && (
                <nav className="mobile-nav">
                    <Link href="/user-dashboard" className={`mobile-nav-link ${isDashboard ? 'active' : ''}`}>
                        <ion-icon name="grid-outline"></ion-icon>
                        <span>Home</span>
                    </Link>
                    <Link href="/courses" className={`mobile-nav-link ${isCourses ? 'active' : ''}`}>
                        <ion-icon name="book-outline"></ion-icon>
                        <span>Courses</span>
                    </Link>
                    <Link href="/paths" className={`mobile-nav-link ${isPaths ? 'active' : ''}`}>
                        <ion-icon name="map-outline"></ion-icon>
                        <span>Paths</span>
                    </Link>
                    <Link href="/forum" className={`mobile-nav-link ${isForum ? 'active' : ''}`}>
                        <ion-icon name="people-outline"></ion-icon>
                        <span>Forum</span>
                    </Link>
                    <a href="#" className="mobile-nav-link" id="mobileMoreBtn" onClick={(e) => { e.preventDefault(); setIsMobileSidebarOpen(true); }}>
                        <ion-icon name="menu-outline"></ion-icon>
                        <span>More</span>
                    </a>
                </nav>
            )}
        </>
    );
}
