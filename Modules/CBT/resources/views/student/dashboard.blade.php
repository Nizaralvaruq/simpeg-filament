<x-layouts.app.sidebar title="Dashboard Ujian Siswa">
    <flux:main>
        <div class="max-w-7xl mx-auto py-6">
            <h1 class="text-3xl font-bold mb-6 text-gray-800 dark:text-gray-100">Daftar Ujian Tersedia</h1>

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($exams as $exam)
                    @php
                        $session = $sessions->get($exam->id);
                        $status = 'Tersedia';
                        $statusClass = 'bg-blue-100 text-blue-800';
                        $buttonText = 'Kerjakan';
                        $buttonUrl = route('cbt.token', $exam->id);
                        $disabled = false;

                        if ($session) {
                            if ($session->end_time) {
                                $status = 'Selesai';
                                $statusClass = 'bg-green-100 text-green-800';
                                $buttonText = 'Lihat Hasil';
                                $buttonUrl = '#'; // TODO
                                $disabled = true; // Temporary disable for finished exams
                            } else {
                                $status = 'Sedang Dikerjakan';
                                $statusClass = 'bg-yellow-100 text-yellow-800';
                                $buttonText = 'Lanjutkan';
                                $buttonUrl = route('cbt.play', $exam->id);
                            }
                        }

                        // Cek apakah waktu mulai belum tiba
                        if (!$session && $exam->start_time && $exam->start_time > now()) {
                            $status = 'Belum Dimulai';
                            $statusClass = 'bg-gray-100 text-gray-800';
                            $buttonText = 'Menunggu Waktu';
                            $disabled = true;
                        }
                    @endphp

                    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow p-6 border border-gray-100 dark:border-zinc-700">
                        <div class="flex justify-between items-start mb-4">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $exam->title }}</h2>
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                {{ $status }}
                            </span>
                        </div>
                        
                        <div class="space-y-2 mb-6 text-sm text-gray-600 dark:text-zinc-400">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Durasi: {{ $exam->duration_minutes }} Menit
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ $exam->start_time ? $exam->start_time->format('d M Y, H:i') : 'Kapan saja' }}
                            </div>
                        </div>

                        <div class="mt-4">
                            @if($disabled)
                                <button disabled class="w-full py-2 px-4 bg-gray-300 text-gray-600 rounded-lg cursor-not-allowed font-medium text-center">
                                    {{ $buttonText }}
                                </button>
                            @else
                                <a href="{{ $buttonUrl }}" class="block w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium text-center">
                                    {{ $buttonText }}
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center p-12 bg-white dark:bg-zinc-800 rounded-xl shadow border border-gray-100 dark:border-zinc-700">
                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100">Belum Ada Ujian</h3>
                        <p class="text-gray-500 mt-2">Tidak ada ujian yang tersedia untuk Anda saat ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </flux:main>
</x-layouts.app.sidebar>
