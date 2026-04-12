@extends('layouts.fitlife')

@section('title', 'About Us | SkillUp')

@section('content')
<main class="about-modern-page">
    
    <!-- Hero & Collage Section -->
    <section class="am-hero container pt-20">
        <div class="am-hero-top">
            <h1 class="am-headline">About our <br> platform.</h1>
            <div class="am-hero-text-wrap">
                <p class="am-hero-text">
                    Our mission is to turn your dreams into reality, one skill at a time. With a diverse team of mentors, developers, and visionaries, we're constantly pushing the boundaries of what's possible in the digital world.
                </p>
                <a href="#vision" class="btn-circle-dark">↓</a>
            </div>
        </div>
        
        <div class="am-collage">
            <div class="img-wrap wrap-1">
                <img src="{{ asset('fitlife-assets/images/class-3.jpg') }}" alt="Mentorship session" class="img-cover">
            </div>
            <div class="img-wrap wrap-2">
                <img src="{{ asset('fitlife-assets/images/about-banner.png') }}" alt="Team collaboration" class="img-cover">
            </div>
            <div class="img-wrap wrap-3">
                <img src="{{ asset('fitlife-assets/images/class-2.jpg') }}" alt="Design workshop" class="img-cover">
            </div>
        </div>
    </section>

    <!-- Vision Statement -->
    <section id="vision" class="am-vision container section">
        <h2 class="am-vision-text">
            Founded with a vision to redefine tech education through a modern and friendly lens, we've become more than just a platform; we're a community of kindred spirits who share a passion for learning and innovation.
        </h2>
    </section>

    <!-- Who Are We -->
    <section class="am-who container section">
        <div class="am-split">
            <div class="am-split-left">
                <h2 class="am-section-title">Who are we?</h2>
                
                <div class="sticky-block">
                    <p class="am-sticky-text">We're the guides who believe in the power of a solid, practical, and friendly start.</p>
                    <p class="am-sticky-text-small mt-5">Ready to build unforgettable skills? Achieve with SkillUp.</p>
                </div>
            </div>
            
            <div class="am-split-right">
                <p class="am-body-text">
                    We thrive on turning your wildest career aspirations into stunning realities. At SkillUp, every project is a collaboration, every learner is a friend, and every lesson is crafted with care and enthusiasm.
                </p>
                <p class="am-body-text mt-4">
                    We're here to make your design and coding dreams come true.
                </p>
                
                <div class="am-body-image mt-5">
                    <img src="{{ asset('fitlife-assets/images/class-1.jpg') }}" alt="Learning together" class="img-cover radius-12">
                </div>
            </div>
        </div>
    </section>

    <!-- Our Team -->
    <section class="am-team container section">
        <h2 class="am-section-title">Our Team</h2>
        <p class="am-body-text mb-6 mt-3 max-w-2xl">
            Our team is a tight-knit family of designers, engineers, and visionaries, all bound by the same creative enthusiasm.
        </p>

        <div class="am-team-grid">
            <div class="team-member">
                <div class="team-avatar">
                    <img src="{{ asset('fitlife-assets/images/hahaha.png') }}" alt="Great Onweazu" class="img-cover">
                </div>
                <h4 class="h4 mt-3">Great Onweazu</h4>
                <p class="team-role">Founder & CEO</p>
            </div>
            
            <div class="team-member placeholder-team">
                <div class="team-avatar">
                    <img src="https://ui-avatars.com/api/?name=Sarah+Chen&background=e2e8f0&color=475569&size=200" alt="Sarah Chen" class="img-cover">
                </div>
                <h4 class="h4 mt-3">Sarah Chen</h4>
                <p class="team-role">Head of Design</p>
            </div>

            <!-- Join us circle -->
            <div class="team-member-join">
                <div class="join-bubble">
                    <span>+15</span>
                </div>
            </div>

            <div class="team-member placeholder-team">
                <div class="team-avatar">
                    <img src="https://ui-avatars.com/api/?name=David+Ross&background=e2e8f0&color=475569&size=200" alt="David Ross" class="img-cover">
                </div>
                <h4 class="h4 mt-3">David Ross</h4>
                <p class="team-role">Lead Engineer</p>
            </div>
            
            <div class="team-member placeholder-team">
                <div class="team-avatar">
                    <img src="https://ui-avatars.com/api/?name=Maya+Patel&background=e2e8f0&color=475569&size=200" alt="Maya Patel" class="img-cover">
                </div>
                <h4 class="h4 mt-3">Maya Patel</h4>
                <p class="team-role">Community Manager</p>
            </div>
        </div>
        
        <div class="mt-5">
            <a href="{{ route('paths') }}" class="btn-link text-uppercase font-bold" style="color: var(--rich-black-fogra-29-1); font-size: 1.6rem;">DO YOU SEE YOUR PARTNER IN US? <span style="border-bottom: 2px solid;">READ MORE</span></a>
        </div>
    </section>

    <!-- Dark Stats Footer -->
    <section class="am-stats">
        <div class="container">
            <div class="am-stats-grid">
                
                <div class="stat-col">
                    <h3 class="stat-num">5k+</h3>
                    <span class="stat-lbl">active learners</span>
                    <p class="stat-sub mt-5">Over 5,000 students successfully upskilled.</p>
                    <a href="{{ route('courses') }}" class="btn-link mt-2" style="font-size: 0.75rem;">EXPLORE COURSES</a>
                </div>

                <div class="stat-col line-left">
                    <h3 class="stat-num">120</h3>
                    <span class="stat-lbl">micro-courses</span>
                </div>

                <div class="stat-col line-left">
                    <h3 class="stat-num">8</h3>
                    <span class="stat-lbl">career paths</span>
                </div>
                
            </div>
        </div>
    </section>

