{{--
    Login Page - INSAN Themed
    Matching Google Stitch design: dark left panel + light right form panel
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

    {{-- ===== RIGHT SIDE: Light Form ===== --}}
    <div class="insan-right">

        {{-- Dark Mode Toggle Button --}}
        <button class="insan-theme-btn"
            onclick="document.documentElement.classList.toggle('dark'); this.querySelector('.icon-moon').style.display = document.documentElement.classList.contains('dark') ? 'none' : 'block'; this.querySelector('.icon-sun').style.display = document.documentElement.classList.contains('dark') ? 'block' : 'none';"
            aria-label="Toggle theme">
            <span class="icon-moon">
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" />
                </svg>
            </span>
            <span class="icon-sun" style="display:none">
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="4" />
                    <path
                        d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" />
                </svg>
            </span>
        </button>

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
           INSAN LOGIN PAGE STYLES
           Matching Google Stitch design
           ===================================================== */

        /* Reset body background for this page */
        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100% !important;
        }

        body {
            background-color: #060b19 !important;
        }

        /* ---- Main Wrapper ---- */
        .insan-login-wrapper {
            display: flex;
            flex-direction: row;
            min-height: 100vh;
            width: 100%;
            font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif;
        }

        /* ---- LEFT SIDE (Dark Panel) ---- */
        .insan-left {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 55%;
            background: linear-gradient(135deg, #020617 0%, #0f172a 100%);
            overflow: hidden;
        }

        /* Dynamic Animated Mesh Gradient Background */
        .insan-grid-bg {
            position: absolute;
            inset: -50%;
            background-image:
                radial-gradient(circle at 50% 50%, rgba(14, 165, 233, 0.15) 0%, transparent 60%),
                radial-gradient(circle at 80% 20%, rgba(56, 189, 248, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 20% 80%, rgba(2, 132, 199, 0.15) 0%, transparent 50%);
            animation: mesh-rotate 20s linear infinite;
            z-index: 0;
        }

        @keyframes mesh-rotate {
            0% {
                transform: rotate(0deg) scale(1.2);
            }

            50% {
                transform: rotate(180deg) scale(1.5);
            }

            100% {
                transform: rotate(360deg) scale(1.2);
            }
        }

        /* Floating decoration elements */
        .insan-left::before,
        .insan-left::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.03);
            z-index: 1;
        }

        .insan-left::before {
            width: 300px;
            height: 300px;
            top: -100px;
            left: -100px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .insan-left::after {
            width: 500px;
            height: 500px;
            bottom: -200px;
            right: -150px;
            border: 1px solid rgba(255, 255, 255, 0.02);
        }

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
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Logo in a rounded dark box */
        .insan-logo-box {
            width: 120px;
            height: 120px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        }

        .insan-logo-img {
            width: auto;
            height: 88px;
            object-fit: contain;
        }

        /* Brand name */
        .insan-brand-name {
            font-size: 72px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: 0.1em;
            line-height: 1;
            margin: 0;
            text-shadow: 0 4px 24px rgba(0, 0, 0, 0.4);
        }

        /* Abbreviation (small cyan caps) */
        .insan-brand-abbr {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #38bdf8;
            line-height: 1.8;
            margin: 0;
        }

        /* Quote card */
        .insan-quote-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 1.75rem 2rem;
            margin-top: 0.5rem;
            max-width: 420px;
            position: relative;
        }

        .insan-quote-icon {
            width: 32px;
            height: 32px;
            color: #38bdf8;
            margin: 0 auto 0.75rem;
            display: block;
        }

        .insan-quote-text {
            font-size: 15px;
            font-style: italic;
            color: #94a3b8;
            line-height: 1.7;
            margin: 0;
        }

        /* ---- RIGHT SIDE (Light Form) ---- */
        .insan-right {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 45%;
            background-color: #f8fafc;
            padding: 4rem 3rem;
        }

        .insan-theme-btn {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e2e8f0;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            transition: background 0.2s;
        }

        .insan-theme-btn:hover {
            background-color: #cbd5e1;
        }

        .insan-form-wrap {
            width: 100%;
            max-width: 380px;
            animation: fade-in-up 0.8s ease-out forwards;
            opacity: 0;
            transform: translateY(15px);
            animation-fill-mode: both;
        }

        .insan-form-title {
            font-size: 28px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 0.5rem;
            letter-spacing: -0.02em;
        }

        .insan-form-subtitle {
            font-size: 14.5px;
            color: #64748b;
            margin: 0 0 2.5rem;
            line-height: 1.5;
        }

        .insan-filament-wrap {
            width: 100%;
        }

        .insan-register-link {
            text-align: center;
            font-size: 13.5px;
            color: #64748b;
            margin-top: 1.5rem;
        }

        .insan-register-link a {
            color: #0ea5e9;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .insan-register-link a:hover {
            color: #0284c7;
            text-decoration: underline;
        }

        /* =====================================================
           FILAMENT FORM OVERRIDES
           ===================================================== */

        /* =====================================================
           FILAMENT FORM OVERRIDES
           ===================================================== */

        /* Remove Filament's card/border wrappers */
        .fi-simple-main,
        .fi-simple-main>div,
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

        /* Field spacing */
        .fi-form {
            gap: 1.5rem !important;
        }

        /* Labels */
        .fi-fo-field-wrp-label {
            font-size: 14px !important;
            font-weight: 600 !important;
            color: #1e293b !important;
            margin-bottom: 8px !important;
            letter-spacing: -0.01em !important;
        }

        /* Input wrappers - Modern Light Theme */
        .fi-input-wrp {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02) !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            height: 52px !important;
            padding: 0 1rem !important;
        }

        .fi-input-wrp:focus-within {
            border-color: #0ea5e9 !important;
            background-color: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1) !important;
        }

        /* Input text */
        .fi-input-wrp input {
            background: transparent !important;
            color: #0f172a !important;
            font-size: 15px !important;
            font-weight: 500 !important;
            border: none !important;
            box-shadow: none !important;
            padding-left: 0.25rem !important;
        }

        .fi-input-wrp input::placeholder {
            color: #94a3b8 !important;
            font-weight: 400 !important;
        }

        /* Helper text */
        .fi-fo-field-wrp-helper-text {
            font-size: 12.5px !important;
            color: #64748b !important;
            margin-top: 6px !important;
        }

        /* Submit / Masuk button */
        .fi-btn[type="submit"],
        button[type="submit"] {
            width: 100% !important;
            height: 54px !important;
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important;
            color: #ffffff !important;
            font-size: 16px !important;
            font-weight: 700 !important;
            letter-spacing: 0.02em !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 10px 25px -5px rgba(14, 165, 233, 0.4), 0 8px 10px -6px rgba(14, 165, 233, 0.2) !important;
            margin-top: 1.5rem !important;
            cursor: pointer !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            justify-content: center !important;
        }

        .fi-btn[type="submit"]:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 14px 28px -5px rgba(14, 165, 233, 0.5), 0 10px 10px -6px rgba(14, 165, 233, 0.3) !important;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
        }

        .fi-btn[type="submit"]:active {
            transform: translateY(1px) !important;
            box-shadow: 0 4px 10px -4px rgba(14, 165, 233, 0.4) !important;
        }

        .fi-btn[type="submit"] span,
        .fi-btn[type="submit"] * {
            color: #ffffff !important;
        }

        /* Checkbox */
        .fi-checkbox {
            border-radius: 6px !important;
            border-color: #cbd5e1 !important;
            width: 18px !important;
            height: 18px !important;
            transition: all 0.2s !important;
        }

        .fi-checkbox:checked {
            background-color: #0ea5e9 !important;
            border-color: #0ea5e9 !important;
            box-shadow: 0 2px 4px rgba(14, 165, 233, 0.2) !important;
        }

        .fi-fo-checkbox label,
        .fi-fo-checkbox span {
            font-size: 14px !important;
            color: #475569 !important;
            font-weight: 500 !important;
        }

        /* Password reveal */
        .fi-input-wrp button {
            background: transparent !important;
            color: #94a3b8 !important;
            transition: color 0.2s !important;
        }

        .fi-input-wrp button:hover {
            color: #0ea5e9 !important;
        }

        /* =====================================================
           DARK MODE SUPPORT (right side only)
           ===================================================== */
        .dark .insan-right {
            background-color: #0f172a;
        }

        .dark .insan-form-title {
            color: #f8fafc;
        }

        .dark .insan-form-subtitle {
            color: #94a3b8;
        }

        .dark .insan-theme-btn {
            background-color: #1e293b;
            color: #94a3b8;
            border: 1px solid #334155;
        }

        .dark .insan-theme-btn:hover {
            background-color: #334155;
            color: #f1f5f9;
        }

        .dark .insan-register-link {
            color: #94a3b8;
        }

        .dark .insan-register-link a {
            color: #38bdf8;
        }

        .dark .fi-fo-field-wrp-label {
            color: #f1f5f9 !important;
        }

        .dark .fi-input-wrp {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
        }

        .dark .fi-input-wrp:focus-within {
            background-color: #0f172a !important;
            border-color: #0ea5e9 !important;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.15) !important;
        }

        .dark .fi-input-wrp input {
            color: #f8fafc !important;
        }

        .dark .fi-fo-checkbox span {
            color: #cbd5e1 !important;
        }

        /* =====================================================
           MOBILE
           ===================================================== */
        @media (max-width: 1023px) {

            html,
            body {
                background-color: #f8fafc !important;
            }

            .insan-login-wrapper {
                flex-direction: column;
                min-height: 100vh;
            }

            .insan-left {
                display: none;
            }

            .insan-right {
                width: 100%;
                min-height: 100vh;
                padding: 3rem 2rem;
                background-color: #f8fafc;
            }
        }

        /* Hide Alpine cloak */
        [x-cloak] {
            display: none !important;
        }
    </style>
</div>
