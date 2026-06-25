import React from 'react';
import { Head, Link } from '@inertiajs/react';
import DashboardLayout from '../Layouts/DashboardLayout';

interface Course {
    slug: string;
    title: string;
    description: string;
    excerpt: string;
    level: string;
    category: string;
    lessons: number;
    image: string;
    coursera_link: string;
}

interface CourseDetailProps {
    course: Course;
    learningPoints: string[];
}

export default function CourseDetail({ course, learningPoints }: CourseDetailProps) {
    return (
        <DashboardLayout title={course.title}>
            <style dangerouslySetInnerHTML={{__html: `
                .detail-container { padding: 40px; background: #fafbfc; min-height: 100vh; text-align: left; }
                .detail-breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 1.15rem; color: #888; font-weight: 600; margin-bottom: 24px; }
                .detail-breadcrumb a { text-decoration: none; color: inherit; }
                
                .detail-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; gap: 20px; }
                .header-title-section { display: flex; flex-direction: column; gap: 10px; }
                .detail-h1 { font-size: 2.2rem; font-weight: 800; color: #1c1c1c; line-height: 1.25; margin: 0; }
                .category-badge { display: inline-flex; align-self: flex-start; background: var(--brand-primary); color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 1.1rem; font-weight: 700; text-transform: uppercase; }
                
                .header-actions { display: flex; align-items: center; gap: 12px; }
                .btn-enroll { background: #ff4500; color: #fff; border: none; padding: 12px 24px; border-radius: 8px; font-size: 1.35rem; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: background 0.2s; }
                .btn-enroll:hover { background: #e03e00; }
                
                .meta-list { display: flex; gap: 24px; margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px; list-style: none; padding-left: 0; }
                .meta-item { display: flex; align-items: center; gap: 8px; font-size: 1.3rem; color: #666; font-weight: 600; }
                
                .detail-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
                .detail-main { display: flex; flex-direction: column; gap: 30px; }
                
                .banner-wrapper { width: 100%; aspect-ratio: 21/9; border-radius: 14px; overflow: hidden; background: #fff; border: 1px solid #eee; position: relative; }
                .banner-img { width: 100%; height: 100%; object-fit: cover; }
                
                .info-box { background: #fff; border-radius: 12px; border: 1px solid #eee; padding: 24px; }
                .info-title { font-size: 1.6rem; font-weight: 800; color: #1c1c1c; margin-bottom: 16px; margin-top: 0; }
                .info-desc { font-size: 1.35rem; color: #555; line-height: 1.6; margin: 0; }
                
                .learn-points-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
                .learn-point-card { background: #fafafa; border: 1px solid #f0f0f0; border-radius: 8px; padding: 12px 16px; display: flex; align-items: center; gap: 8px; }
                .learn-point-card ion-icon { color: #23a55a; font-size: 1.4rem; }
                .learn-point-card span { font-size: 1.25rem; font-weight: 600; color: #444; }

                .syllabus-panel { background: #fff; border-radius: 12px; border: 1px solid #eee; padding: 24px; }

                @media (max-width: 992px) {
                    .detail-grid { grid-template-columns: 1fr; }
                }
            `}} />

            <div className="detail-container">
                <div className="detail-breadcrumb">
                    <Link href="/courses">Courses</Link>
                    <ion-icon name="chevron-forward"></ion-icon>
                    <span>{course.category}</span>
                    <ion-icon name="chevron-forward"></ion-icon>
                    <span>{course.title}</span>
                </div>

                <div className="detail-header">
                    <div className="header-title-section">
                        <span className="category-badge">{course.category}</span>
                        <h1 className="detail-h1">{course.title}</h1>
                    </div>
                    <div className="header-actions">
                        <Link href={`/courses/${course.slug}/learn`} className="btn-enroll">
                            <ion-icon name="play-outline"></ion-icon> Enter Classroom
                        </Link>
                    </div>
                </div>

                <ul className="meta-list">
                    <li className="meta-item"><ion-icon name="play-circle-outline"></ion-icon> {course.lessons} Lessons</li>
                    <li className="meta-item"><ion-icon name="ribbon-outline"></ion-icon> {course.level} Level</li>
                </ul>

                <div className="detail-grid">
                    <div className="detail-main">
                        <div className="banner-wrapper">
                            <img src={course.image} alt={course.title} className="banner-img" />
                        </div>
                        
                        <div className="info-box">
                            <h2 className="info-title">About Course</h2>
                            <p className="info-desc">{course.description}</p>
                        </div>

                        <div className="info-box">
                            <h2 className="info-title">What You'll Learn</h2>
                            <div className="learn-points-grid">
                                {learningPoints.map((point, idx) => (
                                    <div key={idx} className="learn-point-card">
                                        <ion-icon name="checkmark-circle-outline"></ion-icon>
                                        <span>{point}</span>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>

                    <div className="detail-sidebar">
                        <div className="syllabus-panel">
                            <h3 className="info-title" style={{ marginBottom: '12px' }}>Course Syllabus</h3>
                            <p style={{ fontStyle: 'italic', fontSize: '1.25rem', color: '#666', margin: 0 }}>This is a self-paced, project-based development course.</p>
                            <Link href={`/courses/${course.slug}/learn`} className="btn-enroll" style={{ marginTop: '20px', width: '100%', justifyContent: 'center' }}>
                                Start Learning
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
