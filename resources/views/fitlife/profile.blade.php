@extends('layouts.dashboard')

@section('title', $user->name . ' | SkillUp Profile')

@section('content')
<div class="content-area" style="display: block; padding: 4rem; background-color: #ffffff; min-height: calc(100vh - 80px);">
    <div style="max-width: 1100px; margin: 0 auto; display: flex; flex-direction: column; gap: 40px;">
        
        <!-- Top Section -->
        <div class="profile-top-section" style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: start;">
            <!-- Left Info -->
            <div>
                <!-- Avatar and Status -->
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                    <div style="position: relative;">
                        <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&size=150&background=ff4500&color=fff' }}" 
                             alt="{{ $user->name }}" 
                             style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
                        <div style="position: absolute; bottom: 5px; right: 5px; width: 14px; height: 14px; background: #22c55e; border: 2px solid #fff; border-radius: 50%;"></div>
                    </div>
                </div>

                <!-- Name & Title -->
                <h1 style="font-size: 3.5rem; font-weight: 700; color: #1c1c1c; margin-bottom: 8px; letter-spacing: -1px;">{{ $user->name }}</h1>
                <p style="font-size: 1.8rem; color: #666; font-weight: 500; margin-bottom: 30px;">
                    {{ $user->title ?? 'SkillUp User' }}
                </p>

                <!-- Actions -->
                <div style="display: flex; gap: 15px; align-items: center;">
                    @if(auth()->check() && auth()->id() === $user->id)
                        <div style="font-weight: 600; font-size: 1.4rem; padding: 12px 24px; border: 1px solid #eaeaea; border-radius: 30px; color: #1c1c1c; background: #fff;">
                            0 Followers
                        </div>
                        <button style="width: 48px; height: 48px; border-radius: 50%; border: 1px solid #eaeaea; background: transparent; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #444; font-size: 1.8rem;">
                            <ion-icon name="stats-chart-outline"></ion-icon>
                        </button>
                        <button style="width: 48px; height: 48px; border-radius: 50%; border: 1px solid #eaeaea; background: transparent; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #444; font-size: 1.8rem;">
                            <ion-icon name="share-outline"></ion-icon>
                        </button>
                    @else
                        <a href="{{ route('dashboard.chats', ['user_id' => $user->getRouteKey()]) }}" style="background: #1c1c28; color: #fff; padding: 12px 24px; border-radius: 30px; font-weight: 600; font-size: 1.4rem; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                            Message
                        </a>
                        <button style="background: #fff; color: #1c1c28; border: 1px solid #eaeaea; padding: 12px 24px; border-radius: 30px; font-weight: 600; font-size: 1.4rem; cursor: pointer;">
                            Follow
                        </button>
                    @endif
                </div>
            </div>

            <!-- Right Media Box -->
            <div style="border: 1px solid #eaeaea; border-radius: 16px; padding: 40px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; height: 100%; min-height: 250px; background: #fdfdfd; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='#fdfdfd'">
                <ion-icon name="image-outline" style="font-size: 3rem; color: #888; margin-bottom: 15px;"></ion-icon>
                <h3 style="font-size: 1.6rem; color: #333; font-weight: 600; margin-bottom: 8px;">Add featured media</h3>
                <p style="font-size: 1.4rem; color: #666; margin-bottom: 15px;">Drag and drop or <span style="text-decoration: underline;">browse</span></p>
                <p style="font-size: 1.1rem; color: #aaa; max-width: 300px; line-height: 1.5;">We recommend a video (mp4) or image (png, jpg, gif) in a 4:3, 5:4, 9:16, or 16:9 aspect ratio. Max 200MB.</p>
            </div>
        </div>

        <!-- Navigation Bar -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eaeaea; padding-bottom: 15px; flex-wrap: wrap; gap: 20px;">
            <div style="display: flex; gap: 30px;">
                <a href="#work" onclick="switchTab('work'); return false;" id="tab-work" class="profile-tab active-tab" style="font-size: 1.5rem; font-weight: 700; color: #1c1c1c; text-decoration: none; padding-bottom: 15px; margin-bottom: -16px; border-bottom: 2px solid #1c1c1c; transition: 0.2s;">Work</a>
                <a href="#about" onclick="switchTab('about'); return false;" id="tab-about" class="profile-tab" style="font-size: 1.5rem; font-weight: 700; color: #888; text-decoration: none; padding-bottom: 15px; margin-bottom: -16px; border-bottom: 2px solid transparent; transition: 0.2s;">About</a>
            </div>
            
            <div style="display: flex; align-items: center; gap: 20px;">
                @if($user->location)
                    <div style="display: flex; align-items: center; gap: 5px; font-size: 1.3rem; color: #444; padding-right: 20px; border-right: 1px solid #eaeaea;">
                        <ion-icon name="location-outline"></ion-icon> {{ $user->location }}
                    </div>
                @endif
                <button onclick="openSocialModal()" style="background: #f4f4f5; color: #333; border: none; padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 1.3rem; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                    <ion-icon name="add"></ion-icon> Add social links
                </button>
            </div>
        </div>

        <!-- Content Area -->
        <div id="content-work" class="tab-content" style="display: block; margin-top: 30px;">
            <div style="background: #fdfdfd; border-radius: 16px; padding: 80px 40px; text-align: center; position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center; border: 1px solid #f0f0f0;">
                <h2 style="font-size: 2.2rem; font-weight: 700; color: #1c1c1c; margin-bottom: 12px;">Feature your work</h2>
                <p style="font-size: 1.5rem; color: #666; margin-bottom: 30px;">Share quick snapshots of what you've been working on.</p>
                <div style="display: flex; justify-content: center;">
                    <button onclick="openWorkModal()" style="background: #1c1c28; color: #fff; border: none; padding: 12px 30px; border-radius: 30px; font-weight: 600; font-size: 1.4rem; cursor: pointer;">Add work</button>
                </div>
            </div>
        </div>

        <div id="content-about" class="tab-content" style="display: none; margin-top: 30px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                <!-- Left Side -->
                <div>
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 30px;">
                        <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&size=150&background=ff4500&color=fff' }}" 
                             alt="{{ $user->name }}" 
                             style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover;">
                        <h2 style="font-size: 2.2rem; font-weight: 700; color: #1c1c1c;">Meet {{ explode(' ', trim($user->name))[0] }}</h2>
                    </div>
                    
                    <p style="font-size: 1.5rem; color: #666; font-weight: 600; margin-bottom: 40px;">1 following</p>
                    
                    @if($user->bio)
                        <div id="bio-display-wrapper" style="font-size: 1.6rem; line-height: 1.8; color: #1c1c1c; white-space: pre-line; margin-bottom: 40px; position: relative;" class="bio-block">
                            {{ $user->bio }}
                            @if(auth()->check() && auth()->id() === $user->id)
                                <a href="javascript:void(0)" onclick="toggleBioEdit()" style="color: #888; margin-left: 10px;"><ion-icon name="create-outline"></ion-icon></a>
                            @endif
                        </div>
                    @else
                        @if(auth()->check() && auth()->id() === $user->id)
                            <div id="bio-add-wrapper" onclick="toggleBioEdit()" style="display: flex; align-items: center; gap: 10px; color: #1c1c1c; font-size: 1.5rem; cursor: pointer; margin-bottom: 40px;">
                                <ion-icon name="add" style="font-size: 2rem; color: #666;"></ion-icon> Add a descriptive bio
                            </div>
                        @endif
                    @endif

                    @if(auth()->check() && auth()->id() === $user->id)
                    <!-- Inline Bio Edit Form -->
                    <div id="bio-edit-wrapper" style="display: none; margin-bottom: 40px;">
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="name" value="{{ $user->name }}">
                            <input type="hidden" name="title" value="{{ $user->title }}">
                            <input type="hidden" name="location" value="{{ $user->location }}">
                            
                            <div style="position: relative;">
                                <textarea name="bio" id="bio-textarea" oninput="updateBioCount()" placeholder="Add your bio" style="width: 100%; border: none; border-bottom: 1px solid #eaeaea; outline: none; font-size: 1.6rem; color: #1c1c1c; font-family: inherit; resize: none; min-height: 60px; padding-bottom: 25px; background: transparent;">{{ old('bio', $user->bio) }}</textarea>
                                <div style="position: absolute; bottom: 5px; right: 0; font-size: 1.3rem; color: #888;" id="bio-count">0/400</div>
                            </div>
                            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px;">
                                <button type="button" onclick="toggleBioEdit()" style="background: transparent; color: #666; border: none; font-weight: 600; font-size: 1.3rem; cursor: pointer; padding: 8px 15px;">Cancel</button>
                                <button type="submit" style="background: #1c1c28; color: #fff; border: none; padding: 8px 20px; border-radius: 20px; font-weight: 600; font-size: 1.3rem; cursor: pointer;">Save</button>
                            </div>
                        </form>
                    </div>
                    @endif

                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
                        <h3 style="font-size: 1.2rem; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 1px;">Social Links</h3>
                        @if(auth()->check() && auth()->id() === $user->id && ($user->github_url || $user->linkedin_url))
                            <a href="{{ route('profile.edit') }}" style="color: #888; font-size: 1.5rem;"><ion-icon name="create-outline"></ion-icon></a>
                        @endif
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        @if($user->github_url)
                            <a href="{{ $user->github_url }}" target="_blank" style="display:flex; align-items:center; gap:10px; color:#1c1c1c; font-size:1.5rem; text-decoration:none; font-weight: 600;">
                                <ion-icon name="logo-github" style="font-size:2rem;"></ion-icon> GitHub
                            </a>
                        @endif
                        @if($user->linkedin_url)
                            <a href="{{ $user->linkedin_url }}" target="_blank" style="display:flex; align-items:center; gap:10px; color:#0077b5; font-size:1.5rem; text-decoration:none; font-weight: 600;">
                                <ion-icon name="logo-linkedin" style="font-size:2rem;"></ion-icon> LinkedIn
                            </a>
                        @endif
                        @if(!$user->github_url && !$user->linkedin_url)
                            @if(auth()->check() && auth()->id() === $user->id)
                                <a href="javascript:void(0)" onclick="openSocialModal()" style="text-decoration: none;">
                                    <div style="display: flex; align-items: center; gap: 10px; color: #1c1c1c; font-size: 1.5rem; cursor: pointer;">
                                        <ion-icon name="add" style="font-size: 2rem; color: #666;"></ion-icon> Add social links
                                    </div>
                                </a>
                            @else
                                <p style="font-size:1.4rem; color:#888;">No social links added.</p>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Right Side Card -->
                <div>
                    <div style="border: 1px solid #eaeaea; border-radius: 16px; padding: 30px; background: #fff;">
                        <div style="margin-bottom: 30px;">
                            <h4 style="font-size: 1.1rem; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px;">Rate</h4>
                            @if(auth()->check() && auth()->id() === $user->id)
                                <a href="{{ route('profile.edit') }}" style="text-decoration: none;">
                                    <div style="display: flex; align-items: center; gap: 10px; color: #1c1c1c; font-size: 1.5rem; font-weight: 500; cursor: pointer;">
                                        <ion-icon name="add" style="font-size: 2rem; color: #666;"></ion-icon> Add hourly rate
                                    </div>
                                </a>
                            @else
                                <p style="font-size:1.4rem; color:#888;">Not specified</p>
                            @endif
                        </div>

                        <div id="details-display-wrapper">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
                                <h4 style="font-size: 1.1rem; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 1px;">Details</h4>
                                @if(auth()->check() && auth()->id() === $user->id)
                                    <a href="javascript:void(0)" onclick="toggleLocationEdit()" style="color: #888; font-size: 1.5rem;"><ion-icon name="create-outline"></ion-icon></a>
                                @endif
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 15px;">
                                @if($user->location)
                                    <div style="display: flex; align-items: center; gap: 10px; color: #1c1c1c; font-size: 1.5rem; font-weight: 500;">
                                        <ion-icon name="location-outline" style="font-size: 2rem; color: #666;"></ion-icon> {{ $user->location }}
                                    </div>
                                @else
                                    @if(auth()->check() && auth()->id() === $user->id)
                                        <a href="javascript:void(0)" onclick="toggleLocationEdit()" style="text-decoration: none;">
                                            <div style="display: flex; align-items: center; gap: 10px; color: #1c1c1c; font-size: 1.5rem; font-weight: 500; cursor: pointer;">
                                                <ion-icon name="add" style="font-size: 2rem; color: #666;"></ion-icon> Add location
                                            </div>
                                        </a>
                                    @endif
                                @endif
                                
                                @if(auth()->check() && auth()->id() === $user->id)
                                    <a href="{{ route('profile.edit') }}" style="text-decoration: none;">
                                        <div style="display: flex; align-items: center; gap: 10px; color: #1c1c1c; font-size: 1.5rem; font-weight: 500; cursor: pointer;">
                                            <ion-icon name="add" style="font-size: 2rem; color: #666;"></ion-icon> Add timezone
                                        </div>
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if(auth()->check() && auth()->id() === $user->id)
                        <!-- Inline Location Edit Form -->
                        <div id="location-edit-wrapper" style="display: none;">
                            <form action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="name" value="{{ $user->name }}">
                                <input type="hidden" name="title" value="{{ $user->title }}">
                                <input type="hidden" name="bio" value="{{ $user->bio }}">
                                
                                <h4 style="font-size: 1.4rem; font-weight: 600; color: #1c1c1c; margin-bottom: 10px;">Location</h4>
                                <input type="text" name="location" id="location-input" value="{{ old('location', $user->location) }}" style="width: 100%; border: 1px solid #eaeaea; border-radius: 8px; padding: 12px 15px; outline: none; font-size: 1.5rem; color: #1c1c1c; font-family: inherit; margin-bottom: 15px;" placeholder="City, Country">
                                
                                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                    <button type="button" onclick="toggleLocationEdit()" style="background: transparent; color: #666; border: none; font-weight: 600; font-size: 1.3rem; cursor: pointer; padding: 8px 15px;">Cancel</button>
                                    <button type="submit" style="background: #1c1c28; color: #fff; border: none; padding: 8px 20px; border-radius: 20px; font-weight: 600; font-size: 1.3rem; cursor: pointer;">Save</button>
                                </div>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Social Links Modal -->
