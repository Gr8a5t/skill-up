@extends('layouts.dashboard')

@section('title', 'User Dashboard')

@push('styles')
    <style>
        /* Content Area grid */
        .content-area { padding: 30px 40px; overflow-y: auto; flex-grow: 1; display: grid; grid-template-columns: 1fr 340px; gap: 30px; align-items: start; }
        
        /* Hero Banner */
        .hero-banner { background: linear-gradient(135deg, var(--brand-primary), #ff8058); border-radius: 16px; padding: 40px; color: #fff; position: relative; overflow: hidden; margin-bottom: 30px; box-shadow: 0 10px 25px rgba(255, 69, 0, 0.2); }
        .hero-label { font-size: 1.2rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 12px; opacity: 0.9; }
        .hero-title { font-size: 3rem; font-weight: 800; line-height: 1.2; margin-bottom: 24px; max-width: 80%; }
        .hero-btn { background: #1c1c1c; color: #fff; border: none; padding: 12px 24px; border-radius: 30px; font-size: 1.4rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; text-decoration: none; }
        .hero-btn:hover { background: #000; }
        .hero-stars { position: absolute; right: 0; top: 0; width: 40%; height: 100%; opacity: 0.2; pointer-events: none; background: radial-gradient(circle, #fff 2px, transparent 2px); background-size: 30px 30px; }
        
        /* Metric Row */
        .metric-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .metric-card { background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px 20px; display: flex; align-items: center; gap: 16px; }
        .m-icon { width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; flex-shrink: 0; }
        .m-icon-1 { background: #f0ebff; color: #8e54e9; }
        .m-icon-2 { background: #ffe4ef; color: #ff4aa0; }
        .m-icon-3 { background: #dff6ff; color: #3aa8f2; }
        .m-details h4 { font-size: 1.5rem; color: var(--text-main); font-weight: 700; }
        .m-details p { font-size: 1.2rem; color: var(--text-mut); }
        
        /* Section Title */
        .section-hdr { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .section-title { font-size: 1.8rem; font-weight: 800; color: var(--text-main); }
        .section-nav { display: flex; gap: 8px; }
        .s-nav-btn { width: 32px; height: 32px; border-radius: 50%; border: 1px solid var(--border-color); background: var(--bg-surface); display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-main); }
        .s-nav-btn.active { background: var(--brand-primary); color: #fff; border-color: var(--brand-primary); }
        
        /* Recommended Courses Carousel */
        .cw-grid { display: flex; gap: 20px; margin-bottom: 30px; overflow-x: auto; scroll-snap-type: x mandatory; padding-bottom: 15px; scrollbar-width: none; }
        .cw-grid::-webkit-scrollbar { display: none; }
        .cw-card { flex: 0 0 calc(33.333% - 14px); scroll-snap-align: start; background: var(--bg-surface); border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color); }
        .cw-img { width: 100%; height: 140px; background-color: #ddd; background-size: cover; background-position: center; position: relative; }
        .cw-like { position: absolute; top: 12px; right: 12px; width: 32px; height: 32px; border-radius: 50%; background: rgba(0,0,0,0.4); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.4rem; }
        .cw-body { padding: 16px; }
        .cw-badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 1rem; font-weight: 700; margin-bottom: 10px; }
        .cw-badge-fe { background: #dff6ff; color: #3aa8f2; }
        .cw-badge-ux { background: #f0ebff; color: #8e54e9; }
        .cw-badge-br { background: #ffe4ef; color: #ff4aa0; }
        .cw-title { font-size: 1.4rem; font-weight: 700; line-height: 1.4; margin-bottom: 14px; min-height: 40px; }
        .cw-mentor { display: flex; align-items: center; gap: 8px; border-top: 1px dashed var(--border-color); padding-top: 12px; }
        .cw-mentor-ava { width: 28px; height: 28px; border-radius: 50%; background: #ddd; display: flex; align-items: center; justify-content: center; font-size: 1rem; font-weight: 700; }
        .cw-mentor-name { font-size: 1.2rem; color: var(--text-mut); font-weight: 700; }
        
        /* Table */
        .lesson-table { width: 100%; border-collapse: collapse; background: var(--bg-surface); border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color); }
        .lesson-table th { background: #fdfdfd; padding: 14px 20px; text-align: left; font-size: 1.1rem; color: var(--text-mut); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); }
        .lesson-table td { padding: 16px 20px; font-size: 1.3rem; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
        .lesson-table tr:last-child td { border-bottom: none; }
        .tbl-mentor { display: flex; align-items: center; gap: 12px; }
        .tbl-m-info h5 { font-size: 1.3rem; font-weight: 700; color: var(--text-main); }
        .tbl-m-info p { font-size: 1.1rem; color: var(--text-mut); }
        .tbl-badge { background: #f6f7f8; color: var(--text-main); padding: 6px 12px; border-radius: 20px; font-size: 1.1rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; }
        .action-arrow { width: 32px; height: 32px; border-radius: 50%; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: var(--text-mut); text-decoration: none; transition: 0.2s; }
        .action-arrow:hover { border-color: var(--brand-primary); color: var(--brand-primary); }



        /* Right Panel */
        .right-panel { display: flex; flex-direction: column; gap: 30px; }
        
        /* Stats Widget */
        .stat-widget { background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 16px; padding: 24px; text-align: center; }
        .stat-radial { width: 140px; height: 140px; margin: 0 auto 20px; position: relative; border-radius: 50%; background: conic-gradient(var(--brand-primary) 32%, #f0f0f0 0); display: flex; align-items: center; justify-content: center; }
        .stat-inner { width: 120px; height: 120px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: inset 0 2px 10px rgba(0,0,0,0.05); overflow: hidden;}
        .stat-val { position: absolute; top: 0; right: 0; background: var(--brand-primary); color: #fff; padding: 4px 8px; border-radius: 20px; font-size: 1.1rem; font-weight: 700; border: 2px solid #fff; }
        .stat-greeting { font-size: 1.8rem; font-weight: 800; margin-bottom: 6px; }
        .stat-sub { font-size: 1.2rem; color: var(--text-mut); margin-bottom: 24px; }
        
        /* Activity Heatmap */
        .heatmap-wrap { margin-top: 20px; }
        .heatmap-title { font-size: 1.2rem; font-weight: 700; color: var(--text-mut); margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
        .heatmap-title span { color: var(--brand-primary); font-size: 1.1rem; font-weight: 700; }
        .heatmap-outer { display: flex; gap: 5px; overflow-x: auto; padding-bottom: 4px; scrollbar-width: thin; scrollbar-color: #ddd transparent; }
        .heatmap-day-labels { display: flex; flex-direction: column; gap: 3px; padding-top: 18px; flex-shrink: 0; }
        .heatmap-day-label { height: 11px; font-size: 0.95rem; color: var(--text-mut); line-height: 11px; white-space: nowrap; }
        .heatmap-right { display: flex; flex-direction: column; min-width: 0; }
        .heatmap-month-row { display: flex; gap: 3px; height: 16px; margin-bottom: 2px; }
        .heatmap-month-slot { font-size: 1rem; color: var(--text-mut); white-space: nowrap; flex-shrink: 0; overflow: visible; line-height: 1; }
        .heatmap-grid { display: flex; gap: 3px; }
        .heatmap-col { display: flex; flex-direction: column; gap: 3px; }
        .heatmap-cell { width: 11px; height: 11px; border-radius: 2px; background: #ede9f7; transition: transform 0.12s; cursor: pointer; flex-shrink: 0; }
        .heatmap-cell:hover { transform: scale(1.4); outline: 1px solid rgba(115,64,224,0.4); }
        .heatmap-cell[data-level="1"] { background: #c4b0f5; }
        .heatmap-cell[data-level="2"] { background: #9b77ee; }
        .heatmap-cell[data-level="3"] { background: #7340e0; }
        .heatmap-cell[data-level="4"] { background: #4a00c8; }
        .heatmap-legend { display: flex; align-items: center; gap: 5px; margin-top: 8px; font-size: 1.05rem; color: var(--text-mut); justify-content: flex-end; }
        .legend-cell { width: 11px; height: 11px; border-radius: 2px; display: inline-block; }

        /* Mentor Widget */
        .mentor-widget { background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 16px; padding: 24px; }
        .mentor-list { display: flex; flex-direction: column; gap: 16px; margin-top: 16px; }
        .mentor-item { display: flex; align-items: center; justify-content: space-between; border-bottom: 1px dashed var(--border-color); padding-bottom: 16px; }
        .mentor-item:last-child { border-bottom: none; padding-bottom: 0; }
        .m-follow-btn { display: inline-flex; align-items: center; gap: 4px; padding: 6px 14px; border: 1px solid var(--brand-primary); color: var(--brand-primary); border-radius: 20px; font-size: 1.2rem; font-weight: 700; background: none; cursor: pointer; transition: 0.2s; }
        .m-follow-btn:hover { background: var(--brand-primary); color: #fff; }
        .btn-full { width: 100%; display: block; padding: 12px; background: rgba(255, 69, 0, 0.08); color: var(--brand-primary); text-align: center; border-radius: 8px; font-weight: 700; font-size: 1.4rem; text-decoration: none; margin-top: 30px; transition: 0.2s;}
        .btn-full:hover { background: var(--brand-primary); color: #fff; }

        @media (max-width: 1200px) {
            .content-area { grid-template-columns: 1fr; }
            .right-panel { flex-direction: row; }
            .right-panel > div { flex: 1; }
        }
        @media (max-width: 992px) {
            .metric-row { grid-template-columns: repeat(2, 1fr); }
            .cw-card { flex: 0 0 calc(50% - 10px); }
            .hero-title { font-size: 2.2rem; max-width: 100%; }
            .content-area { padding: 20px; }
            .right-panel { flex-direction: column; width: 100%; }
        }
        @media (max-width: 768px) {
            .metric-row { grid-template-columns: 1fr; }
            .cw-card { flex: 0 0 calc(100% - 20px); }
            .hero-banner { padding: 30px 20px; margin-bottom: 20px; width: 100%; box-sizing: border-box; }
            .hero-title { font-size: 1.8rem; max-width: 100%; margin-bottom: 15px; }
            .hero-btn { font-size: 1.2rem; padding: 10px 20px; }
            
            .lesson-table thead { display: none; }
            .lesson-table, .lesson-table tbody, .lesson-table tr, .lesson-table td { display: block; width: 100%; }
            .lesson-table tr { margin-bottom: 16px; border-bottom: 1px solid var(--border-color); background: var(--bg-surface); border-radius: 12px; }
            .lesson-table td { border-bottom: none; display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; text-align: right; }
            .lesson-table td::before { content: attr(data-label); font-weight: 700; color: var(--text-mut); text-transform: uppercase; font-size: 0.9rem; float: left; text-align: left; margin-right: 10px; }
            .tbl-mentor { justify-content: flex-end; width: 100%; }
            
            .content-area { padding: 15px; gap: 20px; }
            .metric-card { padding: 12px 16px; gap: 12px; }
            .m-icon { width: 40px; height: 40px; font-size: 1.8rem; }
            .m-details h4 { font-size: 1.3rem; }
            
            .stat-widget, .mentor-widget { padding: 20px 15px; width: 100%; box-sizing: border-box; overflow: hidden; }
            .heatmap-outer { width: 100%; max-width: 100%; overflow-x: auto; }
            .hero-title { white-space: normal !important; word-break: break-word !important; }
            
            .cw-card { width: 100%; }
            
            /* Fix mentor widget spacing */
            .mentor-list { gap: 15px; }
            .mentor-item { flex-wrap: nowrap; gap: 15px; justify-content: space-between; align-items: center; }
            .tbl-mentor { flex-grow: 0; min-width: 0; gap: 12px; }
            .tbl-m-info { flex-grow: 1; min-width: 0; }
            .tbl-m-info h5 { font-size: 1.3rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px; }
            .tbl-m-info p { font-size: 1.1rem; }
            .m-follow-btn { padding: 5px 10px; font-size: 1.2rem; flex-shrink: 0; margin-left: auto; }
        }
        @media (max-width: 480px) {
            .hero-title { font-size: 1.5rem !important; }
            .section-title { font-size: 1.5rem; }
            .cw-body { padding: 12px; }
            .cw-title { font-size: 1.2rem; }
            .topbar { height: 60px; padding: 0 12px; }
            .user-name { display: none; }
            .content-area { padding: 10px; }
            .m-details h4 { font-size: 1.2rem; }
            .m-follow-btn span { display: none; }
            .m-follow-btn { padding: 8px; border-radius: 50%; }
        }
    </style>
@endpush

@section('content')
    @livewire('dashboard-progress')
@endsection

@push('scripts')
<script>
(function() {
    const MONTHS = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    // Inject month labels above each column where the month changes
    const grid     = document.getElementById('heatmap-grid');
    const monthRow = document.getElementById('heatmap-months');
    if (grid && monthRow) {
        const cols = [...grid.querySelectorAll('.heatmap-col')];
        let lastMonth = null;
        cols.forEach(col => {
            const firstCell = col.querySelector('.heatmap-cell');
            const d = firstCell ? new Date(firstCell.dataset.date + 'T00:00:00') : null;
            const m = d ? d.getMonth() : null;

            const slot = document.createElement('span');
            slot.className = 'heatmap-month-slot';
            slot.style.width = '11px';
            slot.style.display = 'inline-block';
            slot.style.fontSize = '10px';

            if (d && m !== lastMonth) {
                slot.textContent = MONTHS[m];
                lastMonth = m;
            }
            monthRow.appendChild(slot);
        });
    }

    // Streak counter — scan cells newest→oldest
    const allCells = [...document.querySelectorAll('.heatmap-cell')].reverse();
    let streak = 0;
    for (const cell of allCells) {
        if (parseInt(cell.dataset.count) > 0) streak++;
        else break;
    }
    const streakEl = document.getElementById('heatmap-streak');
    if (streakEl && streak > 0) {
        streakEl.textContent = '🔥 ' + streak + ' day streak';
    }

    // Carousel buttons responsiveness
    const carousel = document.getElementById('crs-carousel');
    const btnPrev = document.getElementById('crs-btn-prev');
    const btnNext = document.getElementById('crs-btn-next');

    if (carousel && btnPrev && btnNext) {
        const updateButtons = () => {
            const minScrollLeft = 0;
            const maxScrollLeft = carousel.scrollWidth - carousel.clientWidth;

            if (carousel.scrollLeft <= minScrollLeft) {
                btnPrev.classList.remove('active');
            } else {
                btnPrev.classList.add('active');
            }

            if (Math.ceil(carousel.scrollLeft) >= maxScrollLeft - 2) {
                btnNext.classList.remove('active');
            } else {
                btnNext.classList.add('active');
            }
        };

        // Initial check and event listeners
        updateButtons();
        carousel.addEventListener('scroll', updateButtons);
        window.addEventListener('resize', updateButtons);
    }
})();
</script>
@endpush

