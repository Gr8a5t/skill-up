@extends('layouts.dashboard')

@section('title', 'Forum')

@section('content')
<div style="padding: 20px;">
    @livewire('forum-component')
</div>
@endsection
