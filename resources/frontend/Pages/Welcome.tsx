import React from 'react';

interface WelcomeProps {
    message?: string;
}

export default function Welcome({ message }: WelcomeProps) {
    return (
        <div style={{ padding: '2rem', fontFamily: 'sans-serif', maxWidth: '600px', margin: '0 auto' }}>
            <h1 style={{ color: '#ff4500' }}>SkillUp React SPA</h1>
            <p style={{ fontSize: '1.2rem', color: '#666' }}>
                {message || 'React, TypeScript, and Inertia.js are successfully configured!'}
            </p>
        </div>
    );
}
