import React, { useEffect, useState, useRef } from 'react';
import { Head, Link } from '@inertiajs/react';
import DashboardLayout from '../Layouts/DashboardLayout';

interface Lesson {
    course_slug: string;
    video_id: string;
    category: string;
    title: string;
    progress: number;
    updated: string | null;
}

interface Course {
    slug: string;
    title: string;
    category: string;
    icon: string;
    color: string;
}

interface Mentor {
    name: string;
    role: string;
    avatar: string;
}

interface DashboardProps {
    stats: {
        courses_started: number;
        videos_completed: number;
        overall_pct: number;
    };
    lessons: Lesson[];
    activityMap: Record<string, number>;
    continueWatching: Course[];
    mentors: Mentor[];
}

export default function Dashboard({ stats, lessons, activityMap, continueWatching, mentors }: DashboardProps) {
    const carouselRef = useRef<HTMLDivElement>(null);
    const [canScrollPrev, setCanScrollPrev] = useState(false);
    const [canScrollNext, setCanScrollNext] = useState(true);

    const updateCarouselButtons = () => {
        const carousel = carouselRef.current;
        if (carousel) {
            const minScrollLeft = 0;
            const maxScrollLeft = carousel.scrollWidth - carousel.clientWidth;
            setCanScrollPrev(carousel.scrollLeft > minScrollLeft);
            setCanScrollNext(Math.ceil(carousel.scrollLeft) < maxScrollLeft - 2);
        }
    };

    useEffect(() => {
        const carousel = carouselRef.current;
        if (carousel) {
            updateCarouselButtons();
            carousel.addEventListener('scroll', updateCarouselButtons);
            window.addEventListener('resize', updateCarouselButtons);
        }
        return () => {
            if (carousel) {
                carousel.removeEventListener('scroll', updateCarouselButtons);
            }
            window.removeEventListener('resize', updateCarouselButtons);
        };
    }, [continueWatching]);

    const scrollCarousel = (direction: 'prev' | 'next') => {
        const carousel = carouselRef.current;
        if (carousel) {
            const scrollAmount = direction === 'prev' ? -300 : 300;
            carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        }
    };

    // Prepare Activity Heatmap Columns (chunks of 7 days)
    const dates = Object.keys(activityMap);
    const counts = Object.values(activityMap);
    const columns: Array<Array<{ date: string; count: number }>> = [];
    const paired = dates.map((date, i) => ({ date, count: counts[i] }));
    
    for (let i = 0; i < paired.length; i += 7) {
        columns.push(paired.slice(i, i + 7));
    }

    // Heatmap Month labels
    const MONTHS = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    let lastMonthIndex: number | null = null;
    const monthSlots = columns.map((col, idx) => {
        const firstCell = col[0];
        const d = firstCell ? new Date(firstCell.date + 'T00:00:00') : null;
        const m = d ? d.getMonth() : null;
        let label = '';
        if (d && m !== null && m !== lastMonthIndex) {
            label = MONTHS[m];
            lastMonthIndex = m;
        }
        return (
            <span 
                key={idx} 
                className="heatmap-month-slot" 
                style={{ width: '11px', display: 'inline-block', fontSize: '10px' }}
            >
                {label}
            </span>
        );
    });

    // Streak Count
    let streak = 0;
    const reversedEntries = [...paired].reverse();
    for (const entry of reversedEntries) {
        if (entry.count > 0) {
            streak++;
        } else {
            break;
        }
    }

    const formatDiffForHumans = (dateString: string | null) => {
        if (!dateString) return '';
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now.getTime() - date.getTime();
        const diffMins = Math.floor(diffMs / 60000);
        if (diffMins < 1) return 'just now';
        if (diffMins < 60) return `${diffMins}m ago`;
        const diffHrs = Math.floor(diffMins / 60);
        if (diffHrs < 24) return `${diffHrs}h ago`;
        const diffDays = Math.floor(diffHrs / 24);
        return `${diffDays}d ago`;
    };

    return (
        <DashboardLayout title="User Dashboard">
            <style dangerouslySetInnerHTML={{__html: `
                .content-area { padding: 30px 40px; overflow-y: auto; flex-grow: 1; display: grid; grid-template-columns: 1fr 340px; gap: 30px; align-items: start; }
                .hero-banner { background: linear-gradient(135deg, var(--brand-primary), #ff8058); border-radius: 16px; padding: 40px; color: #fff; position: relative; overflow: hidden; margin-bottom: 30px; box-shadow: 0 10px 25px rgba(255, 69, 0, 0.2); }
                .hero-label { font-size: 1.2rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 12px; opacity: 0.9; }
                .hero-title { font-size: 3rem; font-weight: 800; line-height: 1.2; margin-bottom: 24px; max-width: 80%; }
                .hero-btn { background: #1c1c1c; color: #fff; border: none; padding: 12px 24px; border-radius: 30px; font-size: 1.4rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; text-decoration: none; }
                .hero-btn:hover { background: #000; }
                .hero-stars { position: absolute; right: 0; top: 0; width: 40%; height: 100%; opacity: 0.2; pointer-events: none; background: radial-gradient(circle, #fff 2px, transparent 2px); background-size: 30px 30px; }
                
                .metric-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
                .metric-card { background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px 20px; display: flex; align-items: center; gap: 16px; }
                .m-icon { width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; flex-shrink: 0; }
                .m-icon-1 { background: #f0ebff; color: #8e54e9; }
                .m-icon-2 { background: #ffe4ef; color: #ff4aa0; }
                .m-icon-3 { background: #dff6ff; color: #3aa8f2; }
                .m-details h4 { font-size: 1.5rem; color: var(--text-main); font-weight: 700; }
                .m-details p { font-size: 1.2rem; color: var(--text-mut); }
                
                .section-hdr { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
                .section-title { font-size: 1.8rem; font-weight: 800; color: var(--text-main); }
                .section-nav { display: flex; gap: 8px; }
                .s-nav-btn { width: 32px; height: 32px; border-radius: 50%; border: 1px solid var(--border-color); background: var(--bg-surface); display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-main); }
                .s-nav-btn.active { background: var(--brand-primary); color: #fff; border-color: var(--brand-primary); }
                
                .cw-grid { display: flex; gap: 20px; margin-bottom: 30px; overflow-x: auto; scroll-snap-type: x mandatory; padding-bottom: 15px; scrollbar-width: none; }
                .cw-grid::-webkit-scrollbar { display: none; }
                .cw-card { flex: 0 0 calc(33.333% - 14px); scroll-snap-align: start; background: var(--bg-surface); border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color); }
                .cw-img { width: 100%; height: 140px; background-color: #ddd; background-size: cover; background-position: center; position: relative; }
                .cw-body { padding: 16px; }
                .cw-badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 1rem; font-weight: 700; margin-bottom: 10px; }
                .cw-badge-fe { background: #dff6ff; color: #3aa8f2; }
                .cw-title { font-size: 1.4rem; font-weight: 700; line-height: 1.4; margin-bottom: 14px; min-height: 40px; }
                
                .lesson-table { width: 100%; border-collapse: collapse; background: var(--bg-surface); border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color); }
                .lesson-table th { background: #fdfdfd; padding: 14px 20px; text-align: left; font-size: 1.1rem; color: var(--text-mut); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); }
                .lesson-table td { padding: 16px 20px; font-size: 1.3rem; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
                .lesson-table tr:last-child td { border-bottom: none; }
                .tbl-mentor { display: flex; align-items: center; gap: 12px; }
                .tbl-m-info h5 { font-size: 1.3rem; font-weight: 700; color: var(--text-main); }
                .tbl-m-info p { font-size: 1.1rem; color: var(--text-mut); }
                .tbl-badge { background: #f6f7f8; color: var(--text-main); padding: 6px 12px; border-radius: 20px; font-size: 1.1rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; }
                .action-arrow { width: 32px; height: 32px; border-radius: 50%; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: var(--text-mut); text-decoration: none; transition: 0.2s; }
                .action-arrow:hover { border-color: var(--brand-primary); color: var(--brand-primary); }

                .right-panel { display: flex; flex-direction: column; gap: 30px; }
                .stat-widget { background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 16px; padding: 24px; text-align: center; }
                .stat-radial { width: 140px; height: 140px; margin: 0 auto 20px; position: relative; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: 0.3s; }
                .stat-inner { width: 120px; height: 120px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: inset 0 2px 10px rgba(0,0,0,0.05); overflow: hidden;}
                .stat-val { position: absolute; top: 0; right: 0; background: var(--brand-primary); color: #fff; padding: 4px 8px; border-radius: 20px; font-size: 1.1rem; font-weight: 700; border: 2px solid #fff; }
                .stat-greeting { font-size: 1.8rem; font-weight: 800; margin-bottom: 6px; }
                .stat-sub { font-size: 1.2rem; color: var(--text-mut); margin-bottom: 24px; }
                
                .heatmap-wrap { margin-top: 20px; }
                .heatmap-title { font-size: 1.2rem; font-weight: 700; color: var(--text-mut); margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
                .heatmap-title span { color: var(--brand-primary); font-size: 1.1rem; font-weight: 700; }
                .heatmap-outer { display: flex; gap: 5px; overflow-x: auto; padding-bottom: 4px; scrollbar-width: thin; scrollbar-color: #ddd transparent; }
                .heatmap-day-labels { display: flex; flex-direction: column; gap: 3px; padding-top: 18px; flex-shrink: 0; }
                .heatmap-day-label { height: 11px; font-size: 0.95rem; color: var(--text-mut); line-height: 11px; white-space: nowrap; }
                .heatmap-right { display: flex; flex-direction: column; min-width: 0; }
                .heatmap-month-row { display: flex; gap: 3px; height: 16px; margin-bottom: 2px; }
                .heatmap-grid { display: flex; gap: 3px; }
                .heatmap-col { display: flex; flex-direction: column; gap: 3px; }
                .heatmap-cell { width: 11px; height: 11px; border-radius: 2px; background: #ede9f7; transition: transform 0.12s; cursor: pointer; flex-shrink: 0; }
                .heatmap-cell:hover { transform: scale(1.4); outline: 1px solid rgba(115,64,224,0.4); }
                .heatmap-cell[data-level="1"] { background: #c4b0f5; }
                .heatmap-cell[data-level="2"] { background: #9b77ee; }
                .heatmap-cell[data-level="3"] { background: #7340e0; }
                .heatmap-cell[data-level="4"] { background: #4a00c8; }
                .heatmap-legend { display: flex; align-items: center; gap: 5px; margin-top: 8px; font-size: 1.05rem; color: var(--text-mut); justify-content: flex-end; }
                .legend-cell { width: 11px; height: 11px; border-radius: 2px; display: inline-block; }

                .mentor-widget { background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 16px; padding: 24px; }
                .mentor-list { display: flex; flex-direction: column; gap: 16px; margin-top: 16px; }
                .mentor-item { display: flex; align-items: center; justify-content: space-between; border-bottom: 1px dashed var(--border-color); padding-bottom: 16px; }
                .mentor-item:last-child { border-bottom: none; padding-bottom: 0; }
                .m-follow-btn { display: inline-flex; align-items: center; gap: 4px; padding: 6px 14px; border: 1px solid var(--brand-primary); color: var(--brand-primary); border-radius: 20px; font-size: 1.2rem; font-weight: 700; background: none; cursor: pointer; transition: 0.2s; }
                .m-follow-btn:hover { background: var(--brand-primary); color: #fff; }
                .btn-full { width: 100%; display: block; padding: 12px; background: rgba(255, 69, 0, 0.08); color: var(--brand-primary); text-align: center; border-radius: 8px; font-weight: 700; font-size: 1.4rem; text-decoration: none; margin-top: 30px; transition: 0.2s;}
                .btn-full:hover { background: var(--brand-primary); color: #fff; }

                @media (max-width: 1200px) {
                    .content-area { grid-template-columns: 1fr; }
                    .right-panel { flex-direction: row; }
                    .right-panel > div { flex: 1; }
                }
                @media (max-width: 992px) {
                    .metric-row { grid-template-columns: repeat(2, 1fr); }
                    .cw-card { flex: 0 0 calc(50% - 10px); }
                    .hero-title { font-size: 2.2rem; max-width: 100%; }
                    .content-area { padding: 20px; }
                    .right-panel { flex-direction: column; width: 100%; }
                }
                @media (max-width: 768px) {
                    .metric-row { grid-template-columns: 1fr; }
                    .cw-card { flex: 0 0 calc(100% - 20px); }
                    .hero-banner { padding: 30px 20px; margin-bottom: 20px; width: 100%; box-sizing: border-box; }
                    .hero-title { font-size: 1.8rem; max-width: 100%; margin-bottom: 15px; }
                    .hero-btn { font-size: 1.2rem; padding: 10px 20px; }
                    
                    .lesson-table thead { display: none; }
                    .lesson-table, .lesson-table tbody, .lesson-table tr, .lesson-table td { display: block; width: 100%; }
                    .lesson-table tr { margin-bottom: 16px; border-bottom: 1px solid var(--border-color); background: var(--bg-surface); border-radius: 12px; }
                    .lesson-table td { border-bottom: none; display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; text-align: right; }
                    .lesson-table td::before { content: attr(data-label); font-weight: 700; color: var(--text-mut); text-transform: uppercase; font-size: 0.9rem; float: left; text-align: left; margin-right: 10px; }
                    
                    .content-area { padding: 15px; gap: 20px; }
                    .metric-card { padding: 12px 16px; gap: 12px; }
                    .m-icon { width: 40px; height: 40px; font-size: 1.8rem; }
                    .m-details h4 { font-size: 1.3rem; }
                    
                    .stat-widget, .mentor-widget { padding: 20px 15px; width: 100%; box-sizing: border-box; overflow: hidden; }
                    .heatmap-outer { width: 100%; max-width: 100%; overflow-x: auto; }
                    
                    .cw-card { width: 100%; }
                    .mentor-list { gap: 15px; }
                    .mentor-item { flex-wrap: nowrap; gap: 15px; justify-content: space-between; align-items: center; }
                    .tbl-mentor { flex-grow: 0; min-width: 0; gap: 12px; }
                    .tbl-m-info { flex-grow: 1; min-width: 0; }
                    .tbl-m-info h5 { font-size: 1.3rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px; }
                    .tbl-m-info p { font-size: 1.1rem; }
                    .m-follow-btn { padding: 5px 10px; font-size: 1.2rem; flex-shrink: 0; margin-left: auto; }
                }
                @media (max-width: 480px) {
                    .hero-title { font-size: 1.5rem !important; }
                    .section-title { font-size: 1.5rem; }
                    .cw-body { padding: 12px; }
                    .cw-title { font-size: 1.2rem; }
                    .content-area { padding: 10px; }
                    .m-details h4 { font-size: 1.2rem; }
                    .m-follow-btn span { display: none; }
                    .m-follow-btn { padding: 8px; border-radius: 50%; }
                }
            `}} />

            <div className="content-area">
                <div className="left-panel" style={{ minWidth: 0 }}>
                    <div className="hero-banner">
                        <div className="hero-stars"></div>
                        <div className="hero-label">Online Course</div>
                        <h1 className="hero-title" style={{ whiteSpace: 'normal', wordBreak: 'break-word' }}>Sharpen Your Skills with Professional Online Courses</h1>
                        <Link href="/courses" className="hero-btn">
                            Join Now <span><ion-icon name="chevron-forward-outline"></ion-icon></span>
                        </Link>
                    </div>

                    <div className="metric-row">
                        <div className="metric-card">
                            <div className="m-icon m-icon-1"><ion-icon name="albums-outline"></ion-icon></div>
                            <div className="m-details">
                                <p>Courses In Progress</p>
                                <h4>{stats.courses_started}</h4>
                            </div>
                        </div>
                        <div className="metric-card">
                            <div className="m-icon m-icon-2"><ion-icon name="checkmark-done-circle-outline"></ion-icon></div>
                            <div className="m-details">
                                <p>Videos Completed</p>
                                <h4>{stats.videos_completed}</h4>
                            </div>
                        </div>
                        <div className="metric-card">
                            <div className="m-icon m-icon-3"><ion-icon name="trending-up-outline"></ion-icon></div>
                            <div className="m-details">
                                <p>Overall Progress</p>
                                <h4>{stats.overall_pct}%</h4>
                            </div>
                        </div>
                    </div>

                    <div className="section-hdr">
                        <h2 className="section-title">Recommended Courses</h2>
                        <div className="section-nav">
                            <button 
                                className={`s-nav-btn ${canScrollPrev ? 'active' : ''}`} 
                                id="crs-btn-prev" 
                                onClick={() => scrollCarousel('prev')}
                            >
                                <ion-icon name="chevron-back-outline"></ion-icon>
                            </button>
                            <button 
                                className={`s-nav-btn ${canScrollNext ? 'active' : ''}`} 
                                id="crs-btn-next" 
                                onClick={() => scrollCarousel('next')}
                            >
                                <ion-icon name="chevron-forward-outline"></ion-icon>
                            </button>
                        </div>
                    </div>
                    
                    <div className="cw-grid" id="crs-carousel" ref={carouselRef}>
                        {continueWatching.length > 0 ? (
                            continueWatching.map(cw => (
                                <Link 
                                    key={cw.slug}
                                    href={`/courses/learn/${cw.slug}`} 
                                    className="cw-card" 
                                    style={{ textDecoration: 'none', color: 'inherit' }}
                                >
                                    <div className="cw-img" style={{ backgroundColor: cw.color, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                                        <ion-icon name={cw.icon} style={{ fontSize: '5rem', color: 'rgba(0,0,0,0.6)' }}></ion-icon>
                                    </div>
                                    <div className="cw-body">
                                        <span className="cw-badge cw-badge-fe" style={{ marginBottom: '14px' }}>{cw.category}</span>
                                        <h3 className="cw-title" style={{ marginBottom: '4px' }}>
                                            {cw.title.length > 48 ? `${cw.title.substring(0, 48)}...` : cw.title}
                                        </h3>
                                    </div>
                                </Link>
                            ))
                        ) : (
                            <p style={{ color: 'var(--text-mut)', fontSize: '1.3rem' }}>
                                No courses available. <Link href="/courses">Browse courses →</Link>
                            </p>
                        )}
                    </div>

                    <div className="section-hdr" style={{ marginTop: '40px' }}>
                        <h2 className="section-title">Your Lesson</h2>
                        <a href="#" style={{ color: 'var(--brand-primary)', fontWeight: 700, fontSize: '1.3rem', textDecoration: 'none' }}>See all</a>
                    </div>

                    <table className="lesson-table">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Category</th>
                                <th>Progress</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {lessons.length > 0 ? (
                                lessons.map((lesson, idx) => (
                                    <tr key={idx}>
                                        <td data-label="Course">
                                            <div className="tbl-mentor">
                                                <div className="cw-mentor-ava" style={{ width: '36px', height: '36px', fontSize: '1.2rem', display: 'flex', alignItems: 'center', justifyContent: 'center', background: '#ddd', borderRadius: '50%' }}>
                                                    {lesson.category.substring(0, 1).toUpperCase()}
                                                </div>
                                                <div className="tbl-m-info" style={{ textAlign: 'left' }}>
                                                    <h5>{lesson.title.length > 30 ? `${lesson.title.substring(0, 30)}...` : lesson.title}</h5>
                                                    <p>{formatDiffForHumans(lesson.updated)}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="Category">
                                            <span className="tbl-badge"><ion-icon name="color-filter-outline"></ion-icon> {lesson.category}</span>
                                        </td>
                                        <td data-label="Progress" style={{ fontWeight: 700, color: 'var(--brand-primary)' }}>{lesson.progress}%</td>
                                        <td data-label="Action">
                                            <Link 
                                                href={`/courses/learn/${lesson.course_slug}?v=${lesson.video_id}`} 
                                                className="action-arrow"
                                            >
                                                <ion-icon name="arrow-forward-outline"></ion-icon>
                                            </Link>
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td colSpan={4} style={{ textAlign: 'center', color: 'var(--text-mut)', padding: '30px' }}>
                                        No lessons started yet. <Link href="/courses">Start your first course →</Link>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
                
                {/* Right Column */}
                <div className="right-panel" style={{ minWidth: 0 }}>
                    <div className="stat-widget">
                        <div className="section-hdr" style={{ marginBottom: '24px' }}>
                            <h2 className="section-title">Statistic</h2>
                            <span><ion-icon name="ellipsis-vertical" style={{ color: 'var(--text-mut)', fontSize: '1.8rem' }}></ion-icon></span>
                        </div>
                        
                        <div 
                            className="stat-radial" 
                            style={{ 
                                background: `conic-gradient(var(--brand-primary) ${stats.overall_pct}%, #f0f0f0 0)`
                            }}
                        >
                            <div className="stat-inner">
                                <img 
                                    src={`https://ui-avatars.com/api/?name=${encodeURIComponent('User')}&background=f0ebff&color=8e54e9&rounded=true&size=100`} 
                                    alt="Avatar" 
                                    style={{ width: '100%', height: '100%', objectFit: 'cover' }} 
                                />
                            </div>
                            <div className="stat-val">{stats.overall_pct}%</div>
                        </div>
                        
                        <div className="stat-greeting">Good Learning Day 🔥</div>
                        <div className="stat-sub">Continue your learning to achieve your target!</div>
                        
                        <div className="heatmap-wrap" id="activity-heatmap">
                            <div className="heatmap-title">
                                Learning Activity
                                <span id="heatmap-streak">{streak > 0 ? `🔥 ${streak} day streak` : ''}</span>
                            </div>

                            <div className="heatmap-outer">
                                <div className="heatmap-day-labels">
                                    <div className="heatmap-day-label">Mon</div>
                                    <div className="heatmap-day-label"></div>
                                    <div className="heatmap-day-label">Wed</div>
                                    <div className="heatmap-day-label"></div>
                                    <div className="heatmap-day-label">Fri</div>
                                    <div className="heatmap-day-label"></div>
                                    <div className="heatmap-day-label"></div>
                                </div>
                                <div className="heatmap-right">
                                    <div className="heatmap-month-row" id="heatmap-months">
                                        {monthSlots}
                                    </div>
                                    <div className="heatmap-grid" id="heatmap-grid">
                                        {columns.map((col, colIdx) => (
                                            <div className="heatmap-col" key={colIdx} data-col={colIdx}>
                                                {col.map(({ date, count }) => {
                                                    let level = 0;
                                                    if (count >= 1) level = 1;
                                                    if (count >= 3) level = 2;
                                                    if (count >= 5) level = 3;
                                                    if (count >= 8) level = 4;
                                                    return (
                                                        <div 
                                                            key={date}
                                                            className="heatmap-cell"
                                                            data-level={level}
                                                            data-date={date}
                                                            data-count={count}
                                                            title={`${count} ${count === 1 ? 'activity' : 'activities'} on ${new Date(date).toLocaleDateString(undefined, {month: 'short', day: 'numeric', year: 'numeric'})}`}
                                                        />
                                                    );
                                                })}
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </div>

                            <div className="heatmap-legend">
                                Less
                                <span className="legend-cell" style={{ background: '#ede9f7' }}></span>
                                <span class="legend-cell" style={{ background: '#c4b0f5' }}></span>
                                <span class="legend-cell" style={{ background: '#9b77ee' }}></span>
                                <span class="legend-cell" style={{ background: '#7340e0' }}></span>
                                <span class="legend-cell" style={{ background: '#4a00c8' }}></span>
                                More
                            </div>
                        </div>
                    </div>

                    <div className="mentor-widget">
                        <div className="section-hdr">
                            <h2 className="section-title">Close Friends</h2>
                            <button className="s-nav-btn" style={{ border: 'none', color: 'var(--text-mut)' }}><ion-icon name="add-outline"></ion-icon></button>
                        </div>
                        
                        <div className="mentor-list">
                            {mentors.map((mentor, idx) => (
                                <div className="mentor-item" key={idx}>
                                    <div className="tbl-mentor">
                                        <div className="cw-mentor-ava" style={{ width: '40px', height: '40px', fontSize: '1.4rem', display: 'flex', alignItems: 'center', justifyContent: 'center', background: '#ddd', borderRadius: '50%', fontWeight: 700 }}>
                                            {mentor.avatar}
                                        </div>
                                        <div className="tbl-m-info" style={{ textAlign: 'left' }}>
                                            <h5>{mentor.name}</h5>
                                            <p>{mentor.role}</p>
                                        </div>
                                    </div>
                                    <button className="m-follow-btn"><ion-icon name="person-add-outline"></ion-icon> <span>Follow</span></button>
                                </div>
                            ))}
                        </div>
                        
                        <a href="#" className="btn-full">See All</a>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
