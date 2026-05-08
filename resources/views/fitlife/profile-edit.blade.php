@extends('layouts.dashboard')

@section('title', 'Edit Profile | SkillUp')

@section('content')
<div class="content-area" style="display: block; padding: 0; background-color: #ffffff; min-height: calc(100vh - 80px);">
    <div style="width: 100%; margin: 0;">
        
        <div style="background: #fff; border-radius: 0; border: none; overflow: hidden; box-shadow: none;">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- AVATAR SECTION -->
                <div style="padding: 40px; border-bottom: 1px solid #f0f0f0; display: flex; align-items: center; gap: 40px; background: #fafafa;">
                    <div style="width: 140px; height: 140px; border-radius: 50%; overflow: hidden; background: #eee; border: 5px solid #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.1); flex-shrink: 0;">
                        <img src="{{ old('avatar', $user->avatar) ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&size=150&background=ff4500&color=fff' }}" alt="Profile Photo" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div style="flex-grow: 1;">
                        <h3 style="font-size: 1.8rem; color: #1c1c1c; margin-bottom: 12px; font-weight: 800;">Profile Photo</h3>
                        
                        <div style="display:flex; flex-direction:column; gap: 15px;">
                            <!-- File Upload -->
                            <div style="display:flex; align-items:center; gap: 10px;">
                                <label for="avatar_file" style="cursor:pointer; background:#fff; border:1px solid #ddd; padding: 10px 20px; border-radius: 10px; font-weight: 600; font-size: 1.4rem; display:flex; align-items:center; gap: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                                    <ion-icon name="cloud-upload-outline"></ion-icon> Upload new photo
                                </label>
                                <input type="file" id="avatar_file" name="avatar_file" style="display:none;" onchange="this.nextElementSibling.innerText = this.files[0].name">
                                <span style="font-size: 1.2rem; color: #888;"></span>
                            </div>

                            <!-- URL Alternative -->
                            <div style="margin-top: 10px;">
                                <p style="font-size: 1.2rem; color: #666; margin-bottom: 5px; font-weight:500;">Or use an Image URL:</p>
                                <input type="url" name="avatar_url" value="{{ old('avatar', $user->avatar && str_starts_with($user->avatar, 'http') && !str_contains($user->avatar, '/storage/avatars/') ? $user->avatar : '') }}" placeholder="https://example.com/photo.png" class="form-control" style="width: 100%; max-width:400px; padding: 0.8rem 1rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1.2rem; background: #fff;">
                            </div>
                        </div>
                        
                        <p style="color: #888; font-size: 1.2rem; margin-top: 15px;">At least 800x800 px recommended. JPG or PNG is allowed.</p>
                    </div>
                </div>

                <div style="padding: 40px; display: flex; flex-direction: column; gap: 3rem;">

                    <!-- PERSONAL INFO CARD -->
                    <div style="background: #fafafa; border: 1px solid #eaeaea; border-radius: 12px; padding: 1.5rem;">
                         <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.5rem;">
                            <h3 style="font-size: 1.2rem; color: #1c1c1c; font-weight: 700;">Personal Info</h3>
                            <span style="font-size: 0.9rem; color: #888;"><ion-icon name="lock-closed-outline"></ion-icon> Private info</span>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div>
                                <label style="display:block; font-weight: 500; margin-bottom: 0.5rem; color: #666; font-size:0.9rem;">Full Name</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-control" style="width: 100%; padding: 0.8rem 1rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; background:#fff;">
                                @error('name')<span style="color:var(--brand-primary); font-size: 0.8rem;">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label style="display:block; font-weight: 500; margin-bottom: 0.5rem; color: #666; font-size:0.9rem;">Job Title</label>
                                <input type="text" name="title" value="{{ old('title', $user->title) }}" placeholder="e.g. UX Designer" class="form-control" style="width: 100%; padding: 0.8rem 1rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; background:#fff;">
                            </div>
                        </div>
                    </div>

                    <!-- LOCATION CARD -->
                    <div style="background: #fafafa; border: 1px solid #eaeaea; border-radius: 12px; padding: 1.5rem;">
                        <h3 style="font-size: 1.2rem; color: #1c1c1c; margin-bottom: 1.5rem; font-weight: 700;">Location</h3>
                        <div style="position: relative;">
                            <ion-icon name="compass-outline" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); font-size: 1.2rem; color: #888;"></ion-icon>
                            <input type="text" name="location" value="{{ old('location', $user->location) }}" placeholder="e.g. California" class="form-control" style="width: 100%; padding: 0.9rem 1rem 0.9rem 2.8rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; background:#fff;">
                        </div>
                    </div>

                    <!-- BIO CARD -->
                    <div style="background: #fafafa; border: 1px solid #eaeaea; border-radius: 12px; padding: 1.5rem;">
                        <h3 style="font-size: 1.2rem; color: #1c1c1c; margin-bottom: 1.5rem; font-weight: 700;">Bio</h3>
                        <textarea name="bio" rows="4" class="form-control" placeholder="Hi 👋, I'm..." style="width: 100%; padding: 1rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; background:#fff; line-height:1.6; resize:vertical;">{{ old('bio', $user->bio) }}</textarea>
                    </div>

                    <!-- SOCIAL LINKS CARD -->
                    <div style="background: #fafafa; border: 1px solid #eaeaea; border-radius: 12px; padding: 1.5rem;">
                        <h3 style="font-size: 1.2rem; color: #1c1c1c; margin-bottom: 1.5rem; font-weight: 700;">Social Links</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div style="position: relative;">
                                <ion-icon name="logo-linkedin" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); font-size: 1.2rem; color: #0077b5;"></ion-icon>
                                <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $user->linkedin_url) }}" placeholder="LinkedIn URL" class="form-control" style="width: 100%; padding: 0.9rem 1rem 0.9rem 2.8rem; border: 1px solid #ddd; border-radius: 8px; font-size: 0.95rem; background:#fff;">
                            </div>
                            <div style="position: relative;">
                                <ion-icon name="logo-github" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); font-size: 1.2rem; color: #333;"></ion-icon>
                                <input type="url" name="github_url" value="{{ old('github_url', $user->github_url) }}" placeholder="GitHub URL" class="form-control" style="width: 100%; padding: 0.9rem 1rem 0.9rem 2.8rem; border: 1px solid #ddd; border-radius: 8px; font-size: 0.95rem; background:#fff;">
                            </div>
                        </div>
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div style="display:flex; justify-content:flex-end; gap: 1rem; margin-top: 1rem;">
                        <a href="{{ route('profile.show', $user) }}" style="background:#fff; color:#555; padding:0.8rem 1.5rem; border:1px solid #ddd; border-radius:8px; font-weight:600; text-decoration:none; cursor:pointer;">Cancel</a>
                        <button type="submit" style="background:var(--brand-primary); color:#fff; padding:0.8rem 2.5rem; border:none; border-radius:8px; font-weight:700; cursor:pointer; box-shadow: 0 4px 10px rgba(255, 69, 0, 0.2);">Save changes</button>
                    </div>

                </div>
            </form>
        </div>

    </div>
</div>

@push('styles')
<style>
    @media (max-width: 768px) {
        .content-area { padding: 0 !important; }
        
        div[style*="padding: 40px"] {
            padding: 2rem !important;
            flex-direction: column !important;
            text-align: center;
            gap: 20px !important;
        }

        div[style*="width: 140px"] {
            width: 120px !important;
            height: 120px !important;
        }

        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }

        div[style*="display:flex; align-items:center; gap: 10px;"] {
            flex-direction: column !important;
            align-items: center !important;
        }

        div[style*="justify-content:flex-end"] {
            flex-direction: column-reverse;
            gap: 10px !important;
            padding: 0 20px 20px !important;
        }
        
        div[style*="justify-content:flex-end"] button, 
        div[style*="justify-content:flex-end"] a {
            width: 100%;
            text-align: center;
        }
    }
</style>
@endpush
@endsection
