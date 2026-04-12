@extends('layouts.fitlife')

@section('title', 'SkillUp — Build practical skills for free')

@section('content')
    <main>
        <article>
            @include('fitlife.sections.hero')
            @include('fitlife.sections.about')
            @include('fitlife.sections.video')
            @include('fitlife.sections.class')
            @include('fitlife.sections.blog')
        </article>
    </main>
@endsection
