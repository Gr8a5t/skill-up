import React, { useEffect, useState } from 'react';
import Header from '../Components/Header';
import Footer from '../Components/Footer';

interface FitLifeLayoutProps {
    children: React.ReactNode;
}

export default function FitLifeLayout({ children }: FitLifeLayoutProps) {
    const [isScrolled, setIsScrolled] = useState(false);

    useEffect(() => {
        const handleScroll = () => {
            if (window.scrollY >= 100) {
                setIsScrolled(true);
            } else {
                setIsScrolled(false);
            }
        };

        window.addEventListener('scroll', handleScroll);
        // Run initial check
        handleScroll();

        return () => {
            window.removeEventListener('scroll', handleScroll);
        };
    }, []);

    const scrollToTop = (e: React.MouseEvent<HTMLAnchorElement>) => {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    };

    return (
        <>
            <Header />
            
            {children}
            
            <Footer />

            {/* Back to top button */}
            <a 
                href="#top" 
                className={`back-top-btn ${isScrolled ? 'active' : ''}`} 
                aria-label="back to top" 
                onClick={scrollToTop}
            >
                <ion-icon name="caret-up-sharp" aria-hidden="true"></ion-icon>
            </a>
        </>
    );
}