</main>

<style>
/* Modern About Page Specific Styles */
.about-modern-page {
    /* Offset fixed header */
    padding-top: 120px;
    background-color: var(--white);
    color: var(--rich-black-fogra-29-1);
    font-family: var(--ff-rubik);
}

.pt-20 { padding-top: 60px; }
.mt-3 { margin-top: 1.5rem; }
.mt-4 { margin-top: 2rem; }
.mt-5 { margin-top: 3rem; }
.mb-5 { margin-bottom: 3rem; }
.mb-6 { margin-bottom: 4rem; }
.max-w-2xl { max-width: 60rem; }
.text-uppercase { text-transform: uppercase; }
.font-bold { font-weight: 700; }
.radius-12 { border-radius: 12px; }

/* Typography Overrides */
.am-headline {
    font-family: var(--ff-catamaran);
    font-size: clamp(4.5rem, 8vw, 8rem);
    font-weight: 800;
    line-height: 1.05;
    letter-spacing: -1px;
    margin-bottom: 30px;
}

.am-section-title {
    font-family: var(--ff-catamaran);
    font-size: clamp(3.5rem, 6vw, 4.8rem);
    font-weight: 500;
    line-height: 1.2;
    margin-bottom: 20px;
}

.am-vision-text {
    font-family: var(--ff-catamaran);
    font-size: clamp(2.4rem, 4vw, 3.6rem);
    line-height: 1.35;
    font-weight: 500;
    color: var(--rich-black-fogra-29-1);
    max-width: 90%;
    margin: 50px 0;
}

.am-body-text {
    font-size: 1.8rem;
    line-height: 1.7;
    color: #475569;
}

/* Hero Section */
.am-hero-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 60px;
    margin-bottom: 60px;
}

.am-hero-text-wrap {
    flex: 0 0 50%;
    display: flex;
    gap: 30px;
    align-items: flex-start;
}

.am-hero-text {
    font-size: 1.8rem;
    line-height: 1.6;
    color: #64748b;
    max-width: 500px;
}

.btn-circle-dark {
    width: 65px;
    height: 65px;
    background: var(--rich-black-fogra-29-1);
    color: var(--white);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    flex-shrink: 0;
    transition: transform 0.3s ease;
}

.btn-circle-dark:hover {
    transform: translateY(5px);
    color: var(--white);
}

@media(max-width: 991px) {
    .am-hero-top { flex-direction: column; gap: 30px; }
    .am-hero-text-wrap { flex: 1; }
}

/* Grid Collage */
.am-collage {
    display: grid;
    grid-template-columns: 3fr 4fr 3fr;
    grid-template-rows: 450px;
    gap: 25px;
    margin-bottom: 100px;
}

.img-wrap {
    overflow: hidden;
    position: relative;
    background: #f1f5f9;
}
.wrap-1 { border-radius: 20px 0 0 20px; }
.wrap-3 { border-radius: 0 20px 20px 0; }

@media(max-width: 768px) {
    .am-collage {
        grid-template-columns: 1fr;
        grid-template-rows: auto;
    }
    .img-wrap { height: 350px; border-radius: 12px; }
    .wrap-1, .wrap-3 { border-radius: 12px; }
}

/* Split Section */
.am-split {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 80px;
    padding-bottom: 60px;
}

@media(max-width: 991px) {
    .am-split { grid-template-columns: 1fr; gap: 40px; }
}

.sticky-block {
    position: sticky;
    top: 140px;
}

.am-sticky-text {
    font-size: 2rem;
    font-weight: 500;
    color: #475569;
    padding-right: 20px;
    line-height: 1.5;
}

.am-sticky-text-small {
    font-size: 1.4rem;
    text-transform: uppercase;
    font-weight: 700;
    color: #94a3b8;
    letter-spacing: 0.5px;
}

.am-body-image {
    width: 100%;
    height: 400px;
    overflow: hidden;
}

/* Team Grid */
.am-team-grid {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 40px;
    flex-wrap: wrap;
    margin-bottom: 50px;
}

.team-member {
    text-align: center;
    width: 150px;
}

.team-avatar {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    overflow: hidden;
    margin: 0 auto;
    border: 3px solid #f8fafc;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.team-member:hover .team-avatar {
    transform: translateY(-5px);
}

.team-member .h4 { font-size: 1.6rem; font-weight: 700; }
.team-role { font-size: 1.4rem; color: #64748b; }

.team-member-join {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 80px;
    height: 150px; /* Align with avatars */
}

.join-bubble {
    width: 60px; height: 60px;
    background: var(--rich-black-fogra-29-1);
    color: var(--white);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 1.6rem;
}

@media(max-width: 600px) {
    .am-team-grid { justify-content: center; gap: 30px; }
}

/* Dark Stats Footer */
.am-stats {
    background-color: var(--rich-black-fogra-29-1);
    color: var(--white);
    padding: 100px 0;
    margin-top: 100px;
}

.am-stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 60px;
}

.stat-col {
    display: flex;
    flex-direction: column;
}

.line-left {
    border-left: 1px solid rgba(255,255,255,0.1);
    padding-left: 60px;
}

.stat-num {
    font-family: var(--ff-catamaran);
    font-size: clamp(4rem, 6vw, 6rem);
    font-weight: 400;
    line-height: 1;
    margin-bottom: 10px;
}

.stat-lbl {
    font-family: var(--ff-catamaran);
    font-size: 2.2rem;
    color: #cbd5e1;
}

.stat-sub {
    font-size: 1.5rem;
    color: #94a3b8;
    max-width: 300px;
}

@media(max-width: 768px) {
    .am-stats-grid { grid-template-columns: 1fr; gap: 60px; }
    .line-left { border-left: none; padding-left: 0; }
}

</style>
@endsection
