import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';

export default function Header() {
    const { url, props } = usePage();
    const auth = props.auth as { user?: { name: string } | null };
    const [isNavbarActive, setIsNavbarActive] = useState(false);
    const [isScrolled, setIsScrolled] = useState(false);

    React.useEffect(() => {
        const handleScroll = () => {
            if (window.scrollY >= 100) {
                setIsScrolled(true);
            } else {
                setIsScrolled(false);
            }
        };

        window.addEventListener('scroll', handleScroll);
        handleScroll();

        return () => {
            window.removeEventListener('scroll', handleScroll);
        };
    }, []);

    const isHome = url === '/';
    const isAbout = url === '/about';
    const isPaths = url === '/paths';
    const isCourses = url.startsWith('/courses');
    const isChats = url === '/chats';

    const toggleNavbar = () => {
        setIsNavbarActive(prev => !prev);
    };

    const closeNavbar = () => {
        setIsNavbarActive(false);
    };

    const isHeaderActive = !isHome || isScrolled;

    return (
        <header className={`header ${isHeaderActive ? 'active' : ''}`} data-header>
            <div className="container">
                <Link href="/" className="logo">
                    <img src="/fitlife-assets/images/uplogo.png" alt="SkillUp Logo" />
                </Link>

                <nav className={`navbar ${isNavbarActive ? 'active' : ''}`} data-navbar>
                    <button className="nav-close-btn" aria-label="close menu" onClick={closeNavbar}>
                        <ion-icon name="close-sharp" aria-hidden="true"></ion-icon>
                    </button>

                    <ul className="navbar-list">
                        <li>
                            <Link href="/" className={`navbar-link ${isHome ? 'active' : ''}`} onClick={closeNavbar}>
                                Home
                            </Link>
                        </li>
                        <li>
                            <Link href="/about" className={`navbar-link ${isAbout ? 'active' : ''}`} onClick={closeNavbar}>
                                About Us
                            </Link>
                        </li>
                        <li>
                            <a href="/paths" className={`navbar-link ${isPaths ? 'active' : ''}`} onClick={closeNavbar}>
                                Paths
                            </a>
                        </li>
                        <li>
                            <a href="/courses" className={`navbar-link ${isCourses ? 'active' : ''}`} onClick={closeNavbar}>
                                Courses
                            </a>
                        </li>
                        <li>
                            <a href="/chats" className={`navbar-link ${isChats ? 'active' : ''}`} onClick={closeNavbar}>
                                Chats
                            </a>
                        </li>
                        <li>
                            <a href="#" className="navbar-link" onClick={closeNavbar} style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" style={{ width: '20px', height: '20px' }}>
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

                <div className="header-actions" style={{ display: 'flex', gap: '15px', alignItems: 'center' }}>
                    {isHome ? (
                        <a href="/register" className="btn btn-secondary" style={{ padding: '8px 20px', fontSize: '1.4rem' }}>
                            Join SkillUp
                        </a>
                    ) : (
                        auth?.user ? (
                            <>
                                <span className="auth-username" style={{ fontSize: '1.4rem', fontWeight: 700, color: 'var(--oxford-blue)' }}>
                                    Hi, {auth.user.name}
                                </span>
                                <Link method="post" as="button" href="/logout" className="btn btn-secondary" style={{ padding: '8px 20px', fontSize: '1.4rem', border: 'none', cursor: 'pointer' }}>
                                    Logout
                                </Link>
                            </>
                        ) : (
                            <>
                                <a href="/login" className="navbar-link" style={{ fontWeight: 700, color: 'var(--oxford-blue)', textDecoration: 'none', fontSize: '1.5rem' }}>
                                    Log In
                                </a>
                                <a href="/register" className="btn btn-secondary" style={{ padding: '8px 20px', fontSize: '1.4rem' }}>
                                    Join
                                </a>
                            </>
                        )
                    )}
                </div>

                <button className="nav-open-btn" aria-label="open menu" onClick={toggleNavbar}>
                    <span className="line"></span>
                    <span className="line"></span>
                    <span className="line"></span>
                </button>
            </div>
        </header>
    );
}
