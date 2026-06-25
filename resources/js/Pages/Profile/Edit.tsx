import React, { useState, useEffect } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import DashboardLayout from '../../Layouts/DashboardLayout';

interface User {
    id: number;
    route_key: string;
    name: string;
    avatar?: string;
    bio?: string;
    title?: string;
    location?: string;
    github_url?: string;
    linkedin_url?: string;
}

interface ProfileEditProps {
    user: User;
}

export default function Edit({ user }: ProfileEditProps) {
    const [avatarPreview, setAvatarPreview] = useState(
        user.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&size=150&background=ff4500&color=fff`
    );

    const { data, setData, post, processing, errors } = useForm({
        _method: 'PUT',
        name: user.name,
        title: user.title || '',
        location: user.location || '',
        bio: user.bio || '',
        linkedin_url: user.linkedin_url || '',
        github_url: user.github_url || '',
        avatar_url: (user.avatar && user.avatar.startsWith('http')) ? user.avatar : '',
        avatar_file: null as File | null,
    });

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            setData('avatar_file', file);
            setData('avatar_url', ''); // Clear URL since file is prioritized
            
            // Create preview
            const reader = new FileReader();
            reader.onload = (event) => {
                if (event.target?.result) {
                    setAvatarPreview(event.target.result as string);
                }
            };
            reader.readAsDataURL(file);
        }
    };

    const handleUrlChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const url = e.target.value;
        setData('avatar_url', url);
        setData('avatar_file', null); // Clear file since URL is selected
        if (url) {
            setAvatarPreview(url);
        } else {
            setAvatarPreview(
                user.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&size=150&background=ff4500&color=fff`
            );
        }
    };

    const submitForm = (e: React.FormEvent) => {
        e.preventDefault();
        
        // We post to the endpoint with _method: 'PUT' in the payload.
        // This is Inertia's standard method for submitting multipart files via PUT
        post('/profile/edit', {
            forceFormData: true,
        });
    };

    return (
        <DashboardLayout title="Edit Profile | SkillUp">
            <style dangerouslySetInnerHTML={{__html: `
                .form-control { width: 100%; padding: 0.8rem 1rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1.4rem; background: #fff; box-sizing: border-box; }
                .form-control:focus { outline: none; border-color: var(--brand-primary); }
                
                @media (max-width: 768px) {
                    .edit-container { padding: 0 !important; }
                    .avatar-section { padding: 2rem !important; flex-direction: column !important; text-align: center; gap: 20px !important; }
                    .avatar-wrapper { width: 120px !important; height: 120px !important; }
                    .grid-columns { grid-template-columns: 1fr !important; }
                    .action-buttons { flex-direction: column-reverse; gap: 10px !important; padding: 0 20px 20px !important; }
                    .action-buttons button, .action-buttons a { width: 100%; text-align: center; }
                }
            `}} />

            <div className="content-area" style={{ display: 'block', padding: 0, backgroundColor: '#ffffff', minHeight: 'calc(100vh - 80px)' }}>
                <div style={{ width: '100%', margin: 0, textAlign: 'left' }}>
                    <div style={{ background: '#fff', borderRadius: 0, border: 'none', overflow: 'hidden', boxShadow: 'none' }}>
                        <form onSubmit={submitForm}>
                            
                            {/* AVATAR SECTION */}
                            <div className="avatar-section" style={{ padding: '40px', borderBottom: '1px solid #f0f0f0', display: 'flex', alignItems: 'center', gap: '40px', background: '#fafafa' }}>
                                <div className="avatar-wrapper" style={{ width: '140px', height: '140px', borderRadius: '50%', overflow: 'hidden', background: '#eee', border: '5px solid #fff', boxShadow: '0 4px 12px rgba(0,0,0,0.1)', flexShrink: 0 }}>
                                    <img src={avatarPreview} alt="Profile Photo" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                                </div>
                                <div style={{ flexGrow: 1 }}>
                                    <h3 style={{ fontSize: '1.8rem', color: '#1c1c1c', marginBottom: '12px', fontWeight: 800 }}>Profile Photo</h3>
                                    
                                    <div style={{ display: 'flex', flexDirection: 'column', gap: '15px' }}>
                                        {/* File Upload */}
                                        <div style={{ display: 'flex', alignItems: 'center', gap: '10px', flexWrap: 'wrap' }}>
                                            <label htmlFor="avatar_file" style={{ cursor: 'pointer', background: '#fff', border: '1px solid #ddd', padding: '10px 20px', borderRadius: '10px', fontWeight: 600, fontSize: '1.4rem', display: 'flex', alignItems: 'center', gap: '8px', boxShadow: '0 2px 5px rgba(0,0,0,0.05)' }}>
                                                <ion-icon name="cloud-upload-outline"></ion-icon> Upload new photo
                                            </label>
                                            <input 
                                                type="file" 
                                                id="avatar_file" 
                                                accept="image/*" 
                                                onChange={handleFileChange} 
                                                style={{ display: 'none' }}
                                            />
                                            <span style={{ fontSize: '1.2rem', color: '#888' }}>
                                                {data.avatar_file ? data.avatar_file.name : ''}
                                            </span>
                                        </div>

                                        {/* URL Alternative */}
                                        <div style={{ marginTop: '10px' }}>
                                            <p style={{ fontSize: '1.2rem', color: '#666', marginBottom: '5px', fontWeight: 500 }}>Or use an Image URL:</p>
                                            <input 
                                                type="url" 
                                                value={data.avatar_url}
                                                onChange={handleUrlChange}
                                                placeholder="https://example.com/photo.png" 
                                                className="form-control" 
                                                style={{ width: '100%', maxWidth: '400px' }}
                                            />
                                        </div>
                                    </div>
                                    
                                    <p style={{ color: '#888', fontSize: '1.2rem', marginTop: '15px' }}>At least 800x800 px recommended. JPG or PNG is allowed.</p>
                                </div>
                            </div>

                            <div style={{ padding: '40px', display: 'flex', flexDirection: 'column', gap: '3rem' }}>

                                {/* PERSONAL INFO CARD */}
                                <div style={{ background: '#fafafa', border: '1px solid #eaeaea', borderRadius: '12px', padding: '1.5rem' }}>
                                     <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '1.5rem' }}>
                                        <h3 style={{ fontSize: '1.2rem', color: '#1c1c1c', fontWeight: 700 }}>Personal Info</h3>
                                        <span style={{ fontSize: '0.9rem', color: '#888' }}><ion-icon name="lock-closed-outline"></ion-icon> Private info</span>
                                    </div>
                                    
                                    <div className="grid-columns" style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1.5rem' }}>
                                        <div>
                                            <label style={{ display: 'block', fontWeight: 500, marginBottom: '0.5rem', color: '#666', fontSize: '1.2rem' }}>Full Name</label>
                                            <input 
                                                type="text" 
                                                value={data.name} 
                                                onChange={e => setData('name', e.target.value)} 
                                                required 
                                                className="form-control" 
                                            />
                                            {errors.name && <span style={{ color: 'var(--brand-primary)', fontSize: '1.1rem' }}>{errors.name}</span>}
                                        </div>

                                        <div>
                                            <label style={{ display: 'block', fontWeight: 500, marginBottom: '0.5rem', color: '#666', fontSize: '1.2rem' }}>Job Title</label>
                                            <input 
                                                type="text" 
                                                value={data.title} 
                                                onChange={e => setData('title', e.target.value)} 
                                                placeholder="e.g. UX Designer" 
                                                className="form-control" 
                                            />
                                            {errors.title && <span style={{ color: 'var(--brand-primary)', fontSize: '1.1rem' }}>{errors.title}</span>}
                                        </div>
                                    </div>
                                </div>

                                {/* LOCATION CARD */}
                                <div style={{ background: '#fafafa', border: '1px solid #eaeaea', borderRadius: '12px', padding: '1.5rem' }}>
                                    <h3 style={{ fontSize: '1.2rem', color: '#1c1c1c', marginBottom: '1.5rem', fontWeight: 700 }}>Location</h3>
                                    <div style={{ position: 'relative' }}>
                                        <ion-icon name="compass-outline" style={{ position: 'absolute', left: '1rem', top: '50%', transform: 'translateY(-50%)', fontSize: '1.6rem', color: '#888' }}></ion-icon>
                                        <input 
                                            type="text" 
                                            value={data.location} 
                                            onChange={e => setData('location', e.target.value)} 
                                            placeholder="e.g. California" 
                                            className="form-control" 
                                            style={{ paddingLeft: '2.8rem' }}
                                        />
                                        {errors.location && <span style={{ color: 'var(--brand-primary)', fontSize: '1.1rem' }}>{errors.location}</span>}
                                    </div>
                                </div>

                                {/* BIO CARD */}
                                <div style={{ background: '#fafafa', border: '1px solid #eaeaea', borderRadius: '12px', padding: '1.5rem' }}>
                                    <h3 style={{ fontSize: '1.2rem', color: '#1c1c1c', marginBottom: '1.5rem', fontWeight: 700 }}>Bio</h3>
                                    <textarea 
                                        value={data.bio} 
                                        onChange={e => setData('bio', e.target.value)} 
                                        rows={4} 
                                        className="form-control" 
                                        placeholder="Hi 👋, I'm..." 
                                        style={{ lineHeight: 1.6, resize: 'vertical' }}
                                    />
                                    {errors.bio && <span style={{ color: 'var(--brand-primary)', fontSize: '1.1rem' }}>{errors.bio}</span>}
                                </div>

                                {/* SOCIAL LINKS CARD */}
                                <div style={{ background: '#fafafa', border: '1px solid #eaeaea', borderRadius: '12px', padding: '1.5rem' }}>
                                    <h3 style={{ fontSize: '1.2rem', color: '#1c1c1c', marginBottom: '1.5rem', fontWeight: 700 }}>Social Links</h3>
                                    <div className="grid-columns" style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1.5rem' }}>
                                        <div style={{ position: 'relative' }}>
                                            <ion-icon name="logo-linkedin" style={{ position: 'absolute', left: '1rem', top: '50%', transform: 'translateY(-50%)', fontSize: '1.6rem', color: '#0077b5' }}></ion-icon>
                                            <input 
                                                type="url" 
                                                value={data.linkedin_url} 
                                                onChange={e => setData('linkedin_url', e.target.value)} 
                                                placeholder="LinkedIn URL" 
                                                className="form-control" 
                                                style={{ paddingLeft: '2.8rem' }}
                                            />
                                            {errors.linkedin_url && <span style={{ color: 'var(--brand-primary)', fontSize: '1.1rem' }}>{errors.linkedin_url}</span>}
                                        </div>
                                        <div style={{ position: 'relative' }}>
                                            <ion-icon name="logo-github" style={{ position: 'absolute', left: '1rem', top: '50%', transform: 'translateY(-50%)', fontSize: '1.6rem', color: '#333' }}></ion-icon>
                                            <input 
                                                type="url" 
                                                value={data.github_url} 
                                                onChange={e => setData('github_url', e.target.value)} 
                                                placeholder="GitHub URL" 
                                                className="form-control" 
                                                style={{ paddingLeft: '2.8rem' }}
                                            />
                                            {errors.github_url && <span style={{ color: 'var(--brand-primary)', fontSize: '1.1rem' }}>{errors.github_url}</span>}
                                        </div>
                                    </div>
                                </div>

                                {/* ACTION BUTTONS */}
                                <div className="action-buttons" style={{ display: 'flex', justifyContent: 'flex-end', gap: '1rem', marginTop: '1rem' }}>
                                    <Link href={`/profile/${user.route_key}`} style={{ background: '#fff', color: '#555', padding: '0.8rem 1.5rem', border: '1px solid #ddd', borderRadius: '8px', fontWeight: 600, textDecoration: 'none', cursor: 'pointer', fontSize: '1.4rem' }}>Cancel</Link>
                                    <button 
                                        type="submit" 
                                        disabled={processing}
                                        style={{ background: 'var(--brand-primary)', color: '#fff', padding: '0.8rem 2.5rem', border: 'none', borderRadius: '8px', fontWeight: 700, cursor: 'pointer', boxShadow: '0 4px 10px rgba(255, 69, 0, 0.2)', fontSize: '1.4rem' }}
                                    >
                                        {processing ? 'Saving...' : 'Save changes'}
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
