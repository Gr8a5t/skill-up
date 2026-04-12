@extends('layouts.fitlife')

@section('title', 'SkillUp Paths')

@section('content')
    <main>
        <article>
            <section class="section path-hero" aria-label="path hero">
                <!-- Decorative Path Icon added without replacing main image -->
                <img src="{{ asset('fitlife-assets/images/Path-icon.png') }}" alt="Paths Icon Decoration" class="path-decoration-icon">
                <div class="container path-hero-inner">
                    <div class="hero-copy">
                        <p class="section-subtitle">Learning paths</p>
                        <h2 class="h2 section-title">Every SkillUp path is a practical roadmap</h2>
                        <p class="section-text">
                            Each track combines focused lessons, weekly checkpoints, and career-ready practice so you can move
                            from beginner steps to confident execution.
                        </p>
                        <div class="path-hero-actions">
                            <a href="{{ route('courses') }}" class="btn btn-primary">Browse matching courses</a>
                            <a href="#path-library" class="btn-link path-link-secondary">Explore path library</a>
                        </div>
                    </div>
                    <aside class="path-graphic-container" aria-label="Learning paths graphic" style="position: relative; z-index: 5;">
                        <figure class="path-image-wrapper" style="display: flex; justify-content: center; align-items: center; height: 100%;">
                            <img src="{{ asset('fitlife-assets/images/paths.png') }}" loading="lazy" alt="Learning paths graphic" class="w-100" style="width: 120%; max-width: unset; transform: scale(1.1) translate(5%, -20px); object-fit: contain; transform-origin: top right;">
                        </figure>
                    </aside>
                </div>
            </section>
            <section class="section path-grid" id="path-library" aria-label="path grid">
                <div class="container">
                    <div class="path-grid-header">
                        <p class="section-subtitle">Path library</p>
                        <h2 class="h2 section-title">Choose your next growth track</h2>
                        <p class="section-text">
                            Start with the path that matches your current goal, then use the linked courses to deepen your
                            skills step by step.
                        </p>
                    </div>
                    <div class="grid">
                        @foreach ($paths as $path)
                            <article class="path-card">
                                <div class="path-card-top">
                                    <span class="path-pill">{{ $path['duration'] }}</span>
                                    <span class="path-progress-label">{{ $path['progress'] }}% confidence</span>
                                </div>
                                <h3 class="h3 path-title">{{ $path['title'] }}</h3>
                                <p class="path-description">{{ $path['description'] }}</p>
                                <div class="path-track" role="progressbar" aria-valuenow="{{ $path['progress'] }}"
                                    aria-valuemin="0" aria-valuemax="100">
                                    <span style="width: {{ max(0, min(100, (int) $path['progress'])) }}%"></span>
                                </div>
                                <div class="path-meta">
                                    <span class="path-focus">{{ $path['focus'] }}</span>
                                    <a href="{{ route('paths.learn', $path['slug']) }}" class="btn-link path-card-link">Start Learning &rarr;</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        </article>
    </main>
@endsection
