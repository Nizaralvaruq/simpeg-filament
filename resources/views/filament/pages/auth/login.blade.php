<div class="flex min-h-screen w-full flex-col lg:flex-row shadow-none border-0">
    {{-- Left Side: Branding (Hidden on mobile) --}}
    <div
        class="relative hidden h-screen items-center justify-center bg-[#1e2a4a] lg:flex lg:w-3/5 overflow-hidden border-0 outline-none">
        {{-- Decorative element --}}
        <div class="absolute -top-24 -left-24 h-96 w-96 rounded-full bg-blue-500/10 blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 h-96 w-96 rounded-full bg-blue-400/10 blur-3xl"></div>

        <div class="relative z-10 p-12 text-center">
            {{-- Logo --}}
            <div class="mb-8 flex justify-center">
                <img src="{{ asset('images/logo1.png') }}" alt="Logo" class="h-40 w-auto drop-shadow-2xl" />
            </div>

            {{-- Brand Name --}}
            <h1 class="mb-4 text-3xl font-bold tracking-tight text-white">
                Integrated Holistic <br> Yayasan Application
            </h1>

            {{-- Tagline --}}
            <p class="mx-auto max-w-sm text-lg text-blue-100/80 leading-relaxed">
                Menghidupkan Ekosistem Pendidikan Melalui Teknologi
            </p>
        </div>
    </div>

    {{-- Right Side: Login Form --}}
    <div
        class="flex flex-1 flex-col items-center justify-center bg-white dark:bg-gray-900 px-6 py-12 lg:w-2/5 border-0 outline-none transition-colors duration-300">
        {{-- Mobile Logo --}}
        <div class="mb-8 flex justify-center lg:hidden">
            <img src="{{ asset('images/logo1.png') }}" alt="Logo" class="h-20 w-auto" />
        </div>

        <div class="w-full max-w-sm space-y-8">
            {{-- Form Header --}}
            <div class="text-center">
                <h2
                    class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white lg:text-left transition-colors">
                    Sign In
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 lg:text-left">
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
    </style>
</div>
