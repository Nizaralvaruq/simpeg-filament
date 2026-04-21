<style>
    /* ===== LAYOUT ===== */
    .qr-container { max-width: 1400px; margin: 0 auto; }
    .qr-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; }
    @media (min-width: 1024px) { .qr-grid { grid-template-columns: 2fr 1fr; } }

    /* ===== CARDS ===== */
    .qr-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 1px 4px rgba(0,0,0,.06), 0 4px 12px rgba(0,0,0,.04);
        overflow: hidden;
        border: 1px solid #f1f5f9;
    }
    .dark .qr-card { background: #1e293b; border-color: #334155; }

    /* ===== CAMERA ===== */
    .qr-camera { position: relative; background: #000; aspect-ratio: 16/9; overflow: hidden; }
    @media (max-width: 640px) { .qr-camera { aspect-ratio: 4/3; } }
    #reader { width: 100%; height: 100%; }
    #reader video { object-fit: cover !important; width: 100% !important; height: 100% !important; }
    #reader__scan_region,
    #reader__dashboard_section,
    #reader__header_message,
    #reader__dashboard { display: none !important; }

    /* ===== CAMERA BADGE ===== */
    .qr-badge {
        position: absolute; top: 1rem; left: 1rem; z-index: 10;
        display: inline-flex; align-items: center; gap: .5rem;
        padding: .4rem .9rem;
        background: rgba(15,23,42,.75); backdrop-filter: blur(8px);
        border-radius: 9999px;
        font-size: .7rem; font-weight: 700; color: white; letter-spacing: .05em;
        border: 1px solid rgba(255,255,255,.15);
    }
    .qr-pulse { position: relative; display: flex; height: .6rem; width: .6rem; }
    .qr-pulse-ring { position: absolute; height: 100%; width: 100%; border-radius: 9999px; background: #22d3ee; opacity: .75; animation: qr-ping 1.5s cubic-bezier(0,0,.2,1) infinite; }
    .qr-pulse-dot  { position: relative; border-radius: 9999px; height: .6rem; width: .6rem; background: #06b6d4; }
    @keyframes qr-ping { 75%, 100% { transform: scale(2.4); opacity: 0; } }

    /* ===== SCAN LINE ===== */
    .qr-scan-line {
        position: absolute; top: 5%; left: 5%; width: 90%; height: 2px;
        background: linear-gradient(to right, transparent, #22d3ee 50%, transparent);
        box-shadow: 0 0 8px #22d3ee;
        animation: qr-scan 2.5s ease-in-out infinite;
        pointer-events: none; z-index: 5;
    }
    @keyframes qr-scan {
        0%   { top: 5%;  opacity:0; }
        8%   { opacity:1; }
        92%  { opacity:1; }
        100% { top: 92%; opacity:0; }
    }

    /* ===== ACTIONS BAR ===== */
    .qr-actions {
        padding: 1rem 1.25rem; display: flex; flex-wrap: wrap; gap: .75rem;
        background: #f8fafc; border-top: 1px solid #e2e8f0;
    }
    .dark .qr-actions { background: #0f172a; border-top-color: #1e293b; }

    /* ===== BUTTONS ===== */
    .qr-btn {
        display: inline-flex; align-items: center; gap: .5rem;
        padding: .625rem 1.25rem; border-radius: .75rem;
        font-size: .875rem; font-weight: 700;
        cursor: pointer; border: 1.5px solid transparent;
        transition: all .15s ease;
    }
    .qr-btn-primary  { background: linear-gradient(135deg,#3b82f6,#2563eb); color:white; }
    .qr-btn-primary:hover { opacity: .9; transform: translateY(-1px); }
    .qr-btn-outline  { background: white; border-color: #e2e8f0; color: #475569; }
    .qr-btn-outline:hover { background: #f1f5f9; }
    .dark .qr-btn-outline { background:#1e293b; border-color:#334155; color:#94a3b8; }

    /* ===== STAT CARDS ===== */
    .qr-stat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem; }
    .qr-stat-card {
        position: relative;          /* <-- FIX: required for absolute SVG watermark */
        overflow: hidden;
        padding: 1.25rem; border-radius: 1rem; color: white;
        box-shadow: 0 4px 14px rgba(0,0,0,.15);
    }
    .qr-stat-total { background: linear-gradient(145deg,#0ea5e9,#0284c7); }
    .qr-stat-avg   { background: linear-gradient(145deg,#8b5cf6,#7c3aed); }

    /* ===== HISTORY LIST ===== */
    .qr-history-item {
        display: flex; align-items: center; gap: .75rem;
        padding: .875rem; background: #f8fafc;
        border-radius: .875rem; border: 1.5px solid #f1f5f9;
        animation: qr-slide 0.3s ease-out;
        margin-bottom: .5rem; transition: border-color .15s;
    }
    .qr-history-item:hover { border-color: #3b82f6; }
    .dark .qr-history-item { background: #0f172a; border-color: #1e293b; }
    @keyframes qr-slide { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }

    /* ===== MODAL ===== */
    .qr-modal-overlay {
        position: fixed; inset: 0;
        background: rgba(15,23,42,.8); backdrop-filter: blur(10px);
        display: flex; align-items: center; justify-content: center;
        z-index: 2000; padding: 1rem;
        animation: qr-fade-in .2s ease;
    }
    @keyframes qr-fade-in { from { opacity:0; } to { opacity:1; } }
    .qr-modal-card {
        background: white; border-radius: 1.5rem;
        width: 100%; max-width: 540px; overflow: hidden;
        box-shadow: 0 25px 60px rgba(0,0,0,.35);
        animation: qr-pop-in .25s cubic-bezier(.34,1.56,.64,1);
    }
    @keyframes qr-pop-in { from { transform:scale(.9); opacity:0; } to { transform:scale(1); opacity:1; } }
    .dark .qr-modal-card { background: #1e293b; }

    /* ===== FULLSCREEN ===== */
    .qr-fullscreen { position:fixed !important; inset:0; z-index:9999; background:#000; }

    [x-cloak] { display:none !important; }
</style>