<div id="socialModalOverlay" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 1050; align-items: center; justify-content: center; backdrop-filter: blur(2px);">
    <div style="background: #fff; width: 90%; max-width: 500px; border-radius: 20px; padding: 30px; position: relative; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
        <button onclick="closeSocialModal()" style="position: absolute; right: 20px; top: 20px; background: transparent; border: none; font-size: 2.4rem; color: #444; cursor: pointer;">
            <ion-icon name="close-outline"></ion-icon>
        </button>
        
        <h2 style="font-size: 2.2rem; font-weight: 700; color: #1c1c1c; margin-bottom: 10px;">Social links</h2>
        <p style="font-size: 1.4rem; color: #666; margin-bottom: 25px;">Add links that showcase your work, recognition, personality and more!</p>
        
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <button class="social-modal-btn" onclick="showSocialInput('linkedin')">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <ion-icon name="logo-linkedin"></ion-icon> LinkedIn
                </div>
                <ion-icon name="add" style="color: #666; font-size: 2rem;"></ion-icon>
            </button>
            <button class="social-modal-btn" onclick="showSocialInput('twitter')">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <ion-icon name="logo-twitter"></ion-icon> X / Twitter
                </div>
                <ion-icon name="add" style="color: #666; font-size: 2rem;"></ion-icon>
            </button>
            <button class="social-modal-btn" onclick="showSocialInput('instagram')">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <ion-icon name="logo-instagram"></ion-icon> Instagram
                </div>
                <ion-icon name="add" style="color: #666; font-size: 2rem;"></ion-icon>
            </button>
            <button class="social-modal-btn" onclick="showSocialInput('portfolio')">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <ion-icon name="globe-outline"></ion-icon> Portfolio
                </div>
                <ion-icon name="add" style="color: #666; font-size: 2rem;"></ion-icon>
            </button>
            <button class="social-modal-btn" onclick="showSocialInput('other')">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <ion-icon name="arrow-up-right-outline"></ion-icon> Other
                </div>
                <ion-icon name="add" style="color: #666; font-size: 2rem;"></ion-icon>
            </button>
        </div>
    </div>
