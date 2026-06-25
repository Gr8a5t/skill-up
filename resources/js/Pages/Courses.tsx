import React from 'react';
import { Head, Link } from '@inertiajs/react';
import DashboardLayout from '../Layouts/DashboardLayout';

interface Course {
    slug: string;
    title: string;
    category: string;
    level: string;
    color: string;
    icon: string;
    tags: string[];
}

interface CoursesProps {
    courses: Course[];
}

export default function Courses({ courses }: CoursesProps) {
    return (
        <DashboardLayout title="All Courses">
            <style dangerouslySetInnerHTML={{__html: `
                .content-area { padding: 40px; background: #fbfbfb; text-align: left; }
                .page-header { margin-bottom: 30px; display: flex; align-items: center; justify-content: space-between; }
                .page-title { font-size: 1.8rem; font-weight: 700; color: #1c1c1c; display: flex; align-items: center; gap: 10px; }
                .count-badge { background: #eee; padding: 2px 8px; border-radius: 6px; font-size: 1.2rem; color: #666; font-weight: 500; }
                
                .header-controls { display: flex; align-items: center; gap: 10px; }
                .ctrl-btn { display: flex; align-items: center; gap: 8px; padding: 6px 12px; background: #fff; border: 1px solid #eee; border-radius: 8px; font-size: 1.2rem; font-weight: 600; color: #555; cursor: pointer; transition: 0.2s; }
                .ctrl-btn:hover { background: #f9f9f9; border-color: #ddd; }
                .view-toggle { display: flex; background: #fff; border: 1px solid #eee; border-radius: 8px; overflow: hidden; }
                .toggle-btn { padding: 6px 10px; cursor: pointer; color: #555; font-size: 1.6rem; display: flex; align-items: center; justify-content: center; }
                .toggle-btn.active { background: #1c1c1c; color: #fff; }

                .course-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
                .course-card { background: #fff; border-radius: 14px; border: 1px solid #efefef; overflow: hidden; display: flex; flex-direction: column; transition: 0.3s; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
                .course-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.06); }
                
                .card-hdr { height: 150px; position: relative; display: flex; align-items: center; justify-content: center; }
                .card-icon { font-size: 5.5rem; color: rgba(0,0,0,0.7); }
                
                .card-body { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
                .card-tags { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 12px; }
                .tag-badge { background: #fff; color: #888; padding: 4px 10px; border-radius: 6px; font-size: 1.05rem; font-weight: 600; border: 1px solid #efefef; }
                .card-title { font-size: 1.55rem; font-weight: 700; color: #1c1c1c; line-height: 1.35; margin-bottom: 20px; min-height: 42px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
                
                .card-footer { display: flex; align-items: center; justify-content: space-between; padding-top: 16px; border-top: 1px solid #f8f8f8; }
                .footer-left { font-size: 1.25rem; color: #999; font-weight: 500; display: flex; align-items: center; gap: 4px; }
                .footer-left span { color: #1c1c1c; font-weight: 700; }

                @media (max-width: 992px) {
                    .content-area { padding: 20px; }
                }
                @media (max-width: 768px) {
                    .page-header { flex-direction: column; align-items: flex-start; gap: 20px; }
                }
            `}} />

            <div className="content-area">
                <div className="page-header">
                    <h1 className="page-title">
                        All Courses <span className="count-badge">{courses.length}</span>
                    </h1>
                    <div className="header-controls">
                        <button className="ctrl-btn"><ion-icon name="options-outline"></ion-icon> Filter</button>
                        <button className="ctrl-btn"><ion-icon name="swap-vertical-outline"></ion-icon> Sort by</button>
                        <div className="view-toggle">
                            <div className="toggle-btn"><ion-icon name="menu-outline"></ion-icon></div>
                            <div className="toggle-btn active"><ion-icon name="grid-outline"></ion-icon></div>
                        </div>
                    </div>
                </div>

                <div className="course-grid">
                    {courses.map((course) => (
                        <Link key={course.slug} href={`/courses/${course.slug}/learn`} className="course-card" style={{ textDecoration: 'none' }}>
                            <div className="card-hdr" style={{ backgroundColor: course.color }}>
                                <ion-icon name={course.icon} className="card-icon"></ion-icon>
                            </div>
                            <div className="card-body">
                                <div className="card-tags">
                                    {course.tags.map((tag, idx) => (
                                        <span key={idx} className="tag-badge">{tag}</span>
                                    ))}
                                </div>
                                <h3 className="card-title">{course.title}</h3>
                                
                                <div className="card-footer">
                                    <div className="footer-left">Level: <span>{course.level}</span></div>
                                </div>
                            </div>
                        </Link>
                    ))}
                    {courses.length === 0 && (
                        <div style={{ gridColumn: '1 / -1', textAlign: 'center', padding: '60px 20px' }}>
                            <h2 style={{ fontSize: '2.2rem', color: '#1c1c1c', marginBottom: '10px', fontWeight: 800 }}>No courses found</h2>
                            <p style={{ color: '#666', fontSize: '1.5rem', marginBottom: '30px' }}>We couldn't find any courses matching search criteria.</p>
                            <Link href="/courses" style={{ display: 'inline-flex', alignItems: 'center', gap: '8px', color: 'var(--brand-primary)', fontSize: '1.6rem', fontWeight: 700, textDecoration: 'none' }}>
                                View recommended courses <ion-icon name="arrow-forward-outline" style={{ transform: 'rotate(-45deg)' }}></ion-icon>
                            </Link>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
