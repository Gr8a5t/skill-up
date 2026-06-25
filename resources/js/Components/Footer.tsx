import React from 'react';
import { Link } from '@inertiajs/react';

export default function Footer() {
    return (
        <footer className="footer">
            <div className="section footer-top bg-dark has-bg-image" style={{ backgroundImage: "url('/fitlife-assets/images/footer-bg.png')" }}>
                <div className="container">
                    <div className="footer-brand">
                        <Link href="/" className="logo">
                            <img src="/fitlife-assets/images/footer-logo.png" alt="SkillUp Footer Logo" loading="lazy" decoding="async" />
                        </Link>
                        <p className="footer-brand-text">
                            SkillUp curates free skill-building resources, curated roadmaps, and small accountability nudges for every young learner.
                        </p>
                        <div className="wrapper">
                            <img src="/fitlife-assets/images/footer-clock.png" width="34" height="34" loading="lazy" alt="Clock" />
                            <ul className="footer-brand-list">
                                <li>
                                    <p className="footer-brand-title">Monday - Friday</p>
                                    <p>7:00Am - 10:00Pm</p>
                                </li>
                                <li>
                                    <p className="footer-brand-title">Saturday - Sunday</p>
                                    <p>7:00Am - 2:00Pm</p>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <ul className="footer-list">
                        <li>
                            <p className="footer-list-title has-before">Our Links</p>
                        </li>
                        <li>
                            <Link href="/" className="footer-link">Home</Link>
                        </li>
                        <li>
                            <Link href="/about" className="footer-link">About Us</Link>
                        </li>
                        <li>
                            <Link href="/paths" className="footer-link">Classes</Link>
                        </li>
                        <li>
                            <Link href="/courses" className="footer-link">Blog</Link>
                        </li>
                        <li>
                            <a href="#" className="footer-link">Contact Us</a>
                        </li>
                    </ul>

                    <ul className="footer-list">
                        <li>
                            <p className="footer-list-title has-before">Contact Us</p>
                        </li>
                        <li className="footer-list-item">
                            <div className="icon">
                                <ion-icon name="location" aria-hidden="true"></ion-icon>
                            </div>
                            <address className="address footer-link">
                                Chicago, IL · Remote-first team
                            </address>
                        </li>
                        <li className="footer-list-item">
                            <div className="icon">
                                <ion-icon name="call" aria-hidden="true"></ion-icon>
                            </div>
                            <div>
                                <a href="tel:18001213637" className="footer-link">1800-SKILL-UP</a>
                                <a href="tel:+13125551234" className="footer-link">+1 312 555-1234</a>
                            </div>
                        </li>
                        <li className="footer-list-item">
                            <div className="icon">
                                <ion-icon name="mail" aria-hidden="true"></ion-icon>
                            </div>
                            <div>
                                <a href="mailto:hello@skillup.com" className="footer-link">hello@skillup.com</a>
                                <a href="mailto:team@skillup.com" className="footer-link">team@skillup.com</a>
                            </div>
                        </li>
                    </ul>

                    <ul className="footer-list">
                        <li>
                            <p className="footer-list-title has-before">Our Newsletter</p>
                        </li>
                        <li>
                            <form action="#" className="footer-form" onSubmit={(e) => e.preventDefault()}>
                                <input type="email" name="email_address" aria-label="email" placeholder="Email Address" required className="input-field" />
                                <button type="submit" className="btn btn-primary" aria-label="Submit">
                                    <ion-icon name="chevron-forward-sharp" aria-hidden="true"></ion-icon>
                                </button>
                            </form>
                        </li>
                        <li>
                            <ul className="social-list">
                                <li>
                                    <a href="#" className="social-link">
                                        <ion-icon name="logo-facebook"></ion-icon>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" className="social-link">
                                        <ion-icon name="logo-instagram"></ion-icon>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" className="social-link">
                                        <ion-icon name="logo-twitter"></ion-icon>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>

            <div className="footer-bottom">
                <div className="container">
                    <p className="copyright">
                        &copy; 2026 SkillUp. All Rights Reserved by <a href="#" className="copyright-link">SkillUp Collective</a>.
                    </p>
                    <ul className="footer-bottom-list">
                        <li>
                            <a href="#" className="footer-bottom-link has-before">Privacy Policy</a>
                        </li>
                        <li>
                            <a href="#" className="footer-bottom-link has-before">Terms & Condition</a>
                        </li>
                    </ul>
                </div>
            </div>
        </footer>
    );
}
