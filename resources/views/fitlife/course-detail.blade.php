@extends('layouts.fitlife')

@section('title', ($course['title'] ?? 'Course').' | SkillUp')

@section('content')
    <main>
        <article class="course-detail-page">
            <section class="section course-unified" aria-label="course details">
                <div class="container container-large">
                    <div class="course-unified-breadcrumb">
                        <a href="{{ route('home') }}"><ion-icon name="home-outline"></ion-icon></a>
                        <a href="{{ route('courses') }}">Courses</a>
                        <span>/</span>
                        <a href="#">{{ $course['tags'][0] ?? 'UI / UX Design' }}</a>
                        <span>/</span>
                        <span>{{ \Illuminate\Support\Str::limit($course['title'] ?? 'Course', 42) }}</span>
                    </div>

                    <div class="course-unified-header">
                        <div class="header-left">
                            <a href="{{ route('courses') }}" class="btn-back"><ion-icon name="chevron-back-outline"></ion-icon></a>
                            <h1 class="h2">{{ $course['title'] }}</h1>
                            <span class="badge">{{ $course['tags'][0] ?? 'UI / UX Design' }}</span>
                        </div>
                        <div class="header-right">
                            <a href="#" class="btn-share">Share</a>
                            <button class="btn btn-primary btn-enroll">
                                <ion-icon name="lock-closed-outline"></ion-icon> Enroll Now
                            </button>
                        </div>
                    </div>

                    <ul class="course-unified-meta">
                        <li><ion-icon name="play-circle-outline"></ion-icon> {{ $course['lessons'] ?? 0 }} lessons</li>
                        <li><ion-icon name="time-outline"></ion-icon> {{ $course['workload'] ?? (array_sum(array_column($content, 'lessons')) * 6).' min' }}</li>
                        <li><ion-icon name="star-outline"></ion-icon> {{ $course['rating'] ?? (is_numeric($course['progress']) && $course['progress'] > 0 ? '4.'.($course['progress'] % 10) : '4.5') }} ({{ $course['reviews_count'] ?? 126 }} reviews)</li>
                    </ul>

                    <div class="course-unified-grid">
                        <div class="course-unified-left">
                            
                            <div class="course-video-wrapper" style="background-color: {{ $course['banner_color'] ?? '#e8e2d4' }};">
                                <img src="{{ $course['image'] }}" alt="{{ $course['title'] }}" loading="lazy">
                                <div class="play-button-overlay">
                                    <ion-icon name="play"></ion-icon>
                                </div>
                            </div>

                            <ul class="course-tabs">
                                <li><button class="tab-btn active">Overview</button></li>
                                <li><button class="tab-btn">Author</button></li>
                                <li><button class="tab-btn">FAQ</button></li>
                                <li><button class="tab-btn">Announcements</button></li>
                                <li><button class="tab-btn">Reviews</button></li>
                            </ul>

                            <div class="course-panel no-border">
                                <h2 class="h3">About Course</h2>
                                <p class="course-description">
                                    {{ $course['description'] }}
                                </p>
                                
                                <br>
                                <h2 class="h3">What You'll Learn</h2>
                                <div class="course-learn-grid">
                                    @foreach ($learningPoints as $point)
                                        <div class="learn-item">
                                            <ion-icon name="checkmark-outline" class="check-icon"></ion-icon>
                                            <span>{{ $point }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>

                        <div class="course-unified-right">
                            <div class="course-panel sidebar-panel">
                                <h2 class="h3 panel-title">Course content</h2>
                                <ul class="course-accordion">
                                    @foreach ($content as $index => $part)
                                        <li class="{{ $index === 0 ? 'is-open' : '' }}">
                                            <div class="accordion-header">
                                                <div class="accordion-title">
                                                    <span class="part-num">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}:</span>
                                                    <span class="part-name">{{ \Illuminate\Support\Str::limit($part['title'], 25) }}</span>
                                                </div>
                                                <div class="accordion-meta">
                                                    <span class="duration">{{ $part['duration'] }}</span>
                                                    <ion-icon name="{{ $index === 0 ? 'chevron-up-outline' : 'chevron-down-outline' }}"></ion-icon>
                                                </div>
                                            </div>
                                            @php
                                                $sectionLessons = $index === 0
                                                    ? $introLessons
                                                    : [
                                                        ['title' => 'Key concepts in '.$part['title'], 'duration' => '4 min'],
                                                        ['title' => 'Guided practice: '.$part['title'], 'duration' => '6 min'],
                                                    ];
                                            @endphp
                                            <ul class="accordion-body">
                                                @foreach ($sectionLessons as $lesson)
                                                    <li>
                                                        <div class="lesson-left">
                                                            <ion-icon name="play-outline"></ion-icon>
                                                            <span>{{ $lesson['title'] }}</span>
                                                        </div>
                                                        <span class="lesson-duration">{{ $lesson['duration'] }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="course-panel sidebar-panel mt-4">
                                <h2 class="h3 panel-title">Author</h2>
                                <div class="author-card">
                                    <div class="author-header">
                                        <div class="author-avatar">
                                            <img src="{{ $course['author_image'] ?? 'https://ui-avatars.com/api/?name=Coursera+Instructor&background=0D8ABC&color=fff&rounded=true' }}" alt="{{ $course['author_name'] ?? 'Course Author' }}">
                                        </div>
                                        <div class="author-info">
                                            <h4>{{ $course['author_name'] ?? 'Course Author' }} <ion-icon name="checkmark-circle" class="verified-icon"></ion-icon></h4>
                                            <span>{{ $course['author_role'] ?? 'Course Instructor' }}</span>
                                        </div>
                                        <div class="author-rating">
                                            <ion-icon name="star"></ion-icon> {{ $course['rating'] ?? '4.8' }}
                                        </div>
                                    </div>
                                    <p class="author-bio">
                                        {{ $course['author_bio'] ?? 'Learn from trusted instructors and institutions on Coursera.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </section>
        </article>
    </main>
@endsection
