import React from 'react';
import { Head, Link } from '@inertiajs/react';
import FitLifeLayout from '../Layouts/FitLifeLayout';

interface SkillClass {
    title: string;
    slug: string;
    description: string;
    duration: string;
    focus: string;
    image: string;
    icon: string;
    statusLabel?: string;
    progress: number;
}

interface BlogPost {
    title: string;
    image: string;
    alt: string;
    date: string;
    datetime: string;
    excerpt: string;
}

interface HomeProps {
    classes: SkillClass[];
    blogs: BlogPost[];
}

export default function Home({ classes, blogs }: HomeProps) {
    return (
        <FitLifeLayout>
            <Head title="SkillUp — Build practical skills for free" />

            <main>
                <article>
                    {/* Hero Section */}
                    <section 
                        className="section hero bg-dark has-after has-bg-image" 
                        id="home" 
                        aria-label="hero" 
                        data-section
                        style={{ backgroundImage: "url('/fitlife-assets/images/hero-bg.png')" }}
                    >
                        <div className="container">
                            <div className="hero-content">
                                <p className="hero-subtitle">
                                    <strong className="strong">SkillUp</strong> for young minds
                                </p>
                                <h1 className="h1 hero-title">Build practical skills, free</h1>
                                <p className="section-text">
                                    SkillUp guides you through the skills employers and founders seek—coding, design, and career habits—
                                    without a paywall or fluff.
                                </p>
                                <Link href="/courses" className="btn btn-primary">Browse skill paths</Link>
                            </div>

                            <div className="hero-banner">
                                <img src="/fitlife-assets/images/hero-banner.png" width="660" height="753" alt="hero banner" className="w-100" fetchpriority="high" decoding="async" />
                                <img src="/fitlife-assets/images/hero-circle-one.png" width="666" height="666" aria-hidden="true" alt="" className="circle circle-1" />
                                <img src="/fitlife-assets/images/hero-circle-two.png" width="666" height="666" aria-hidden="true" alt="" className="circle circle-2" />
                            </div>
                        </div>
                    </section>

                    {/* About Section */}
                    <section className="section about" id="about" aria-label="about">
                        <div className="container">
                            <div className="about-banner has-after">
                                <img src="/fitlife-assets/images/about-banner.png" width="660" height="648" loading="lazy" decoding="async" alt="about banner" className="w-100" />
                                <img src="/fitlife-assets/images/about-circle-one.png" width="660" height="534" loading="lazy" decoding="async" aria-hidden="true" alt="" className="circle circle-1" />
                                <img src="/fitlife-assets/images/about-circle-two.png" width="660" height="534" loading="lazy" decoding="async" aria-hidden="true" alt="" className="circle circle-2" />
                                <img src="/fitlife-assets/images/fitness.png" width="650" height="154" loading="lazy" decoding="async" alt="fitness" className="abs-img w-100" />
                            </div>

                            <div className="about-content">
                                <p className="section-subtitle">About SkillUp</p>
                                <h2 className="h2 section-title">Every curious young learner deserves a guide</h2>
                                <p className="section-text">
                                    SkillUp combines mentors, practical projects, and transparent roadmaps so students and early-career builders
                                    can focus on learning instead of hunting for the next resource.
                                </p>
                                <p className="section-text">
                                    We celebrate every milestone, keep motivation high with simple rituals, and point you toward the skills
                                    that unlock modern careers.
                                </p>
                                <div className="wrapper">
                                    <div className="about-coach">
                                        <figure className="coach-avatar">
                                            <img src="/fitlife-assets/images/hahaha.png" width="65" height="65" loading="lazy" alt="SkillUp guide" />
                                        </figure>
                                        <div>
                                            <h3 className="h3 coach-name">Great Onweazu</h3>
                                            <p className="coach-title">Founder</p>
                                        </div>
                                    </div>
                                    <a href="#class" className="btn btn-primary">See the learning paths</a>
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Video Section */}
                    <section className="section video" aria-label="video">
                        <div className="container">
                            <div 
                                className="video-card has-before has-bg-image"
                                style={{ backgroundImage: "url('/fitlife-assets/images/video-banner.jpg')" }}
                            >
                                <h2 className="h2 card-title">Discover SkillUp stories</h2>
                                <button className="play-btn" aria-label="play video">
                                    <ion-icon name="play-sharp" aria-hidden="true"></ion-icon>
                                </button>
                                <a href="#" className="btn-link has-before">Hear how SkillUp helps</a>
                            </div>
                        </div>
                    </section>

                    {/* Class/Pathways Section */}
                    <section 
                        className="section class bg-dark has-bg-image" 
                        id="class" 
                        aria-label="class"
                        style={{ backgroundImage: "url('/fitlife-assets/images/classes-bg.png')" }}
                    >
                        <div className="container">
                            <p className="section-subtitle">Learning Paths</p>
                            <h2 className="h2 section-title text-center">SkillUp pathways for every focus</h2>

                            <ul className="class-list has-scrollbar">
                                {classes.map((c, index) => (
                                    <li key={c.slug || index} className="scrollbar-item">
                                        <div className="class-card">
                                            <figure className="card-banner img-holder" style={{ '--width': '416', '--height': '240' } as React.CSSProperties}>
                                                <img src={`/fitlife-assets/images/${c.image || 'class-1.jpg'}`} width="416" height="240" loading="lazy" decoding="async" alt={c.title} className="img-cover" />
                                            </figure>
                                            <div className="card-content">
                                                <div className="title-wrapper">
                                                    <img src={`/fitlife-assets/images/${c.icon || 'class-icon-1.png'}`} width="52" height="52" aria-hidden="true" alt="" className="title-icon" />
                                                    <h3 className="h3">
                                                        <Link href={`/learn/${c.slug}`} className="card-title">{c.title}</Link>
                                                    </h3>
                                                </div>
                                                <p className="card-text">{c.description}</p>
                                                <div className="card-progress">
                                                    <div className="progress-wrapper">
                                                        <p className="progress-label">{c.statusLabel || 'Path confidence'}</p>
                                                        <span className="progress-value">{Math.round(c.progress || 0)}%</span>
                                                    </div>
                                                    <div className="progress-bg">
                                                        <div className="progress-bar" style={{ width: `${Math.max(0, Math.min(100, c.progress || 0))}%` }}></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </section>

                    {/* Blog Section */}
                    <section className="section blog" id="blog" aria-label="blog">
                        <div className="container">
                            <p className="section-subtitle">SkillUp News</p>
                            <h2 className="h2 section-title text-center">Fresh updates and mentor notes</h2>

                            <ul className="blog-list has-scrollbar">
                                {blogs.map((blog, index) => (
                                    <li key={index} className="scrollbar-item">
                                        <div className="blog-card">
                                            <div className="card-banner img-holder" style={{ '--width': '440', '--height': '270' } as React.CSSProperties}>
                                                <img src={`/fitlife-assets/images/${blog.image}`} width="440" height="270" loading="lazy" alt={blog.alt} className="img-cover" />
                                                <time className="card-meta" dateTime={blog.datetime}>{blog.date}</time>
                                            </div>
                                            <div className="card-content">
                                                <h3 className="h3">
                                                    <a href="#" className="card-title">{blog.title}</a>
                                                </h3>
                                                <p className="card-text">{blog.excerpt}</p>
                                                <a href="#" className="btn-link has-before">Read More</a>
                                            </div>
                                        </div>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </section>
                </article>
            </main>
        </FitLifeLayout>
    );
}
