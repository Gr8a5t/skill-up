import React, { useState } from 'react';
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

interface ProfileShowProps {
    user: User;
    auth: {
        user: {
            id: number;
            route_key: string;
        };
    };
}

export default function Show({ user, auth }: ProfileShowProps) {
    const [activeTab, setActiveTab] = useState<'work' | 'about'>('work');
    const [isBioEditing, setIsBioEditing] = useState(false);
    const [isLocationEditing, setIsLocationEditing] = useState(false);
    const [socialModalOpen, setSocialModalOpen] = useState(false);
    const [workModalOpen, setWorkModalOpen] = useState(false);

    const isOwnProfile = auth.user && (auth.user.route_key === user.route_key || auth.user.id === user.id);

    // Bio Form
    const bioForm = useForm({
        name: user.name,
        title: user.title || '',
        location: user.location || '',
        bio: user.bio || '',
    });

    // Location Form
    const locationForm = useForm({
        name: user.name,
        title: user.title || '',
        bio: user.bio || '',
        location: user.location || '',
    });

    const submitBio = (e: React.FormEvent) => {
        e.preventDefault();
        bioForm.put('/profile/edit', {
            preserveScroll: true,
            onSuccess: () => setIsBioEditing(false),
        });
    };

    const submitLocation = (e: React.FormEvent) => {
        e.preventDefault();
        locationForm.put('/profile/edit', {
            preserveScroll: true,
            onSuccess: () => setIsLocationEditing(false),
        });
    };

    return (
        <DashboardLayout title={`${user.name} | SkillUp Profile`}>
            <style dangerouslySetInnerHTML={{__html: `
                .profile-top-section { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: start; }
                .profile-tab { font-size: 1.5rem; font-weight: 700; text-decoration: none; padding-bottom: 15px; margin-bottom: -16px; border-bottom: 2px solid transparent; transition: 0.2s; cursor: pointer; }
                .profile-tab.active-tab { color: #1c1c1c !important; border-bottom-color: #1c1c1c !important; }
                .social-modal-btn { width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; background: #fff; border: 1px solid #eaeaea; border-radius: 12px; font-size: 1.5rem; font-weight: 500; color: #1c1c1c; cursor: pointer; transition: 0.2s; }
                .social-modal-btn:hover { border-color: #ccc; background: #fafafa; }
                
                @media (max-width: 992px) {
                    .profile-top-section { grid-template-columns: 1fr !important; }
                }
            `}} />

            <div className="content-area" style={{ display: 'block', padding: '4rem', backgroundColor: '#ffffff', minHeight: 'calc(100vh - 80px)' }}>
                <div style={{ maxWidth: '1100px', margin: '0 auto', display: 'flex', flexDirection: 'column', gap: '40px' }}>
                    
                    {/* Top Section */}
                    <div className="profile-top-section">
                        {/* Left Info */}
                        <div style={{ textAlign: 'left' }}>
                            <div style={{ display: 'flex', alignItems: 'center', gap: '15px', margin: '20px 0' }}>
                                <div style={{ position: 'relative' }}>
                                    <img 
                                        src={user.avatar ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&size=150&background=ff4500&color=fff`} 
                                        alt={user.name} 
                                        style={{ width: '100px', height: '100px', borderRadius: '50%', objectFit: 'cover' }}
                                    />
                                    <div style={{ position: 'absolute', bottom: '5px', right: '5px', width: '14px', height: '14px', background: '#22c55e', border: '2px solid #fff', borderRadius: '50%' }}></div>
                                </div>
                            </div>

                            <h1 style={{ fontSize: '3.5rem', fontWeight: 700, color: '#1c1c1c', marginBottom: '8px', letterSpacing: '-1px' }}>{user.name}</h1>
                            <p style={{ fontSize: '1.8rem', color: '#666', fontWeight: 500, marginBottom: '30px', lineHeight: 1.5, whiteSpace: 'pre-line' }}>
                                {user.bio ?? 'SkillUp User'}
                            </p>

                            {/* Actions */}
                            <div style={{ display: 'flex', gap: '15px', alignItems: 'center' }}>
                                {isOwnProfile ? (
                                    <>
                                        <div style={{ fontWeight: 600, fontSize: '1.4rem', padding: '12px 24px', border: '1px solid #eaeaea', borderRadius: '30px', color: '#1c1c1c', background: '#fff' }}>
                                            0 Followers
                                        </div>
                                        <button style={{ width: '48px', height: '48px', borderRadius: '50%', border: '1px solid #eaeaea', background: 'transparent', display: 'flex', alignItems: 'center', justifyContent: 'center', cursor: 'pointer', color: '#444', fontSize: '1.8rem' }}>
                                            <ion-icon name="stats-chart-outline"></ion-icon>
                                        </button>
                                        <button style={{ width: '48px', height: '48px', borderRadius: '50%', border: '1px solid #eaeaea', background: 'transparent', display: 'flex', alignItems: 'center', justifyContent: 'center', cursor: 'pointer', color: '#444', fontSize: '1.8rem' }}>
                                            <ion-icon name="share-outline"></ion-icon>
                                        </button>
                                    </>
                                ) : (
                                    <>
                                        <Link href={`/chats?user_id=${user.id}`} style={{ background: '#1c1c28', color: '#fff', padding: '12px 24px', borderRadius: '30px', fontWeight: 600, fontSize: '1.4rem', textDecoration: 'none', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                                            Message
                                        </Link>
                                        <button style={{ background: '#fff', color: '#1c1c28', border: '1px solid #eaeaea', padding: '12px 24px', borderRadius: '30px', fontWeight: 600, fontSize: '1.4rem', cursor: 'pointer' }}>
                                            Follow
                                        </button>
                                    </>
                                )}
                            </div>
                        </div>

                        {/* Right Media Box */}
                        <div 
                            onClick={() => setWorkModalOpen(true)}
                            style={{ border: '1px solid #eaeaea', borderRadius: '16px', padding: '40px', display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', textAlign: 'center', height: '100%', minHeight: '250px', background: '#fdfdfd', cursor: 'pointer', transition: '0.2s' }}
                        >
                            <ion-icon name="image-outline" style={{ fontSize: '3rem', color: '#888', marginBottom: '15px' }}></ion-icon>
                            <h3 style={{ fontSize: '1.6rem', color: '#333', fontWeight: 600, marginBottom: '8px' }}>Add featured media</h3>
                            <p style={{ fontSize: '1.4rem', color: '#666', marginBottom: '15px' }}>Drag and drop or <span style={{ textDecoration: 'underline' }}>browse</span></p>
                            <p style={{ fontSize: '1.1rem', color: '#aaa', maxWidth: '300px', lineHeight: 1.5 }}>We recommend a video (mp4) or image (png, jpg, gif) in a 4:3, 5:4, 9:16, or 16:9 aspect ratio. Max 200MB.</p>
                        </div>
                    </div>

                    {/* Navigation Bar */}
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderBottom: '1px solid #eaeaea', paddingBottom: '15px', flexWrap: 'wrap', gap: '20px' }}>
                        <div style={{ display: 'flex', gap: '30px' }}>
                            <span 
                                onClick={() => setActiveTab('work')} 
                                className={`profile-tab ${activeTab === 'work' ? 'active-tab' : ''}`}
                                style={{ color: '#888' }}
                            >
                                Work
                            </span>
                            <span 
                                onClick={() => setActiveTab('about')} 
                                className={`profile-tab ${activeTab === 'about' ? 'active-tab' : ''}`}
                                style={{ color: '#888' }}
                            >
                                About
                            </span>
                        </div>
                        
                        <div style={{ display: 'flex', alignItems: 'center', gap: '20px' }}>
                            {user.location && (
                                <div style={{ display: 'flex', alignItems: 'center', gap: '5px', fontSize: '1.3rem', color: '#444', paddingRight: '20px', borderRight: '1px solid #eaeaea' }}>
                                    <ion-icon name="location-outline"></ion-icon> {user.location}
                                </div>
                            )}
                            {isOwnProfile && (
                                <button onClick={() => setSocialModalOpen(true)} style={{ background: '#f4f4f5', color: '#333', border: 'none', padding: '8px 16px', borderRadius: '20px', fontWeight: 600, fontSize: '1.3rem', cursor: 'pointer', display: 'flex', alignItems: 'center', gap: '5px' }}>
                                    <ion-icon name="add"></ion-icon> Add social links
                                </button>
                            )}
                        </div>
                    </div>

                    {/* Content Area - Work Tab */}
                    {activeTab === 'work' && (
                        <div style={{ display: 'block', marginTop: '30px' }}>
                            <div style={{ background: '#fdfdfd', borderRadius: '16px', padding: '80px 40px', textAlign: 'center', position: 'relative', display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', border: '1px solid #f0f0f0' }}>
                                <h2 style={{ fontSize: '2.2rem', fontWeight: 700, color: '#1c1c1c', marginBottom: '12px' }}>Feature your work</h2>
                                <p style={{ fontSize: '1.5rem', color: '#666', marginBottom: '30px' }}>Share quick snapshots of what you've been working on.</p>
                                <div style={{ display: 'flex', justifyContent: 'center' }}>
                                    <button onClick={() => setWorkModalOpen(true)} style={{ background: '#1c1c28', color: '#fff', border: 'none', padding: '12px 30px', borderRadius: '30px', fontWeight: 600, fontSize: '1.4rem', cursor: 'pointer' }}>Add work</button>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Content Area - About Tab */}
                    {activeTab === 'about' && (
                        <div style={{ display: 'block', marginTop: '30px', textAlign: 'left' }}>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '40px' }}>
                                {/* Left Side */}
                                <div>
                                    <div style={{ display: 'flex', alignItems: 'center', gap: '15px', marginBottom: '30px' }}>
                                        <img 
                                            src={user.avatar ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&size=150&background=ff4500&color=fff`} 
                                            alt={user.name} 
                                            style={{ width: '64px', height: '64px', borderRadius: '50%', objectFit: 'cover' }}
                                        />
                                        <h2 style={{ fontSize: '2.2rem', fontWeight: 700, color: '#1c1c1c' }}>Meet {user.name.split(' ')[0]}</h2>
                                    </div>
                                    
                                    <p style={{ fontSize: '1.5rem', color: '#666', fontWeight: 600, marginBottom: '40px' }}>1 following</p>
                                    
                                    {!isBioEditing ? (
                                        user.bio ? (
                                            <div style={{ fontSize: '1.6rem', lineHeight: '1.8', color: '#1c1c1c', whiteSpace: 'pre-line', marginBottom: '40px', position: 'relative' }}>
                                                {user.bio}
                                                {isOwnProfile && (
                                                    <span onClick={() => setIsBioEditing(true)} style={{ color: '#888', marginLeft: '10px', cursor: 'pointer' }}>
                                                        <ion-icon name="create-outline"></ion-icon>
                                                    </span>
                                                )}
                                            </div>
                                        ) : (
                                            isOwnProfile && (
                                                <div onClick={() => setIsBioEditing(true)} style={{ display: 'flex', alignItems: 'center', gap: '10px', color: '#1c1c1c', fontSize: '1.5rem', cursor: 'pointer', marginBottom: '40px' }}>
                                                    <ion-icon name="add" style={{ fontSize: '2rem', color: '#666' }}></ion-icon> Add a descriptive bio
                                                </div>
                                            )
                                        )
                                    ) : (
                                        /* Bio Edit Form */
                                        <div style={{ marginBottom: '40px' }}>
                                            <form onSubmit={submitBio}>
                                                <div style={{ position: 'relative' }}>
                                                    <textarea 
                                                        name="bio" 
                                                        value={bioForm.data.bio}
                                                        onChange={e => bioForm.setData('bio', e.target.value)}
                                                        placeholder="Add your bio" 
                                                        maxLength={400}
                                                        style={{ width: '100%', border: 'none', borderBottom: '1px solid #eaeaea', outline: 'none', fontSize: '1.6rem', color: '#1c1c1c', fontFamily: 'inherit', resize: 'none', minHeight: '60px', paddingBottom: '25px', background: 'transparent' }}
                                                    />
                                                    <div style={{ position: 'absolute', bottom: '5px', right: 0, fontSize: '1.3rem', color: bioForm.data.bio.length > 400 ? 'red' : '#888' }}>
                                                        {bioForm.data.bio.length}/400
                                                    </div>
                                                </div>
                                                <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '10px', marginTop: '15px' }}>
                                                    <button type="button" onClick={() => setIsBioEditing(false)} style={{ background: 'transparent', color: '#666', border: 'none', fontWeight: 600, fontSize: '1.3rem', cursor: 'pointer', padding: '8px 15px' }}>Cancel</button>
                                                    <button type="submit" disabled={bioForm.processing} style={{ background: '#1c1c28', color: '#fff', border: 'none', padding: '8px 20px', borderRadius: '20px', fontWeight: 600, fontSize: '1.3rem', cursor: 'pointer' }}>Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    )}

                                    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: '15px' }}>
                                        <h3 style={{ fontSize: '1.2rem', fontWeight: 700, color: '#888', textTransform: 'uppercase', letterSpacing: '1px' }}>Social Links</h3>
                                        {isOwnProfile && (user.github_url || user.linkedin_url) && (
                                            <Link href="/profile/edit" style={{ color: '#888', fontSize: '1.5rem' }}><ion-icon name="create-outline"></ion-icon></Link>
                                        )}
                                    </div>
                                    <div style={{ display: 'flex', flexDirection: 'column', gap: '15px' }}>
                                        {user.github_url && (
                                            <a href={user.github_url} target="_blank" rel="noreferrer" style={{ display: 'flex', alignItems: 'center', gap: '10px', color: '#1c1c1c', fontSize: '1.5rem', textDecoration: 'none', fontWeight: 600 }}>
                                                <ion-icon name="logo-github" style={{ fontSize: '2rem' }}></ion-icon> GitHub
                                            </a>
                                        )}
                                        {user.linkedin_url && (
                                            <a href={user.linkedin_url} target="_blank" rel="noreferrer" style={{ display: 'flex', alignItems: 'center', gap: '10px', color: '#0077b5', fontSize: '1.5rem', textDecoration: 'none', fontWeight: 600 }}>
                                                <ion-icon name="logo-linkedin" style={{ fontSize: '2rem' }}></ion-icon> LinkedIn
                                            </a>
                                        )}
                                        {!user.github_url && !user.linkedin_url && (
                                            isOwnProfile ? (
                                                <div onClick={() => setSocialModalOpen(true)} style={{ display: 'flex', alignItems: 'center', gap: '10px', color: '#1c1c1c', fontSize: '1.5rem', cursor: 'pointer' }}>
                                                    <ion-icon name="add" style={{ fontSize: '2rem', color: '#666' }}></ion-icon> Add social links
                                                </div>
                                            ) : (
                                                <p style={{ fontSize: '1.4rem', color: '#888' }}>No social links added.</p>
                                            )
                                        )}
                                    </div>
                                </div>

                                {/* Right Side Card */}
                                <div>
                                    <div style={{ border: '1px solid #eaeaea', borderRadius: '16px', padding: '30px', background: '#fff' }}>
                                        <div style={{ marginBottom: '30px' }}>
                                            <h4 style={{ fontSize: '1.1rem', fontWeight: 700, color: '#888', textTransform: 'uppercase', letterSpacing: '1px', marginBottom: '15px' }}>Rate</h4>
                                            {isOwnProfile ? (
                                                <Link href="/profile/edit" style={{ textDecoration: 'none' }}>
                                                    <div style={{ display: 'flex', alignItems: 'center', gap: '10px', color: '#1c1c1c', fontSize: '1.5rem', fontWeight: 500, cursor: 'pointer' }}>
                                                        <ion-icon name="add" style={{ fontSize: '2rem', color: '#666' }}></ion-icon> Add hourly rate
                                                    </div>
                                                </Link>
                                            ) : (
                                                <p style={{ fontSize: '1.4rem', color: '#888' }}>Not specified</p>
                                            )}
                                        </div>

                                        {!isLocationEditing ? (
                                            <div id="details-display-wrapper">
                                                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: '15px' }}>
                                                    <h4 style={{ fontSize: '1.1rem', fontWeight: 700, color: '#888', textTransform: 'uppercase', letterSpacing: '1px' }}>Details</h4>
                                                    {isOwnProfile && (
                                                        <span onClick={() => setIsLocationEditing(true)} style={{ color: '#888', fontSize: '1.5rem', cursor: 'pointer' }}><ion-icon name="create-outline"></ion-icon></span>
                                                    )}
                                                </div>
                                                <div style={{ display: 'flex', flexDirection: 'column', gap: '15px' }}>
                                                    {user.location ? (
                                                        <div style={{ display: 'flex', alignItems: 'center', gap: '10px', color: '#1c1c1c', fontSize: '1.5rem', fontWeight: 500 }}>
                                                            <ion-icon name="location-outline" style={{ fontSize: '2rem', color: '#666' }}></ion-icon> {user.location}
                                                        </div>
                                                    ) : (
                                                        isOwnProfile && (
                                                            <div onClick={() => setIsLocationEditing(true)} style={{ display: 'flex', alignItems: 'center', gap: '10px', color: '#1c1c1c', fontSize: '1.5rem', fontWeight: 500, cursor: 'pointer' }}>
                                                                <ion-icon name="add" style={{ fontSize: '2rem', color: '#666' }}></ion-icon> Add location
                                                            </div>
                                                        )
                                                    )}
                                                    
                                                    {isOwnProfile && (
                                                        <Link href="/profile/edit" style={{ textDecoration: 'none' }}>
                                                            <div style={{ display: 'flex', alignItems: 'center', gap: '10px', color: '#1c1c1c', fontSize: '1.5rem', fontWeight: 500, cursor: 'pointer' }}>
                                                                <ion-icon name="add" style={{ fontSize: '2rem', color: '#666' }}></ion-icon> Add timezone
                                                            </div>
                                                        </Link>
                                                    )}
                                                </div>
                                            </div>
                                        ) : (
                                            /* Inline Location Edit Form */
                                            <div>
                                                <form onSubmit={submitLocation}>
                                                    <h4 style={{ fontSize: '1.4rem', fontWeight: 600, color: '#1c1c1c', marginBottom: '10px' }}>Location</h4>
                                                    <input 
                                                        type="text" 
                                                        name="location" 
                                                        value={locationForm.data.location} 
                                                        onChange={e => locationForm.setData('location', e.target.value)}
                                                        style={{ width: '100%', border: '1px solid #eaeaea', borderRadius: '8px', padding: '12px 15px', outline: 'none', fontSize: '1.5rem', color: '#1c1c1c', fontFamily: 'inherit', marginBottom: '15px' }} 
                                                        placeholder="City, Country"
                                                    />
                                                    
                                                    <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '10px' }}>
                                                        <button type="button" onClick={() => setIsLocationEditing(false)} style={{ background: 'transparent', color: '#666', border: 'none', fontWeight: 600, fontSize: '1.3rem', cursor: 'pointer', padding: '8px 15px' }}>Cancel</button>
                                                        <button type="submit" disabled={locationForm.processing} style={{ background: '#1c1c28', color: '#fff', border: 'none', padding: '8px 20px', borderRadius: '20px', fontWeight: 600, fontSize: '1.3rem', cursor: 'pointer' }}>Save</button>
                                                    </div>
                                                </form>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>

            {/* Social Links Modal */}
            {socialModalOpen && (
                <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.4)', zIndex: 1050, display: 'flex', alignItems: 'center', justifyContent: 'center', backdropFilter: 'blur(2px)' }}>
                    <div style={{ background: '#fff', width: '90%', maxWidth: '500px', borderRadius: '20px', padding: '30px', position: 'relative', boxShadow: '0 10px 40px rgba(0,0,0,0.1)' }}>
                        <button onClick={() => setSocialModalOpen(false)} style={{ position: 'absolute', right: '20px', top: '20px', background: 'transparent', border: 'none', fontSize: '2.4rem', color: '#444', cursor: 'pointer' }}>
                            <ion-icon name="close-outline"></ion-icon>
                        </button>
                        
                        <h2 style={{ fontSize: '2.2rem', fontWeight: 700, color: '#1c1c1c', marginBottom: '10px' }}>Social links</h2>
                        <p style={{ fontSize: '1.4rem', color: '#666', marginBottom: '25px' }}>Add links that showcase your work, recognition, personality and more!</p>
                        
                        <div style={{ display: 'flex', flexDirection: 'column', gap: '12px' }}>
                            <Link href="/profile/edit" onClick={() => setSocialModalOpen(false)} className="social-modal-btn" style={{ textDecoration: 'none' }}>
                                <div style={{ display: 'flex', alignItems: 'center', gap: '15px' }}>
                                    <ion-icon name="logo-linkedin"></ion-icon> LinkedIn
                                </div>
                                <ion-icon name="add" style={{ color: '#666', fontSize: '2rem' }}></ion-icon>
                            </Link>
                            <Link href="/profile/edit" onClick={() => setSocialModalOpen(false)} className="social-modal-btn" style={{ textDecoration: 'none' }}>
                                <div style={{ display: 'flex', alignItems: 'center', gap: '15px' }}>
                                    <ion-icon name="logo-twitter"></ion-icon> X / Twitter
                                </div>
                                <ion-icon name="add" style={{ color: '#666', fontSize: '2rem' }}></ion-icon>
                            </Link>
                            <Link href="/profile/edit" onClick={() => setSocialModalOpen(false)} className="social-modal-btn" style={{ textDecoration: 'none' }}>
                                <div style={{ display: 'flex', alignItems: 'center', gap: '15px' }}>
                                    <ion-icon name="logo-instagram"></ion-icon> Instagram
                                </div>
                                <ion-icon name="add" style={{ color: '#666', fontSize: '2rem' }}></ion-icon>
                            </Link>
                        </div>
                    </div>
                </div>
            )}

            {/* Add Work Modal */}
            {workModalOpen && (
                <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.4)', zIndex: 1050, display: 'flex', alignItems: 'center', justifyContent: 'center', backdropFilter: 'blur(2px)' }}>
                    <div style={{ background: '#fff', width: '90%', maxWidth: '650px', borderRadius: '20px', padding: '25px 30px', position: 'relative', boxShadow: '0 10px 40px rgba(0,0,0,0.1)' }}>
                        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: '30px' }}>
                            <div style={{ display: 'flex', alignItems: 'center', gap: '15px', flexGrow: 1 }}>
                                <img src={user.avatar ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&size=150&background=ff4500&color=fff`} alt={user.name} style={{ width: '48px', height: '48px', borderRadius: '50%', objectFit: 'cover' }} />
                                <input type="text" placeholder="Tell us a bit about this work" style={{ border: 'none', outline: 'none', fontSize: '1.8rem', color: '#1c1c1c', fontWeight: 500, width: '100%', background: 'transparent' }} />
                            </div>
                            <button onClick={() => setWorkModalOpen(false)} style={{ background: 'transparent', border: 'none', fontSize: '2.8rem', color: '#444', cursor: 'pointer', display: 'flex', alignItems: 'center', justifyContent: 'center', marginLeft: '15px' }}>
                                <ion-icon name="close-outline"></ion-icon>
                            </button>
                        </div>

                        <div style={{ border: '1px solid #eaeaea', borderRadius: '12px', padding: '60px 20px', textAlign: 'center', background: '#fafafa', marginBottom: '30px', cursor: 'pointer' }}>
                            <ion-icon name="image-outline" style={{ fontSize: '3.5rem', color: '#bbb', marginBottom: '15px' }}></ion-icon>
                            <h3 style={{ fontSize: '1.6rem', fontWeight: 600, color: '#1c1c1c', marginBottom: '10px' }}>Share visuals from a recent project</h3>
                            <p style={{ fontSize: '1.4rem', color: '#888' }}>Drop a file or <span style={{ textDecoration: 'underline', color: '#444', fontWeight: 600 }}>Browse</span></p>
                        </div>

                        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                            <button style={{ background: 'transparent', border: 'none', fontSize: '2.4rem', color: '#666', cursor: 'pointer', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                                <ion-icon name="happy-outline"></ion-icon>
                            </button>
                            <button onClick={() => setWorkModalOpen(false)} style={{ background: '#1c1c28', color: '#fff', border: 'none', padding: '12px 35px', borderRadius: '30px', fontWeight: 600, fontSize: '1.5rem', cursor: 'pointer' }}>Next</button>
                        </div>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
}
