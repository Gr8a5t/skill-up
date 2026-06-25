import React from 'react';
import { Head, Link } from '@inertiajs/react';
import DashboardLayout from '../Layouts/DashboardLayout';

export default function Paths() {
    return (
        <DashboardLayout title="Learning Paths — Coming Soon">
            <div style={{ width: '100%', height: 'calc(100vh - 80px)', overflow: 'hidden', backgroundColor: '#ffffff' }}>
                <Link href="/user-dashboard">
                    <img 
                        src="/fitlife-assets/images/comingSoon.png" 
                        alt="Coming Soon" 
                        style={{ width: '100%', height: '100%', objectFit: 'cover', display: 'block', cursor: 'pointer' }} 
                    />
                </Link>
            </div>
        </DashboardLayout>
    );
}
