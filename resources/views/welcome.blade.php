<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Kepegawaian - Modern HR Solution</title>
    <meta name="description" content="Solusi Manajemen Kepegawaian Modern, Aman, dan Terintegrasi.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #6366f1;
            --primary-light: #818cf8;
            --secondary: #4f46e5;
            --accent: #f43f5e;
            --bg: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --text-main: #f8fafc;
            --text-dim: #94a3b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            background:
                radial-gradient(circle at 0% 0%, rgba(99, 102, 241, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(244, 63, 94, 0.1) 0%, transparent 50%),
                #0f172a;
        }

        /* Hero image background */
        .hero-bg {
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background-image: url("{{ asset('assets/images/hero.png') }}");
            background-size: cover;
            background-position: center;
            opacity: 0.15;
            mask-image: linear-gradient(to bottom, black, transparent);
            z-index: -1;
        }

        header {
            padding: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            z-index: 10;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(45deg, var(--primary-light), var(--accent));
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
        }

        .hero-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            max-width: 900px;
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .badge {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
            padding: 0.5rem 1.25rem;
            border-radius: 99px;
            font-size: 0.875rem;
            color: var(--primary-light);
            margin-bottom: 2rem;
            font-weight: 600;
            backdrop-filter: blur(4px);
        }

        h1 {
            font-size: clamp(2.5rem, 8vw, 4.5rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            letter-spacing: -2px;
        }

        h1 span {
            color: var(--primary-light);
        }

        .description {
            font-size: 1.25rem;
            color: var(--text-dim);
            max-width: 600px;
            margin-bottom: 3rem;
            line-height: 1.6;
        }

        .cta-group {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            text-decoration: none;
            padding: 1.25rem 2.5rem;
            border-radius: 16px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            color: white;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.5);
            filter: brightness(1.1);
        }

        .btn-glass {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            backdrop-filter: blur(12px);
        }

        .btn-glass:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-5px);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            width: 100%;
            max-width: 1200px;
            margin: 4rem auto;
            padding: 0 2rem;
        }

        .feature-card {
            background: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 2rem;
            border-radius: 24px;
            backdrop-filter: blur(20px);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            border-color: rgba(99, 102, 241, 0.3);
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            background: rgba(99, 102, 241, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-light);
            margin-bottom: 1.5rem;
        }

        .feature-card h3 {
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
        }

        .feature-card p {
            color: var(--text-dim);
            font-size: 0.95rem;
            line-height: 1.5;
        }

        footer {
            padding: 2rem;
            text-align: center;
            color: var(--text-dim);
            font-size: 0.875rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 3rem;
            }

            .cta-group {
                flex-direction: column;
                width: 100%;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="hero-bg"></div>

    <header>
        <div class="logo">KEPEGAWAIAN.</div>
        @if (Route::has('login'))
        <nav>
            @auth
            <a href="{{ url('/admin') }}" class="btn btn-glass" style="padding: 0.75rem 1.5rem; font-size: 0.875rem; border-radius: 99px;">Dashboard</a>
            @else
            <a href="{{ url('/admin/login') }}" class="btn btn-glass" style="padding: 0.75rem 1.5rem; font-size: 0.875rem; border-radius: 99px;">Login Portal</a>
            @endauth
        </nav>
        @endif
    </header>

    <main>
        <div class="hero-section">
            <div class="badge">Sistem Kepegawaian Generasi Terbaru</div>
            <h1>Kelola SDM Lebih <span>Cerdas</span> & Terintegrasi</h1>
            <p class="description">
                Solusi all-in-one untuk manajemen data induk, pengajuan cuti,
                penilaian kinerja 360 derajat, hingga efisiensi administrasi SDM.
            </p>

            <div class="cta-group">
                <a href="{{ url('/admin/login') }}" class="btn btn-primary">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Portal Admin
                </a>
                <a href="{{ url('/admin/login') }}" class="btn btn-glass">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Portal Staff
                </a>
            </div>
        </div>
    </main>

    <div class="features">
        <div class="feature-card">
            <div class="feature-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h3>Manajemen Data Induk</h3>
            <p>Penyimpanan data pegawai terpusat dengan otomasi pembuatan akun dan integrasi unit kerja.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3>Penilaian 360 Derajat</h3>
            <p>Sistem evaluasi kinerja yang adil dan transparan dari atasan, rekan sejawat, hingga diri sendiri.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                </svg>
            </div>
            <h3>Portal Mandiri Staff (ESS)</h3>
            <p>Staff dapat mengelola data pribadi, mengajukan cuti, dan melihat hasil penilaian secara mandiri.</p>
        </div>
    </div>

    <footer>
        <p>&copy; {{ date('Y') }} Sistem Kepegawaian Modern. Built with Filament & Laravel.</p>
    </footer>
</body>

</html>