{{--
    Login Page - INSAN Themed
    One consistent look — dark navy bg + white card — same for both light & dark OS preference
--}}
<div class="insan-login-wrapper">

    {{-- ===== LEFT SIDE: Dark Branding ===== --}}
    <div class="insan-left">
        <div class="insan-grid-bg"></div>

        <div class="insan-left-content">
            {{-- Logo Box --}}
            <div class="insan-logo-box">
                <img src="{{ asset('images/logo1.png') }}" alt="Logo" class="insan-logo-img" />
            </div>

            {{-- Brand Name --}}
            <h1 class="insan-brand-name">INSAN</h1>

            {{-- Abbreviation --}}
            <p class="insan-brand-abbr">
                INTEGRATED NETWORK FOR STRATEGIC<br>ADMINISTRATION &amp; NOBLESSE
            </p>

            {{-- Quote --}}
            <div class="insan-quote-card">
                <svg class="insan-quote-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M11.192 15.757c0-.88-.23-1.618-.69-2.217-.326-.412-.768-.683-1.327-.812-.55-.128-1.07-.137-1.54-.028-.16-.95.1-1.956.76-3.022.66-1.065 1.515-1.867 2.558-2.403L9.373 5c-.8.396-1.56.898-2.26 1.505-.71.607-1.34 1.305-1.9 2.094s-.98 1.68-1.25 2.69-.346 2.04-.217 3.1c.168 1.4.62 2.52 1.356 3.35.735.84 1.652 1.26 2.748 1.26.965 0 1.766-.29 2.4-.878.628-.576.94-1.365.94-2.365zm10.324 0c0-.88-.23-1.618-.69-2.217-.326-.412-.768-.683-1.327-.812-.55-.128-1.07-.137-1.54-.028-.16-.95.1-1.956.76-3.022.66-1.065 1.515-1.867 2.558-2.403L19.697 5c-.8.396-1.56.898-2.26 1.505-.71.607-1.34 1.305-1.9 2.094s-.98 1.68-1.25 2.69-.346 2.04-.217 3.1c.168 1.4.62 2.52 1.356 3.35.735.84 1.652 1.26 2.748 1.26.965 0 1.766-.29 2.4-.878.628-.576.94-1.365.94-2.365z" />
                </svg>
                <p class="insan-quote-text">
                    "Menghidupkan Ekosistem Pendidikan Melalui Teknologi"
                </p>
            </div>
        </div>
    </div>

    {{-- ===== RIGHT SIDE: Form ===== --}}
    <div class="insan-right">
        <div class="insan-form-wrap">
            {{-- Header --}}
            <h2 class="insan-form-title">Sign In</h2>
            <p class="insan-form-subtitle">Selamat datang kembali! Silakan masuk ke akun Anda.</p>

            {{-- Filament Login Content --}}
            <div class="insan-filament-wrap">
                {{ $this->content }}
            </div>

            {{-- Bottom Register Link --}}
            <p class="insan-register-link">
                Belum punya akun?
                @if (filament()->hasRegistration())
                    <a href="{{ filament()->getRegistrationUrl() }}">Hubungi Admin</a>
                @else
                    <a href="#">Hubungi Admin</a>
                @endif
            </p>
        </div>
    </div>

    <style>
        /* =====================================================
           INSAN LOGIN — One Consistent Theme
           Dark navy bg (always) + white card form
           No dark/light mode switching — same for everyone
           ===================================================== */

        /* Force override any OS/browser dark mode on this page */
        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100% !important;
            background-color: #060b19 !important;
            color-scheme: light !important; /* prevent browser dark-mode override on form */
        }

        /* ---- Main Wrapper ---- */
        .insan-login-wrapper {
            display: flex;
            flex-direction: row;
            min-height: 100vh;
            width: 100%;
            font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif;
        }

        /* ========================================
           LEFT SIDE — Dark Branding Panel
           ======================================== */
        .insan-left {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 55%;
            background: linear-gradient(135deg, #020617 0%, #0f172a 100%);
            overflow: hidden;
        }

        /* Animated mesh gradient */
        .insan-grid-bg {
            position: absolute;
            inset: -50%;
            background-image:
                radial-gradient(circle at 50% 50%, rgba(14, 165, 233, 0.15) 0%, transparent 60%),
                radial-gradient(circle at 80% 20%, rgba(56, 189, 248, 0.10) 0%, transparent 50%),
                radial-gradient(circle at 20% 80%, rgba(2, 132, 199, 0.15) 0%, transparent 50%);
            animation: mesh-rotate 20s linear infinite;
            z-index: 0;
        }

        @keyframes mesh-rotate {
            0%   { transform: rotate(0deg)   scale(1.2); }
            50%  { transform: rotate(180deg) scale(1.5); }
            100% { transform: rotate(360deg) scale(1.2); }
        }

        /* Floating decorations */
        .insan-left::before,
        .insan-left::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.03);
            z-index: 1;
        }
        .insan-left::before { width:300px; height:300px; top:-100px;   left:-100px;  border:1px solid rgba(255,255,255,0.05); }
        .insan-left::after  { width:500px; height:500px; bottom:-200px; right:-150px; border:1px solid rgba(255,255,255,0.02); }

        .insan-left-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 3rem 2rem;
            gap: 1.5rem;
            max-width: 500px;
            animation: fade-in-up 1s ease-out;
        }

        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .insan-logo-box {
            width: 120px; height: 120px;
            border-radius: 20px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.10);
            display: flex; align-items: center; justify-content: center;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
        }
        .insan-logo-img { width: auto; height: 88px; object-fit: contain; }

        .insan-brand-name {
            font-size: 72px; font-weight: 900;
            color: #ffffff;
            letter-spacing: 0.1em; line-height: 1; margin: 0;
            text-shadow: 0 4px 24px rgba(0,0,0,0.4);
        }
        .insan-brand-abbr {
            font-size: 11px; font-weight: 600;
            letter-spacing: 0.2em; text-transform: uppercase;
            color: #38bdf8; line-height: 1.8; margin: 0;
        }

        .insan-quote-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 20px;
            padding: 1.75rem 2rem;
            margin-top: 0.5rem;
            max-width: 420px;
        }
        .insan-quote-icon {
            width: 32px; height: 32px;
            color: #38bdf8;
            margin: 0 auto 0.75rem; display: block;
        }
        .insan-quote-text {
            font-size: 15px; font-style: italic;
            color: #94a3b8; line-height: 1.7; margin: 0;
        }

        /* ========================================
           RIGHT SIDE — White Card Form
           Always white card on dark bg, no toggle
           ======================================== */
        .insan-right {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 45%;
            background-color: #0f172a; /* dark navy — matches left panel */
            padding: 4rem 3rem;
        }

        /* The card itself */
        .insan-form-wrap {
            background: #ffffff;
            border-radius: 24px;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.5);
            animation: fade-in-up 0.8s ease-out both;
        }

        .insan-form-title {
            font-size: 26px; font-weight: 700;
            color: #0f172a; margin: 0 0 0.4rem;
            letter-spacing: -0.02em;
            text-align: center;
        }
        .insan-form-subtitle {
            font-size: 14px; color: #64748b;
            margin: 0 0 2rem; line-height: 1.5;
            text-align: center;
        }
        .insan-filament-wrap { width: 100%; }

        .insan-register-link {
            text-align: center;
            font-size: 13.5px; color: #64748b;
            margin-top: 1.5rem;
        }
        .insan-register-link a {
            color: #0ea5e9; font-weight: 600;
            text-decoration: none; transition: color 0.2s;
        }
        .insan-register-link a:hover { color: #0284c7; text-decoration: underline; }

        /* =====================================================
           FILAMENT FORM OVERRIDES — Always light card style
           ===================================================== */
        .fi-simple-main,
        .fi-simple-main > div,
        .fi-simple-layout,
        .fi-simple-main-ctn main {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            border-radius: 0 !important;
        }

        .fi-form { gap: 1.25rem !important; }

        /* Labels — always dark text */
        .fi-fo-field-label,
        .fi-fo-field-label *,
        .fi-fo-field-wrp-label,
        .fi-fo-field-wrp-label *,
        .dark .fi-fo-field-label,
        .dark .fi-fo-field-label *,
        .dark .fi-fo-field-wrp-label,
        .dark .fi-fo-field-wrp-label * {
            font-size: 14px !important;
            font-weight: 600 !important;
            color: #1e293b !important;
        }

        /* Input wrapper — always light */
        .fi-input-wrp,
        .dark .fi-input-wrp {
            background-color: #f8fafc !important;
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 12px !important;
            box-shadow: none !important;
            transition: all 0.2s ease !important;
            height: 52px !important;
            padding: 0 1rem !important;
        }
        .fi-input-wrp:focus-within,
        .dark .fi-input-wrp:focus-within {
            border-color: #0ea5e9 !important;
            background-color: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(14,165,233,0.12) !important;
        }

        /* Input text — always dark */
        .fi-input-wrp input,
        .dark .fi-input-wrp input,
        .fi-input,
        .dark .fi-input {
            background: transparent !important;
            color: #0f172a !important;
            font-size: 15px !important;
            font-weight: 500 !important;
            border: none !important;
            box-shadow: none !important;
            padding-left: 0.25rem !important;
        }
        .fi-input-wrp input::placeholder,
        .dark .fi-input-wrp input::placeholder,
        .fi-input::placeholder,
        .dark .fi-input::placeholder {
            color: #94a3b8 !important;
            font-weight: 400 !important;
        }

        /* Helper text */
        .fi-fo-field-wrp-helper-text,
        .dark .fi-fo-field-wrp-helper-text {
            font-size: 12.5px !important;
            color: #64748b !important;
            margin-top: 5px !important;
        }

        /* Validation error */
        .fi-fo-field-wrp-error-message,
        .dark .fi-fo-field-wrp-error-message {
            color: #ef4444 !important;
            font-size: 12.5px !important;
        }

        /* Submit button — sky blue gradient, always */
        .fi-btn[type="submit"],
        button[type="submit"],
        .dark .fi-btn[type="submit"],
        .dark button[type="submit"] {
            width: 100% !important;
            height: 52px !important;
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important;
            color: #ffffff !important;
            font-size: 15px !important; font-weight: 700 !important;
            letter-spacing: 0.02em !important;
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 8px 24px -4px rgba(14,165,233,0.45) !important;
            margin-top: 1.25rem !important;
            cursor: pointer !important;
            transition: all 0.25s ease !important;
            justify-content: center !important;
        }
        .fi-btn[type="submit"]:hover,
        .dark .fi-btn[type="submit"]:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 14px 32px -4px rgba(14,165,233,0.55) !important;
        }
        .fi-btn[type="submit"]:active,
        .dark .fi-btn[type="submit"]:active {
            transform: translateY(0) !important;
            box-shadow: 0 4px 12px -4px rgba(14,165,233,0.4) !important;
        }
        .fi-btn[type="submit"] span,
        .fi-btn[type="submit"] *,
        .dark .fi-btn[type="submit"] span,
        .dark .fi-btn[type="submit"] * {
            color: #ffffff !important;
        }

        /* Checkbox */
        .fi-checkbox,
        .dark .fi-checkbox {
            border-radius: 6px !important;
            border-color: #cbd5e1 !important;
            width: 18px !important; height: 18px !important;
        }
        .fi-checkbox:checked,
        .dark .fi-checkbox:checked {
            background-color: #0ea5e9 !important;
            border-color: #0ea5e9 !important;
        }
        .fi-fo-checkbox label,
        .fi-fo-checkbox span,
        .dark .fi-fo-checkbox label,
        .dark .fi-fo-checkbox span {
            font-size: 14px !important;
            color: #475569 !important; font-weight: 500 !important;
        }

        /* Password reveal button */
        .fi-input-wrp button,
        .dark .fi-input-wrp button,
        .fi-input-wrp button *,
        .dark .fi-input-wrp button * {
            background: transparent !important;
            color: #94a3b8 !important;
            transition: color 0.2s !important;
        }
        .fi-input-wrp button:hover,
        .dark .fi-input-wrp button:hover { color: #0ea5e9 !important; }


        /* =====================================================
           MOBILE — Stack vertically, card on dark bg
           ===================================================== */
        @media (max-width: 1023px) {
            html, body { background-color: #0f172a !important; }

            .insan-login-wrapper {
                flex-direction: column;
                min-height: 100vh;
                background: linear-gradient(160deg, #0f172a 0%, #1e293b 60%, #0f172a 100%);
                align-items: center;
                justify-content: center;
                padding: 2rem 1.25rem;
            }

            /* Hide the big left panel */
            .insan-left { display: none; }

            .insan-right {
                width: 100%;
                min-height: unset;
                padding: 0;
                background: transparent;
            }

            /* Card with logo above it */
            .insan-form-wrap {
                position: relative;
                padding-top: 3rem; /* space for logo */
            }

            /* Mini logo floating above card */
            .insan-form-wrap::before {
                content: '';
                position: absolute;
                top: -36px;
                left: 50%;
                transform: translateX(-50%);
                width: 72px; height: 72px;
                background: url('{{ asset("images/logo1.png") }}') center/56px no-repeat,
                            #1e293b;
                border-radius: 18px;
                border: 2px solid rgba(255,255,255,0.1);
                box-shadow: 0 8px 24px rgba(0,0,0,0.4);
            }
        }

        /* =====================================================
           HIDE ALPINE CLOAK
           ===================================================== */
        [x-cloak] { display: none !important; }
    </style>
</div>
