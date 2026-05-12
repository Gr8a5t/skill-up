@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@push('styles')
    <style>
        .welcome-row { margin-bottom: 25px; }
        .welcome-title { font-size: 2.2rem; font-weight: 800; color: #fff; margin-bottom: 5px; }
        .welcome-sub { color: var(--text-mut); font-size: 1.3rem; font-weight: 500; }

        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            padding: 20px;
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: 0.3s;
            position: relative;
            overflow: hidden;
        }
        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, rgba(255, 69, 0, 0.05), transparent);
            border-radius: 0 0 0 100%;
        }
        .stat-info p { color: var(--text-mut); font-size: 1.2rem; font-weight: 600; margin-bottom: 4px; }
        .stat-info h2 { font-size: 2.1rem; font-weight: 800; color: #fff; }
        .stat-badge { font-size: 1rem; font-weight: 700; padding: 4px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; width: max-content; }
        .badge-up { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .badge-down { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

        /* Chart & Sales Section */
        .dashboard-row { display: grid; grid-template-columns: 1.8fr 1fr; gap: 24px; margin-bottom: 40px; }
        .panel { background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 20px; padding: 24px; }
        .panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .panel-title { font-size: 1.6rem; font-weight: 700; color: #fff; }
        
        /* User Growth Mock SVG Chart */
        .chart-container { height: 200px; position: relative; margin-top: 20px; width: 100%; }
        .chart-svg { width: 100%; height: 100%; overflow: visible; }
        .chart-path { fill: none; stroke: var(--brand-primary); stroke-width: 3; stroke-linecap: round; stroke-linejoin: round; filter: drop-shadow(0 4px 4px rgba(255, 69, 0, 0.2)); }
        .chart-fill { fill: url(#chartGradient); opacity: 0.1; }

        /* Top Performing Courses */
        .list-group { display: flex; flex-direction: column; gap: 15px; }
        .list-item { display: flex; align-items: center; gap: 12px; }
        .item-icon { width: 40px; height: 40px; border-radius: 10px; background: var(--bg-panel); display: flex; align-items: center; justify-content: center; font-size: 2rem; color: var(--brand-primary); }
        .item-info { flex-grow: 1; }
        .item-info h4 { font-size: 1.3rem; font-weight: 700; color: #fff; margin-bottom: 2px; }
        .item-info p { font-size: 1.1rem; color: var(--text-mut); }

        /* Tables */
        .table-panel { width: 100%; overflow-x: auto; }
        .admin-table { width: 100%; border-collapse: collapse; min-width: 600px; }
        .admin-table th { text-align: left; padding: 15px; color: var(--text-mut); font-size: 1.2rem; font-weight: 700; border-bottom: 1px solid var(--border-color); text-transform: uppercase; letter-spacing: 1px; }
        .admin-table td { padding: 15px; border-bottom: 1px solid var(--border-color); font-size: 1.3rem; vertical-align: middle; }
        .student-cell { display: flex; align-items: center; gap: 12px; }
        .student-ava { width: 32px; height: 32px; border-radius: 50%; border: 1px solid var(--border-color); }
        .status-badge { padding: 4px 10px; border-radius: 8px; font-size: 1.1rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; }
        .status-paid { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

        @media (max-width: 1200px) {
            .dashboard-row { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('content')
    <div class="welcome-row">
        <h1 class="welcome-title">Welcome back, Sarah! 👋</h1>
        <p class="welcome-sub">Here's what's happening with your platform today.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <p>Active Courses</p>
                <h2>1,340</h2>
            </div>
            <div class="stat-badge badge-up">
                <ion-icon name="trending-up-outline"></ion-icon> 3.1%
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <p>Total Students</p>
                <h2>45,821</h2>
            </div>
            <div class="stat-badge badge-up">
                <ion-icon name="trending-up-outline"></ion-icon> 8.4%
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <p>Avg. Session Time</p>
                <h2>19m 45s</h2>
            </div>
            <div class="stat-badge badge-up">
                <ion-icon name="trending-up-outline"></ion-icon> 1.2%
            </div>
        </div>
    </div>

    <div class="dashboard-row">
        <!-- Growth Chart -->
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">User Growth</h3>
                <div class="stat-badge badge-up" style="background: var(--bg-panel); color: var(--text-mut);">Oct 1 - Oct 31</div>
            </div>
            <p style="color: var(--text-mut); font-size: 1.2rem; margin-top: -15px;">New Signups & Active Users</p>
            
            <div class="chart-container">
                <svg class="chart-svg" viewBox="0 0 1000 200">
                    <defs>
                        <linearGradient id="chartGradient" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="var(--brand-primary)" />
                            <stop offset="100%" stop-color="transparent" />
                        </linearGradient>
                    </defs>
                    <path class="chart-path" d="M0,180 Q100,160 200,170 Q300,180 400,140 Q500,100 600,150 Q700,170 800,130 Q900,90 1000,50" />
                    <path class="chart-fill" d="M0,180 Q100,160 200,170 Q300,180 400,140 Q500,100 600,150 Q700,170 800,130 Q900,90 1000,50 L1000,200 L0,200 Z" />
                </svg>
            </div>
            <div style="display:flex; justify-content:space-between; margin-top:15px; color:var(--text-mut); font-size:1.1rem; font-weight:700;">
                <span>Oct 1</span>
                <span>Oct 10</span>
                <span>Oct 20</span>
                <span>Oct 31</span>
            </div>
        </div>

        <!-- Top Courses -->
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Top Performing Courses</h3>
            </div>
            <div class="list-group">
                <div class="list-item">
                    <div class="item-icon"><ion-icon name="logo-react"></ion-icon></div>
                    <div class="item-info">
                        <h4>React Masters</h4>
                        <p>23 minutes ago sales</p>
                    </div>
                </div>
                <div class="list-item">
                    <div class="item-icon"><ion-icon name="bulb-outline"></ion-icon></div>
                    <div class="item-info">
                        <h4>AI Basics</h4>
                        <p>45 minutes ago sales</p>
                    </div>
                </div>
                <div class="list-item">
                    <div class="item-icon"><ion-icon name="server-outline"></ion-icon></div>
                    <div class="item-info">
                        <h4>Data Science</h4>
                        <p>1 hour ago sales</p>
                    </div>
                </div>
                <div class="list-item">
                    <div class="item-icon"><ion-icon name="logo-python"></ion-icon></div>
                    <div class="item-info">
                        <h4>Python Bootc.</h4>
                        <p>2 hours ago sales</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Sales -->
    <div class="panel" style="margin-bottom: 20px;">
        <div class="panel-header">
            <h3 class="panel-title">Recent Course Sales</h3>
            <button class="status-badge" style="background:var(--bg-panel); color:var(--text-mut); border:none; cursor:pointer;">View All</button>
        </div>
        <div class="table-panel">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="student-cell">
                                <img src="https://ui-avatars.com/api/?name=Liam+S&background=random" class="student-ava">
                                <span>Liam S.</span>
                            </div>
                        </td>
                        <td>React Masters</td>
                        <td>Oct 29</td>
                        <td>$249</td>
                        <td><span class="status-badge status-paid"><div class="dot"></div> Paid</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="student-cell">
                                <img src="https://ui-avatars.com/api/?name=Anya+K&background=random" class="student-ava">
                                <span>Anya K.</span>
                            </div>
                        </td>
                        <td>AI Basics</td>
                        <td>Oct 29</td>
                        <td>$199</td>
                        <td><span class="status-badge status-paid" style="color:#8e54e9; background:rgba(142, 84, 233, 0.1);"><div class="dot"></div> Completed</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="student-cell">
                                <img src="https://ui-avatars.com/api/?name=Marcus+V&background=random" class="student-ava">
                                <span>Marcus V.</span>
                            </div>
                        </td>
                        <td>Data Science</td>
                        <td>Oct 28</td>
                        <td>$329</td>
                        <td><span class="status-badge status-paid"><div class="dot"></div> Paid</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
