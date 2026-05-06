@extends('layouts.dashboard')

@section('title', 'Learning Paths — Coming Soon')

@section('content')
<div style="width: 100%; height: 100vh; overflow: hidden; background: #ffffff;">
    <a href="{{ route('dashboard') }}">
        <img src="{{ asset('fitlife-assets/images/comingSoon.png') }}" alt="Coming Soon" style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </a>
</div>
@endsection
