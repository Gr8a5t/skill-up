import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import FitLifeLayout from '../../Layouts/FitLifeLayout';

export default function Login() {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/login');
    };

    return (
        <FitLifeLayout>
            <Head title="Login | SkillUp" />
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
                .auth-remember {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    margin-bottom: 20px;
                    cursor: pointer;
                    user-select: none;
                }
                .auth-remember input {
                    width: 18px;
                    height: 18px;
                    cursor: pointer;
                    accent-color: #ff4500;
                }
                .auth-remember span {
                    font-size: 1.4rem;
                    color: #3c3c3c;
                }
            `}} />
            <div className="auth-wrapper">
                <div className="auth-card">
                    <div className="auth-header">
                        <h1>Welcome Back</h1>
                        <p>Ready to continue learning?</p>
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
                            <label htmlFor="email">Email Address</label>
                            <input 
                                type="email" 
                                id="email" 
                                className="auth-input" 
                                value={data.email}
                                onChange={e => setData('email', e.target.value)}
                                required 
                                autoFocus 
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

                        <label className="auth-remember" htmlFor="remember">
                            <input 
                                type="checkbox" 
                                id="remember" 
                                checked={data.remember}
                                onChange={e => setData('remember', e.target.checked)}
                            />
                            <span>Remember me</span>
                        </label>

                        <button type="submit" className="auth-btn" disabled={processing}>
                            {processing ? 'Signing In...' : 'Sign In'}
                        </button>
                    </form>

                    <div className="auth-links">
                        Don't have an account? <Link href="/register">Sign Up</Link>
                    </div>
                </div>
            </div>
        </FitLifeLayout>
    );
}