</div>

<!-- Add Work Modal -->
<div id="workModalOverlay" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 1050; align-items: center; justify-content: center; backdrop-filter: blur(2px);">
    <div style="background: #fff; width: 90%; max-width: 650px; border-radius: 20px; padding: 25px 30px; position: relative; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
        
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px;">
            <div style="display: flex; align-items: center; gap: 15px; flex-grow: 1;">
                <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&size=150&background=ff4500&color=fff' }}" alt="{{ $user->name }}" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;">
                <input type="text" placeholder="Tell us a bit about this work" style="border: none; outline: none; font-size: 1.8rem; color: #1c1c1c; font-weight: 500; width: 100%; background: transparent;" />
            </div>
            <button onclick="closeWorkModal()" style="background: transparent; border: none; font-size: 2.8rem; color: #444; cursor: pointer; display: flex; align-items: center; justify-content: center; margin-left: 15px;">
                <ion-icon name="close-outline"></ion-icon>
            </button>
        </div>

        <div style="border: 1px solid #eaeaea; border-radius: 12px; padding: 60px 20px; text-align: center; background: #fafafa; margin-bottom: 30px; cursor: pointer; transition: 0.2s;" onmouseover="this.style.borderColor='#ccc'" onmouseout="this.style.borderColor='#eaeaea'">
            <ion-icon name="image-outline" style="font-size: 3.5rem; color: #bbb; margin-bottom: 15px;"></ion-icon>
            <h3 style="font-size: 1.6rem; font-weight: 600; color: #1c1c1c; margin-bottom: 10px;">Share visuals from a recent project</h3>
            <p style="font-size: 1.4rem; color: #888;">Drop a file or <span style="text-decoration: underline; color: #444; font-weight: 600;">Browse</span></p>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between;">
            <button style="background: transparent; border: none; font-size: 2.4rem; color: #666; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s;" onmouseover="this.style.color='#1c1c28'" onmouseout="this.style.color='#666'">
                <ion-icon name="happy-outline"></ion-icon>
            </button>
            <button style="background: #1c1c28; color: #fff; border: none; padding: 12px 35px; border-radius: 30px; font-weight: 600; font-size: 1.5rem; cursor: pointer; transition: 0.2s;">Next</button>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .social-modal-btn {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        background: #fff;
        border: 1px solid #eaeaea;
        border-radius: 12px;
        font-size: 1.5rem;
        font-weight: 500;
        color: #1c1c1c;
        cursor: pointer;
        transition: 0.2s;
    }
    .social-modal-btn:hover {
        border-color: #ccc;
        background: #fafafa;
    }
    .social-modal-btn ion-icon:first-child {
        font-size: 2rem;
        color: #1c1c1c;
    }
    @media (max-width: 992px) {
        .content-area { padding: 2rem !important; }
        .profile-top-section {
            grid-template-columns: 1fr !important;
        }
        .profile-top-section > div:first-child { margin-bottom: 20px; }
    }
