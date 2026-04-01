<x-layouts.app.sidebar title="Ujian Berlangsung">
    <flux:main>
        <div class="max-w-4xl mx-auto py-12">
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Work In Progress:</strong> Halaman Player CBT sedang dibangun. Silakan kembali untuk mengecek progres selanjutnya.
                        </p>
                    </div>
                </div>
            </div>

            <h1 class="text-2xl font-bold">{{ $exam->title }}</h1>
            <p class="text-gray-600 mt-2">Waktu ujian Anda mulai berjalan sejak: {{ $session->start_time->format('H:i:s') }}</p>

            <div class="mt-8 p-6 bg-white shadow rounded-lg text-center">
                <h2 class="text-xl text-gray-800">Area Soal Pilihan Ganda</h2>
                <div class="animate-pulse mt-4 flex space-x-4 justify-center">
                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                </div>
            </div>
            
            <form action="{{ route('cbt.index') }}" method="GET" class="mt-4">
                <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                    KEMBALI KE DASHBOARD (Testing)
                </button>
            </form>
        </div>
    </flux:main>
</x-layouts.app.sidebar>
