<div
    class="fixed bottom-0 left-0 right-0 z-50 block md:hidden bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-t border-gray-200 dark:border-gray-700 pb-safe">
    <div class="grid grid-cols-4 h-16">
        <a href="{{ url('/admin') }}"
            class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 {{ request()->is('admin') ? 'text-sky-600 dark:text-sky-400' : '' }}">
            <x-filament::icon icon="heroicon-o-home" class="w-6 h-6" />
            <span class="text-[10px] mt-1 font-medium">Beranda</span>
        </a>

        <a href="{{ url('/admin/my-attendance') }}"
            class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 {{ request()->is('admin/my-attendance*') ? 'text-sky-600 dark:text-sky-400' : '' }}">
            <x-filament::icon icon="heroicon-o-calendar-days" class="w-6 h-6" />
            <span class="text-[10px] mt-1 font-medium">Presensi</span>
        </a>

        <a href="{{ url('/admin/tugas-penilaian-saya') }}"
            class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 {{ request()->is('admin/tugas-penilaian-saya*') ? 'text-sky-600 dark:text-sky-400' : '' }}">
            <x-filament::icon icon="heroicon-o-pencil-square" class="w-6 h-6" />
            <span class="text-[10px] mt-1 font-medium">Penilaian</span>
        </a>

        <a href="{{ url('/admin/profile') }}"
            class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 {{ request()->is('admin/profile*') ? 'text-sky-600 dark:text-sky-400' : '' }}">
            <x-filament::icon icon="heroicon-o-user" class="w-6 h-6" />
            <span class="text-[10px] mt-1 font-medium">Profil</span>
        </a>
    </div>
</div>

<style>
    /* Adjust main content padding to avoid overlapping with bottom bar on mobile */
    @media (max-width: 767px) {
        .fi-main {
            padding-bottom: 5rem !important;
        }
    }

    .pb-safe {
        padding-bottom: env(safe-area-inset-bottom);
    }
</style>