</style>
@endpush

@push('scripts')
<script>
    function switchTab(tabId) {
        // Update tabs styling
        document.querySelectorAll('.profile-tab').forEach(el => {
            el.style.color = '#888';
            el.style.borderBottomColor = 'transparent';
        });
        const activeTab = document.getElementById('tab-' + tabId);
        activeTab.style.color = '#1c1c1c';
        activeTab.style.borderBottomColor = '#1c1c1c';

        // Show/hide content
        document.querySelectorAll('.tab-content').forEach(el => {
            el.style.display = 'none';
        });
        document.getElementById('content-' + tabId).style.display = 'block';
    }
    
    // Check url hash to open correct tab on load
    document.addEventListener('DOMContentLoaded', () => {
        if(window.location.hash) {
            const tab = window.location.hash.substring(1);
            if(document.getElementById('tab-' + tab)) {
                switchTab(tab);
            }
        }
        
        // Initial count if bio edit is present
        const bioTextarea = document.getElementById('bio-textarea');
        if(bioTextarea) {
            updateBioCount();
        }
    });

    function toggleBioEdit() {
        const displayWrapper = document.getElementById('bio-display-wrapper');
        const addWrapper = document.getElementById('bio-add-wrapper');
        const editWrapper = document.getElementById('bio-edit-wrapper');
        
        if (editWrapper.style.display === 'none') {
            if(displayWrapper) displayWrapper.style.display = 'none';
            if(addWrapper) addWrapper.style.display = 'none';
            editWrapper.style.display = 'block';
            updateBioCount();
            document.getElementById('bio-textarea').focus();
        } else {
            if(displayWrapper) displayWrapper.style.display = 'block';
            if(addWrapper) addWrapper.style.display = 'flex';
            editWrapper.style.display = 'none';
        }
    }

    function updateBioCount() {
        const textarea = document.getElementById('bio-textarea');
        const count = document.getElementById('bio-count');
        if(!textarea || !count) return;
        
        const length = textarea.value.length;
        count.innerText = length + '/400';
        if(length > 400) {
            count.style.color = 'red';
        } else {
            count.style.color = '#888';
        }
    }

    function openSocialModal() {
        document.getElementById('socialModalOverlay').style.display = 'flex';
    }

    function closeSocialModal() {
        document.getElementById('socialModalOverlay').style.display = 'none';
    }

    function showSocialInput(platform) {
        // Close modal and redirect to profile.edit for now to actually add the link
        closeSocialModal();
        window.location.href = "{{ route('profile.edit') }}";
    }

    function toggleLocationEdit() {
        const displayWrapper = document.getElementById('details-display-wrapper');
        const editWrapper = document.getElementById('location-edit-wrapper');
        
        if (editWrapper.style.display === 'none') {
            if(displayWrapper) displayWrapper.style.display = 'none';
            editWrapper.style.display = 'block';
            document.getElementById('location-input').focus();
        } else {
            if(displayWrapper) displayWrapper.style.display = 'block';
            editWrapper.style.display = 'none';
        }
    }

    function openWorkModal() {
        document.getElementById('workModalOverlay').style.display = 'flex';
    }

    function closeWorkModal() {
        document.getElementById('workModalOverlay').style.display = 'none';
    }
</script>
@endpush
