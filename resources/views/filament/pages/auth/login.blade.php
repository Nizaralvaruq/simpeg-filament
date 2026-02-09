<div class="flex min-h-screen w-full flex-col lg:flex-row shadow-none border-0">
    {{-- Left Side: Branding (Hidden on mobile) --}}
    <div
        class="relative hidden h-screen items-center justify-center bg-premium-mesh lg:flex lg:w-3/5 overflow-hidden border-0 outline-none">

        {{-- Elegant Decorative Elements --}}
        <div
            class="absolute -top-40 -left-40 h-[800px] w-[800px] rounded-full bg-sky-400/5 blur-[120px] animate-subtle-pulse">
        </div>
        <div class="absolute -bottom-40 -right-40 h-[800px] w-[800px] rounded-full bg-blue-600/5 blur-[120px] animate-subtle-pulse"
            style="animation-delay: 3s"></div>

        <div class="relative z-10 flex flex-col items-center p-12 text-center">
            {{-- Logo with depth --}}
            <div class="mb-10">
                <img src="{{ asset('images/logo1.png') }}" alt="Logo"
                    class="h-40 w-auto drop-shadow-[0_20px_50px_rgba(0,0,0,0.1)] dark:drop-shadow-[0_20px_50px_rgba(0,0,0,0.5)] transition-all duration-500" />
            </div>

            {{-- Brand Name & Tagline --}}
            <div class="space-y-4">
                <h1 class="text-7xl font-black tracking-tight text-white uppercase drop-shadow-sm">
                    IHYA
                </h1>
                <p class="text-xl font-bold text-white tracking-wide uppercase">
                    Integrated Holistic Yayasan Application
                </p>
            </div>

            {{-- Tagline --}}
            <div class="mt-12">
                <div class="glass-premium max-w-sm p-10 rounded-[2.5rem]">
                    <p class="text-lg text-white leading-relaxed font-light italic">
                        "Menghidupkan Ekosistem Pendidikan Melalui Teknologi"
                    </p>
                </div>
            </div>
        </div>

        {{-- Texture Overlay --}}
        <div class="absolute inset-0 opacity-[0.03] mix-blend-soft-light"
            style="background-image: url('https://www.transparenttextures.com/patterns/carbon-fibre.png');"></div>
    </div>

    {{-- Right Side: Login Form --}}
    <div
        class="flex flex-1 flex-col items-center justify-center bg-slate-50 dark:bg-gray-950 px-6 py-12 lg:w-2/5 border-0 outline-none transition-colors duration-300 relative overflow-hidden">
        {{-- Subtle background decoration for form side --}}
        <div
            class="absolute top-0 right-0 w-64 h-64 bg-sky-100/20 rounded-full blur-3xl -mr-32 -mt-32 dark:bg-sky-900/10">
        </div>
        <div
            class="absolute bottom-0 left-0 w-64 h-64 bg-blue-100/20 rounded-full blur-3xl -ml-32 -mb-32 dark:bg-blue-900/10">
        </div>

        {{-- Mobile Logo --}}
        <div class="mb-8 flex justify-center lg:hidden relative z-10">
            <img src="{{ asset('images/logo1.png') }}" alt="Logo" class="h-16 w-auto drop-shadow-lg" />
        </div>

        <div class="w-full max-w-sm space-y-8 relative z-10">
            {{-- Form Header --}}
            <div class="text-center lg:text-left">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white transition-colors">
                    Sign In
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Selamat datang kembali! Silakan masuk ke akun Anda.
                </p>
            </div>

            {{-- Filament Login Content (Form + Actions) --}}
            {{ $this->content }}

            {{-- Footer (Registration if enabled) --}}
            @if (filament()->hasRegistration())
                <p class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400">
                    Belum punya akun?
                    <a href="{{ filament()->getRegistrationUrl() }}"
                        class="font-semibold text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                        Daftar sekarang
                    </a>
                </p>
            @endif
        </div>
    </div>

    <style>
        .bg-premium-mesh {
            background-color: #1e2a4a;
            background-image:
                radial-gradient(at 0% 0%, #075985 0px, transparent 55%),
                radial-gradient(at 100% 0%, #0369a1 0px, transparent 55%),
                radial-gradient(at 100% 100%, #1e293b 0px, transparent 55%),
                radial-gradient(at 0% 100%, #0c4a6e 0px, transparent 55%);
        }

        .glass-premium {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.5);
            transition: all 0.5s ease;
        }

        .dark .glass-premium {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Force remove any lingering borders/rings on the root container */
        .fi-simple-main,
        .fi-simple-main>div,
        .fi-simple-layout {
            border: 0 !important;
            box-shadow: none !important;
            outline: none !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            background: transparent !important;
        }

        /* Ensure the split screen takes full height without padding */
        .fi-simple-page {
            padding: 0 !important;
            margin: 0 !important;
            max-width: 100% !important;
        }

        body {
            overflow-x: hidden;
        }

        .animate-subtle-pulse {
            animation: subtle-pulse 10s infinite alternate;
        }

        @keyframes subtle-pulse {
            from {
                opacity: 0.1;
                transform: scale(1);
            }

            to {
                opacity: 0.2;
                transform: scale(1.1);
            }
        }
    </style>
</div>
