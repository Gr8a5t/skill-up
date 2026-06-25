import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import FitLifeLayout from '../../Layouts/FitLifeLayout';

export default function Register() {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/register');
    };

    return (
        <FitLifeLayout>
            <Head title="Sign Up | SkillUp" />
            <style dangerouslySetInnerHTML={{__html: `
                .auth-wrapper {
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: #f6f7f8;
                    padding: 100px 20px 40px;
                    font-family: var(--ff-rubik);
                }
                .auth-card {
                    background: #fff;
                    width: 100%;
                    max-width: 440px;
                    border-radius: 8px;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
                    padding: 40px;
                    border: 1px solid #edeff1;
                }
                .auth-header {
                    text-align: center;
                    margin-bottom: 30px;
                }
                .auth-header h1 {
                    font-size: 2.8rem;
                    font-weight: 700;
                    color: #1c1c1c;
                    margin-bottom: 8px;
                }
                .auth-header p {
                    font-size: 1.5rem;
                    color: #878a8c;
                }
                .auth-form-group {
                    margin-bottom: 20px;
                }
                .auth-form-group label {
                    display: block;
                    font-size: 1.3rem;
                    font-weight: 700;
                    color: #3c3c3c;
                    margin-bottom: 8px;
                }
                .auth-input {
                    width: 100%;
                    padding: 12px 16px;
                    font-size: 1.5rem;
                    color: #1c1c1c;
                    background: #f6f7f8;
                    border: 1px solid #edeff1;
                    border-radius: 4px;
                    transition: 0.2s;
                }
                .auth-input:focus {
                    outline: none;
                    background: #fff;
                    border-color: #ff4500;
                    box-shadow: 0 0 0 3px rgba(255, 69, 0, 0.1);
                }
                .auth-btn {
                    width: 100%;
                    padding: 14px;
                    background: #ff4500;
                    color: #fff;
                    font-size: 1.5rem;
                    font-weight: 700;
                    border: none;
                    border-radius: 30px;
                    cursor: pointer;
                    transition: 0.2s;
                    margin-top: 10px;
                }
                .auth-btn:hover {
                    background: #e03d00;
                }
                .auth-links {
                    margin-top: 24px;
                    text-align: center;
                    font-size: 1.4rem;
                    color: #878a8c;
                }
                .auth-links a {
                    color: #ff4500;
                    font-weight: 700;
                    text-decoration: none;
                }
                .auth-links a:hover {
                    text-decoration: underline;
                }
                .auth-error {
                    background: #fdf5f5;
                    color: #d93d3d;
                    padding: 12px;
                    border-radius: 4px;
                    font-size: 1.3rem;
                    margin-bottom: 20px;
                    border-left: 4px solid #d93d3d;
                }
            `}} />
            <div className="auth-wrapper">
                <div className="auth-card">
                    <div className="auth-header">
                        <h1>Join SkillUp</h1>
                        <p>Start your learning journey today.</p>
                    </div>

                    {Object.keys(errors).length > 0 && (
                        <div className="auth-error">
                            <ul style={{ margin: 0, paddingLeft: '20px' }}>
                                {Object.values(errors).map((error, idx) => (
                                    <li key={idx}>{error}</li>
                                ))}
                            </ul>
                        </div>
                    )}

                    <form onSubmit={submit}>
                        <div className="auth-form-group">
                            <label htmlFor="name">Full Name</label>
                            <input 
                                type="text" 
                                id="name" 
                                className="auth-input" 
                                value={data.name}
                                onChange={e => setData('name', e.target.value)}
                                required 
                                autoFocus 
                            />
                        </div>

                        <div className="auth-form-group">
                            <label htmlFor="email">Email Address</label>
                            <input 
                                type="email" 
                                id="email" 
                                className="auth-input" 
                                value={data.email}
                                onChange={e => setData('email', e.target.value)}
                                required 
                            />
                        </div>
                        
                        <div className="auth-form-group">
                            <label htmlFor="password">Password</label>
                            <input 
                                type="password" 
                                id="password" 
                                className="auth-input" 
                                value={data.password}
                                onChange={e => setData('password', e.target.value)}
                                required 
                            />
                        </div>

                        <div className="auth-form-group">
                            <label htmlFor="password_confirmation">Confirm Password</label>
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                className="auth-input" 
                                value={data.password_confirmation}
                                onChange={e => setData('password_confirmation', e.target.value)}
                                required 
                            />
                        </div>

                        <button type="submit" className="auth-btn" disabled={processing}>
                            {processing ? 'Creating Account...' : 'Create Account'}
                        </button>
                    </form>

                    <div className="auth-links">
                        Already have an account? <Link href="/login">Sign In</Link>
                    </div>
                </div>
            </div>
        </FitLifeLayout>
    );
}
