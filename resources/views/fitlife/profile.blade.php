@extends('layouts.dashboard')

@section('title', $user->name . ' | SkillUp Profile')

@section('content')
<div class="content-area" style="display: block; padding: 2.5rem; background-color: #f9f9fb; min-height: calc(100vh - 80px);">
    <div style="max-width: 900px; margin: 0 auto;">
        
        <!-- Header Card -->
        <div style="background: #fff; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 30px; border: 1px solid #eaeaea;">
            <div style="height: 140px; background: linear-gradient(135deg, var(--brand-primary), #ff8058);"></div>
            
            <div style="padding: 0 30px 30px; position: relative;">
                <!-- Avatar -->
                <div style="width: 130px; height: 130px; border-radius: 50%; border: 5px solid #fff; position: absolute; top: -65px; left: 30px; overflow: hidden; background: #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                    <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&size=150&background=ff4500&color=fff' }}" 
                         alt="{{ $user->name }}" 
                         style="width: 100%; height: 100%; object-fit: cover;">
                </div>

                <div style="padding-top: 75px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-end; gap: 20px;">
                    <div>
                        <h1 style="font-size: 2.4rem; font-weight: 800; color: #1c1c1c; margin-bottom: 4px;">{{ $user->name }}</h1>
                        @if($user->title)
                            <p style="font-size: 1.5rem; color: #555; font-weight: 500; margin-bottom: 6px;">{{ $user->title }}</p>
                        @endif
                        @if($user->location)
                            <p style="font-size: 1.3rem; color: #888; display: flex; align-items: center; gap: 5px;">
                                <ion-icon name="location-outline"></ion-icon> {{ $user->location }}
                            </p>
                        @endif
                    </div>

                    <div style="display: flex; gap: 12px; margin-bottom: 5px;">
                        @if(auth()->check() && auth()->id() === $user->id)
                            <a href="{{ route('profile.edit') }}" class="btn" style="background: var(--brand-primary); color: #fff; padding: 10px 22px; border-radius: 10px; font-weight: 700; text-transform: none; display: flex; align-items: center; gap: 8px; font-size: 1.3rem; text-decoration:none;">
                                <ion-icon name="create-outline"></ion-icon> Edit Profile
                            </a>
                        @elseif(auth()->check())
                            <a href="{{ route('dashboard.chats', ['user_id' => $user->getRouteKey()]) }}" class="btn" style="background: var(--brand-primary); color: #fff; padding: 10px 22px; border-radius: 10px; font-weight: 700; text-transform: none; display: flex; align-items: center; gap: 8px; font-size: 1.3rem; text-decoration:none;">
                                <ion-icon name="chatbubbles-outline"></ion-icon> Message
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1.8fr 1fr; gap: 25px;">
            <!-- Left Column: About -->
            <div style="display: flex; flex-direction: column; gap: 25px;">
                <div style="background: #fff; padding: 25px; border-radius: 16px; border: 1px solid #eaeaea; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px; border-bottom: 1px solid #f5f5f5; padding-bottom: 12px;">
                         <h2 style="font-size: 1.6rem; font-weight: 800; color: #1c1c1c;">About</h2>
                         @if(auth()->check() && auth()->id() === $user->id)
                            <a href="{{ route('profile.edit') }}" style="color:var(--text-mut);"><ion-icon name="create-outline"></ion-icon></a>
                         @endif
                    </div>
                    @if($user->bio)
                        <div style="font-size: 1.4rem; line-height: 1.7; color: #444; white-space: pre-line;">
                            {{ $user->bio }}
                        </div>
                    @else
                        <p style="font-size: 1.4rem; color: #aaa; font-style: italic;">No bio written yet.</p>
                    @endif
                </div>
            </div>

            <!-- Right Column: Sidebar info -->
            <div style="display: flex; flex-direction: column; gap: 25px;">
                <div style="background: #fff; padding: 25px; border-radius: 16px; border: 1px solid #eaeaea; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                    <h3 style="font-size: 1.6rem; font-weight: 800; color: #1c1c1c; margin-bottom: 18px;">Social Links</h3>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @if($user->github_url)
                            <a href="{{ $user->github_url }}" target="_blank" style="display:flex; align-items:center; gap:10px; color:#333; font-size:1.4rem; text-decoration:none;">
                                <ion-icon name="logo-github" style="font-size:1.8rem;"></ion-icon> GitHub
                            </a>
                        @endif
                        @if($user->linkedin_url)
                            <a href="{{ $user->linkedin_url }}" target="_blank" style="display:flex; align-items:center; gap:10px; color:#0077b5; font-size:1.4rem; text-decoration:none;">
                                <ion-icon name="logo-linkedin" style="font-size:1.8rem;"></ion-icon> LinkedIn
                            </a>
                        @endif
                        @if(!$user->github_url && !$user->linkedin_url)
                            <p style="font-size:1.3rem; color:#aaa; font-style:italic;">No social links added.</p>
                        @endif
                    </div>
                </div>

                <div style="background: #fff; padding: 25px; border-radius: 16px; border: 1px solid #eaeaea; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                    <h3 style="font-size: 1.6rem; font-weight: 800; color: #1c1c1c; margin-bottom: 18px;">Statistics</h3>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <div style="display: flex; justify-content: space-between; font-size: 1.3rem;">
                            <span style="color: #888;">Joined</span>
                            <span style="font-weight: 700; color: #333;">{{ $user->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
    @media (max-width: 992px) {
        .content-area { padding: 1.5rem !important; }
        div[style*="grid-template-columns: 1.8fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endpush
