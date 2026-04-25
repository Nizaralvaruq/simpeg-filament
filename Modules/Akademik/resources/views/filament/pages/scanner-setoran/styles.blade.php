<style>
    /* ===== GOOGLE FONT ===== */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

    /* ===== RESET & BASE ===== */
    .qr-wrap * { font-family: 'Inter', system-ui, sans-serif; box-sizing: border-box; }

    /* ===== SCAN LINE ANIMATION ===== */
    .qr-scan-line {
        position: absolute; top: 27%; left: 27%; width: 46%; height: 2px;
        background: linear-gradient(to right, transparent, #34d399 50%, transparent);
        box-shadow: 0 0 10px #34d399, 0 0 20px rgba(52,211,153,.4);
        animation: qr-scan 2.5s ease-in-out infinite;
        pointer-events: none; z-index: 6;
    }
    @keyframes qr-scan {
        0%   { top: 27%; opacity:0; }
        8%   { opacity:1; }
        92%  { opacity:1; }
        100% { top: 73%; opacity:0; }
    }

    /* ===== FULLSCREEN ===== */
    .qr-fullscreen { position:fixed !important; inset:0; z-index:9999; background:#0f172a; overflow-y:auto; }

    [x-cloak] { display:none !important; }
</style>
