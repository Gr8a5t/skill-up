@extends('layouts.fitlife')

@section('title', 'SkillUp Courses')

@section('content')


    <main>
        <article>
            <section class="section course-landing" aria-label="course landing">
                <div class="container">
                    <div class="course-landing-inner">

                        <img src="{{ asset('fitlife-assets/images/course2.png') }}" alt="Course Image" class="course-image">
                        <div class="course-landing-content">
                            <p class="section-subtitle">Courses</p>
                            <h2 class="h2 section-title">Explore every SkillUp-approved course</h2>
                            <p class="section-text">
                                Curated selections from Coursera that match our tracks—free to audit, carefully tracked by
                                SkillUp mentors. Scroll through every option and open the ones that fit your journey.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            @include('fitlife.sections.courses')
        </article>
    </main>
@endsection
