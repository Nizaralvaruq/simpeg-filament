<style>
    .qr-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .qr-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    @media (min-width: 1024px) {
        .qr-grid {
            grid-template-columns: 2fr 1fr;
        }
    }

    .qr-card {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .dark .qr-card {
        background: #1f2937;
    }

    .qr-camera {
        position: relative;
        background: #111827;
        aspect-ratio: 16/9;
    }

    .qr-badge {
        position: absolute;
        top: 1rem;
        left: 1rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.375rem 0.75rem;
        background: rgba(17, 24, 39, 0.8);
        backdrop-filter: blur(4px);
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .qr-pulse {
        position: relative;
        display: flex;
        height: 0.5rem;
        width: 0.5rem;
    }

    .qr-pulse-ring {
        position: absolute;
        display: inline-flex;
        height: 100%;
        width: 100%;
        border-radius: 9999px;
        background: #f87171;
        opacity: 0.75;
        animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
    }

    .qr-pulse-dot {
        position: relative;
        display: inline-flex;
        border-radius: 9999px;
        height: 0.5rem;
        width: 0.5rem;
        background: #ef4444;
    }

    @keyframes ping {

        75%,
        100% {
            transform: scale(2);
            opacity: 0;
        }
    }

    .qr-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.2);
        /* Much lower opacity */
        backdrop-filter: blur(1px);
        /* minimal blur */
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s;
        z-index: 10;
    }

    .qr-scan-line {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background: linear-gradient(to right, transparent, #ef4444, transparent);
        animation: scan 3s ease-in-out infinite;
    }

    @keyframes scan {

        0%,
        100% {
            top: 0%;
            opacity: 0;
        }

        10% {
            opacity: 1;
        }

        90% {
            opacity: 1;
        }

        100% {
            top: 100%;
            opacity: 0;
        }
    }

    .qr-actions {
        padding: 1rem;
        background: #f9fafb;
        border-top: 1px solid #e5e7eb;
    }

    .dark .qr-actions {
        background: rgba(31, 41, 55, 0.5);
        border-top-color: #374151;
    }

    .qr-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        cursor: pointer;
        transition: all 0.2s;
    }

    .qr-btn:hover {
        background: #f9fafb;
    }

    .qr-btn-primary {
        background: #2563eb;
        border-color: #2563eb;
        color: white;
    }

    .qr-btn-primary:hover {
        background: #1d4ed8;
    }

    .qr-btn-danger {
        background: #dc2626;
        border-color: #dc2626;
        color: white;
    }

    .qr-btn-danger:hover {
        background: #b91c1c;
    }

    .qr-btn-success {
        background: #16a34a;
        border-color: #16a34a;
        color: white;
    }

    .qr-btn-success:hover {
        background: #15803d;
    }

    .dark .qr-btn {
        background: #1f2937;
        border-color: #374151;
        color: #d1d5db;
    }

    .dark .qr-btn:hover {
        background: #374151;
    }

    .qr-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .qr-stat-card {
        background: #2563eb;
        border-radius: 0.5rem;
        padding: 1rem;
        text-align: center;
        transition: transform 0.3s;
    }

    .qr-stat-card:hover {
        transform: scale(1.05);
    }

    .qr-stat-value {
        font-size: 1.875rem;
        font-weight: 700;
        color: white;
        transition: all 0.3s;
    }

    .qr-stat-label {
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.8);
        margin-top: 0.25rem;
    }

    .qr-list-header {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .dark .qr-list-header {
        border-bottom-color: #374151;
    }

    .qr-list-title {
        font-size: 1rem;
        font-weight: 600;
        color: #111827;
    }

    .dark .qr-list-title {
        color: white;
    }

    .qr-list-body {
        padding: 1rem;
        max-height: 400px;
        overflow-y: auto;
    }

    .qr-list-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: #f9fafb;
        border-radius: 0.5rem;
        margin-bottom: 0.5rem;
        animation: slideIn 0.3s ease-out;
    }

    .dark .qr-list-item {
        background: rgba(31, 41, 55, 0.5);
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .qr-empty {
        text-align: center;
        padding: 2rem 0;
    }

    .qr-info-box {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .dark .qr-info-box {
        background: rgba(37, 99, 235, 0.1);
        border-color: rgba(37, 99, 235, 0.2);
    }

    .qr-info-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #1e40af;
        margin-bottom: 0.5rem;
    }

    .dark .qr-info-title {
        color: #93c5fd;
    }

    .qr-info-list {
        font-size: 0.75rem;
        color: #1e40af;
    }

    .dark .qr-info-list {
        color: #bfdbfe;
    }

    .qr-info-item {
        display: flex;
        align-items: start;
        gap: 0.5rem;
        margin-top: 0.375rem;
    }

    .qr-fullscreen-btn {
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 20;
    }

    .qr-admin-panel {
        background: #fef3c7;
        border: 2px solid #fbbf24;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .dark .qr-admin-panel {
        background: rgba(251, 191, 36, 0.1);
        border-color: rgba(251, 191, 36, 0.3);
    }

    .qr-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .qr-status-online {
        background: #dcfce7;
        color: #166534;
    }

    .qr-status-offline {
        background: #fee2e2;
        color: #991b1b;
    }

    .qr-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.4);
        /* Transparent background to keep camera visible */
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 100;
    }

    .qr-modal-content {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        max-width: 500px;
        width: 90%;
        text-align: center;
    }

    .dark .qr-modal-content {
        background: #1f2937;
    }

    .qr-countdown {
        font-size: 3rem;
        font-weight: 700;
        color: #2563eb;
        margin: 1rem 0;
    }

    [x-cloak] {
        display: none !important;
    }

    #reader {
        border: none !important;
    }

    #reader__scan_region,
    #reader__dashboard_section,
    #reader__header_message {
        display: none !important;
    }

    /* Fullscreen styles */
    .qr-fullscreen {
        position: fixed !important;
        inset: 0 !important;
        z-index: 9999 !important;
        background: #111827 !important;
    }

    .qr-fullscreen .qr-container {
        max-width: none !important;
        height: 100vh !important;
        display: flex !important;
        flex-direction: column !important;
    }

    /* Segmented Control Styles */
    .qr-segment-box {
        background: white;
        border-radius: 0.75rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }

    .dark .qr-segment-box {
        background: #1f2937;
        border-color: #374151;
    }

    .qr-segment-header {
        padding: 1rem;
        background: white;
        border-bottom: 1px solid #e5e7eb;
    }

    .dark .qr-segment-header {
        background: #1f2937;
        border-bottom-color: #374151;
    }

    .qr-segment-track {
        position: relative;
        background: #f3f4f6;
        padding: 0.25rem;
        border-radius: 0.5rem;
        display: flex;
        border: 1px solid #e5e7eb;
    }

    .dark .qr-segment-track {
        background: #374151;
        border-color: #4b5563;
    }

    .qr-segment-indicator {
        position: absolute;
        top: 0.25rem;
        bottom: 0.25rem;
        left: 0.25rem;
        width: calc(50% - 0.25rem);
        background: white;
        border-radius: 0.375rem;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .dark .qr-segment-indicator {
        background: #1f2937;
    }

    .qr-segment-btn {
        position: relative;
        z-index: 10;
        flex: 1;
        padding: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        text-align: center;
        border-radius: 0.375rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        color: #6b7280;
        transition: color 0.2s;
    }

    .dark .qr-segment-btn {
        color: #9ca3af;
    }

    .qr-segment-btn:hover {
        color: #374151;
    }

    .dark .qr-segment-btn:hover {
        color: #d1d5db;
    }

    .qr-segment-btn.active {
        color: #2563eb;
    }

    .dark .qr-segment-btn.active {
        color: #60a5fa;
    }

    .qr-segment-btn.active-purple {
        color: #9333ea;
    }

    .dark .qr-segment-btn.active-purple {
        color: #c084fc;
    }

    /* Admin Box Styles */
    .qr-admin-box {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .dark .qr-admin-box {
        background: #1f2937;
        border-color: #374151;
    }

    .qr-admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .qr-admin-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
    }
</style>
