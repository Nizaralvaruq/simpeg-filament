<style>
    /* ===== GOOGLE FONT ===== */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

    /* ===== RESET & BASE ===== */
    .qr-wrap * { font-family: 'Inter', system-ui, sans-serif; box-sizing: border-box; }

    /* ===== LAYOUT ===== */
    .qr-container { max-width: 1400px; margin: 0 auto; }
    .qr-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    @media (min-width: 1024px) {
        .qr-grid { grid-template-columns: 3fr 2fr; }
    }

    /* ===== HEADER HERO ===== */
    .qr-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 45%, #2563eb 100%);
        border-radius: 1.25rem;
        padding: 1.5rem 1.75rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(37,99,235,.35);
    }
    .qr-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .qr-hero-left { position: relative; z-index: 1; }
    .qr-hero-title {
        font-size: 1.5rem;
        font-weight: 900;
        color: white;
        letter-spacing: -.02em;
        display: flex;
        align-items: center;
        gap: .6rem;
        margin-bottom: .35rem;
    }
    .qr-hero-sub { font-size: .8rem; color: rgba(255,255,255,.65); font-weight: 500; }
    .qr-hero-right {
        position: relative; z-index: 1;
        display: flex; align-items: center; gap: 1rem;
    }
    .qr-clock-box { text-align: right; }
    .qr-clock-time {
        font-size: 1.5rem; font-weight: 900; color: white;
        font-variant-numeric: tabular-nums; letter-spacing: -.03em;
    }
    .qr-clock-date { font-size: .65rem; color: rgba(255,255,255,.6); font-weight: 600; text-transform: uppercase; letter-spacing: .1em; }
    .qr-online-badge {
        display: flex; align-items: center; gap: .4rem;
        padding: .4rem .8rem; border-radius: 9999px;
        font-size: .65rem; font-weight: 800; text-transform: uppercase; letter-spacing: .08em;
        background: rgba(34,197,94,.2); color: #86efac; border: 1px solid rgba(34,197,94,.35);
    }
    .qr-online-dot { width: .45rem; height: .45rem; border-radius: 50%; background: #4ade80; animation: pulse-dot 2s infinite; }
    @keyframes pulse-dot { 0%,100% { opacity: 1; } 50% { opacity: .4; } }
    .qr-fullscreen-btn {
        display: flex; align-items: center; justify-content: center;
        width: 2.25rem; height: 2.25rem; border-radius: .75rem;
        background: rgba(255,255,255,.15); color: white;
        border: 1px solid rgba(255,255,255,.2); cursor: pointer;
        transition: background .15s;
    }
    .qr-fullscreen-btn:hover { background: rgba(255,255,255,.25); }

    /* ===== CARDS ===== */
    .qr-card {
        background: white;
        border-radius: 1.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,.05), 0 4px 16px rgba(0,0,0,.05);
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }
    .dark .qr-card { background: #1e293b; border-color: #334155; }

    /* ===== CAMERA ===== */
    .qr-camera {
        position: relative;
        background: #020617;
        aspect-ratio: 4/3;
        overflow: hidden;
    }
    @media (min-width: 768px) { .qr-camera { aspect-ratio: 16/9; } }
    #reader { width: 100%; height: 100%; }
    #reader video { object-fit: cover !important; width: 100% !important; height: 100% !important; }
    #reader__scan_region,
    #reader__dashboard_section,
    #reader__header_message,
    #reader__dashboard { display: none !important; }

    /* ===== SCAN FRAME OVERLAY ===== */
    .qr-frame {
        position: absolute; top: 50%; left: 50%;
        transform: translate(-50%,-50%);
        width: 46%; aspect-ratio: 1;
        z-index: 5; pointer-events: none;
    }
    .qr-frame-corner {
        position: absolute; width: 1.5rem; height: 1.5rem;
        border-color: #22d3ee; border-style: solid; border-width: 0;
    }
    .qr-frame-corner.tl { top:0; left:0;  border-top-width:3px; border-left-width:3px;  border-top-left-radius:6px; }
    .qr-frame-corner.tr { top:0; right:0; border-top-width:3px; border-right-width:3px; border-top-right-radius:6px; }
    .qr-frame-corner.bl { bottom:0; left:0;  border-bottom-width:3px; border-left-width:3px;  border-bottom-left-radius:6px; }
    .qr-frame-corner.br { bottom:0; right:0; border-bottom-width:3px; border-right-width:3px; border-bottom-right-radius:6px; }

    /* ===== SCAN LINE ===== */
    .qr-scan-line {
        position: absolute; top: 27%; left: 27%; width: 46%; height: 2px;
        background: linear-gradient(to right, transparent, #22d3ee 50%, transparent);
        box-shadow: 0 0 10px #22d3ee, 0 0 20px rgba(34,211,238,.4);
        animation: qr-scan 2.5s ease-in-out infinite;
        pointer-events: none; z-index: 6;
    }
    @keyframes qr-scan {
        0%   { top: 27%; opacity:0; }
        8%   { opacity:1; }
        92%  { opacity:1; }
        100% { top: 73%; opacity:0; }
    }

    /* ===== LIVE BADGE ===== */
    .qr-live-badge {
        position: absolute; top: 1rem; left: 1rem; z-index: 10;
        display: inline-flex; align-items: center; gap: .45rem;
        padding: .35rem .8rem;
        background: rgba(2,6,23,.75); backdrop-filter: blur(12px);
        border-radius: 9999px;
        font-size: .65rem; font-weight: 800; color: white; letter-spacing: .08em; text-transform: uppercase;
        border: 1px solid rgba(255,255,255,.12);
    }
    .qr-live-dot { width: .5rem; height: .5rem; border-radius: 50%; background: #22d3ee; animation: pulse-dot 1.5s infinite; }

    /* ===== ACTION BAR ===== */
    .qr-action-bar {
        display: flex; gap: .75rem; padding: 1rem 1.25rem;
        background: #f8fafc; border-top: 1px solid #e2e8f0;
    }
    .dark .qr-action-bar { background: #0f172a; border-top-color: #1e293b; }

    /* ===== BUTTONS ===== */
    .qr-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
        padding: .65rem 1.25rem; border-radius: .875rem;
        font-size: .8rem; font-weight: 700; letter-spacing: .01em;
        cursor: pointer; border: 1.5px solid transparent;
        transition: all .15s ease;
    }
    .qr-btn-primary {
        background: linear-gradient(135deg,#3b82f6,#2563eb);
        color: white;
        box-shadow: 0 4px 12px rgba(37,99,235,.3);
    }
    .qr-btn-primary:hover { filter: brightness(1.1); transform: translateY(-1px); box-shadow: 0 6px 16px rgba(37,99,235,.4); }
    .qr-btn-outline {
        background: white; border-color: #e2e8f0; color: #475569;
    }
    .qr-btn-outline:hover { background: #f1f5f9; border-color: #cbd5e1; }
    .dark .qr-btn-outline { background:#1e293b; border-color:#334155; color:#94a3b8; }

    /* ===== TIPS BOX ===== */
    .qr-tips {
        margin-top: 1rem; padding: 1rem 1.25rem;
        border-radius: 1rem;
        background: linear-gradient(135deg, #eff6ff, #f0f9ff);
        border: 1px solid #bfdbfe;
    }
    .dark .qr-tips { background: rgba(37,99,235,.1); border-color: rgba(37,99,235,.2); }

    /* ===== STAT CARDS ===== */
    .qr-stat-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
    .qr-stat-card {
        position: relative; overflow: hidden;
        padding: 1.1rem 1.25rem; border-radius: 1rem; color: white;
        box-shadow: 0 4px 16px rgba(0,0,0,.12);
    }
    .qr-stat-total { background: linear-gradient(145deg,#0ea5e9,#0369a1); }
    .qr-stat-a     { background: linear-gradient(145deg,#8b5cf6,#6d28d9); }
    .qr-stat-num { font-size: 2.25rem; font-weight: 900; line-height: 1; letter-spacing: -.04em; }
    .qr-stat-lbl { font-size: .6rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; opacity: .75; margin-top: .3rem; }
    .qr-stat-icon {
        position: absolute; bottom: -.5rem; right: -.5rem;
        width: 4.5rem; height: 4.5rem; opacity: .12;
    }

    /* ===== HISTORY ===== */
    .qr-history-wrap { padding: .875rem; max-height: 280px; overflow-y: auto; }
    .qr-history-wrap::-webkit-scrollbar { width: 3px; }
    .qr-history-wrap::-webkit-scrollbar-track { background: transparent; }
    .qr-history-wrap::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 9999px; }

    .qr-history-item {
        display: flex; align-items: center; gap: .75rem;
        padding: .75rem .875rem;
        background: #f8fafc;
        border-radius: .875rem;
        border: 1.5px solid #f1f5f9;
        animation: qr-slide .3s ease-out;
        margin-bottom: .5rem;
        transition: border-color .15s, background .15s;
        cursor: default;
    }
    .qr-history-item:last-child { margin-bottom: 0; }
    .qr-history-item:hover { border-color: #93c5fd; background: #eff6ff; }
    .dark .qr-history-item { background: #0f172a; border-color: #1e293b; }
    .dark .qr-history-item:hover { border-color: #3b82f6; background: rgba(59,130,246,.1); }
    @keyframes qr-slide { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }

    .qr-grade-badge {
        display: inline-flex; align-items: center; justify-content: center;
        width: 1.5rem; height: 1.5rem; border-radius: .4rem;
        font-size: .7rem; font-weight: 900;
    }
    .qr-grade-A { background: #dcfce7; color: #15803d; }
    .qr-grade-B { background: #dbeafe; color: #1d4ed8; }
    .qr-grade-C { background: #fee2e2; color: #b91c1c; }

    /* ===== MODAL ===== */
    .qr-modal-overlay {
        position: fixed; inset: 0;
        background: rgba(2,6,23,.8); backdrop-filter: blur(12px);
        display: flex; align-items: center; justify-content: center;
        z-index: 2000; padding: 1rem;
        animation: qr-fade .2s ease;
    }
    @keyframes qr-fade { from { opacity:0; } to { opacity:1; } }
    .qr-modal-card {
        background: white; border-radius: 1.5rem;
        width: 100%; max-width: 520px; overflow: hidden;
        box-shadow: 0 30px 80px rgba(0,0,0,.4), 0 0 0 1px rgba(255,255,255,.05);
        animation: qr-pop .25s cubic-bezier(.34,1.56,.64,1);
    }
    @keyframes qr-pop { from { transform:scale(.92); opacity:0; } to { transform:scale(1); opacity:1; } }
    .dark .qr-modal-card { background: #1e293b; border: 1px solid #334155; }

    .qr-modal-header {
        padding: 1.25rem 1.5rem;
        background: linear-gradient(135deg, #1e3a8a, #2563eb);
        display: flex; align-items: center; gap: 1rem;
    }
    .qr-modal-avatar {
        width: 3.5rem; height: 3.5rem; border-radius: 1rem;
        background: rgba(255,255,255,.2);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; font-weight: 900; color: white; flex-shrink: 0;
        border: 2px solid rgba(255,255,255,.3);
        overflow: hidden;
    }
    .qr-modal-name { font-size: 1.1rem; font-weight: 900; color: white; letter-spacing: -.01em; }
    .qr-modal-meta { font-size: .75rem; color: rgba(255,255,255,.65); margin-top: .2rem; }
    .qr-modal-close {
        margin-left: auto; padding: .45rem; border-radius: .6rem;
        background: rgba(255,255,255,.15); color: white; cursor: pointer;
        border: none; transition: background .15s;
    }
    .qr-modal-close:hover { background: rgba(255,59,48,.7); }

    .qr-modal-last {
        padding: .6rem 1.5rem; font-size: .75rem;
        background: #fffbeb; border-bottom: 1px solid #fde68a;
        display: flex; align-items: center; gap: .75rem;
    }
    .qr-badge-last {
        padding: .2rem .5rem; border-radius: .35rem;
        background: #f59e0b; color: white; font-size: .6rem; font-weight: 800;
        text-transform: uppercase; letter-spacing: .08em; flex-shrink: 0;
    }
    .qr-modal-form { padding: 1.25rem 1.5rem; }
    .qr-form-label {
        display: block; font-size: .65rem; font-weight: 800;
        text-transform: uppercase; letter-spacing: .1em;
        color: #94a3b8; margin-bottom: .5rem;
    }
    .qr-form-label span { color: #f43f5e; }
    .qr-form-input {
        width: 100%; padding: .625rem .875rem;
        border: 1.5px solid #e2e8f0; border-radius: .75rem;
        font-size: .875rem; font-weight: 600; color: #1e293b;
        background: #f8fafc;
        outline: none; transition: border-color .15s, box-shadow .15s;
    }
    .qr-form-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.15); background: white; }
    .dark .qr-form-input { background: #0f172a; border-color: #334155; color: white; }
    .dark .qr-form-input:focus { background: #1e293b; }

    /* Grade buttons */
    .qr-grade-btn {
        flex: 1; padding: .625rem .5rem; border-radius: .75rem;
        font-size: .875rem; font-weight: 900;
        border: 2px solid #e2e8f0; background: #f9fafb; color: #9ca3af;
        cursor: pointer; transition: all .15s; display: flex; flex-direction: column; align-items: center; gap: .15rem;
    }
    .qr-grade-btn:hover { border-color: #93c5fd; }
    .qr-grade-btn.active-A { background: #dcfce7; color: #15803d; border-color: #22c55e; transform: scale(1.05); }
    .qr-grade-btn.active-B { background: #dbeafe; color: #1d4ed8; border-color: #3b82f6; transform: scale(1.05); }
    .qr-grade-btn.active-C { background: #fee2e2; color: #b91c1c; border-color: #ef4444; transform: scale(1.05); }
    .qr-grade-sub { font-size: .6rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; opacity: .7; }

    /* Submit button */
    .qr-submit-btn {
        width: 100%; padding: .875rem;
        background: linear-gradient(135deg,#2563eb,#1d4ed8);
        color: white; font-weight: 900; font-size: .9rem;
        border-radius: .875rem; border: none; cursor: pointer;
        box-shadow: 0 6px 20px rgba(37,99,235,.4);
        transition: all .15s; letter-spacing: .01em;
    }
    .qr-submit-btn:hover { filter: brightness(1.08); transform: translateY(-1px); box-shadow: 0 8px 24px rgba(37,99,235,.5); }
    .qr-submit-btn:active { transform: scale(.98); }

    /* ===== CARD SECTION HEADER ===== */
    .qr-section-head {
        padding: .875rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: space-between;
    }
    .dark .qr-section-head { border-color: #1e293b; }

    /* ===== VOLUME ===== */
    .qr-volume-box { padding: 1rem 1.25rem; }

    /* ===== LOADING OVERLAY ===== */
    .qr-loading-overlay {
        position: absolute; inset: 0;
        display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1rem;
        background: rgba(2,6,23,.8);
        z-index: 20; opacity: 0; pointer-events: none;
        transition: opacity .3s;
    }
    .qr-spinner {
        width: 3.5rem; height: 3.5rem;
        border: 3px solid rgba(34,211,238,.3);
        border-top-color: #22d3ee;
        border-radius: 50%;
        animation: spin .8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ===== FULLSCREEN ===== */
    .qr-fullscreen { position:fixed !important; inset:0; z-index:9999; background:#000; overflow-y:auto; }

    [x-cloak] { display:none !important; }
</style>
