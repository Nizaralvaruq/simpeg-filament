<x-layouts.app.sidebar title="Validasi Token Ujian">
    <flux:main>
        <div class="max-w-3xl mx-auto py-12">
            
            <div class="mb-6">
                <a href="{{ route('cbt.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center text-sm font-medium transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Dashboard
                </a>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-gray-100 dark:border-zinc-700 overflow-hidden">
                <div class="border-b border-gray-100 dark:border-zinc-700 p-6 bg-gray-50 dark:bg-zinc-800/50">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $exam->title }}</h1>
                    <p class="text-gray-600 dark:text-zinc-400 mt-2">{{ $exam->description ?? 'Tidak ada deskripsi ujian tambahan.' }}</p>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div class="p-3 bg-blue-100 dark:bg-blue-800 rounded-full text-blue-600 dark:text-blue-300 mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-blue-900 dark:text-blue-100">Durasi Pengerjaan</p>
                                <p class="text-2xl font-bold text-blue-700 dark:text-blue-200">{{ $exam->duration_minutes }} Menit</p>
                            </div>
                        </div>

                        <div class="flex items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                            <div class="p-3 bg-purple-100 dark:bg-purple-800 rounded-full text-purple-600 dark:text-purple-300 mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-purple-900 dark:text-purple-100">Jumlah Soal</p>
                                <p class="text-2xl font-bold text-purple-700 dark:text-purple-200">{{ $exam->questionBank->questions()->count() }} Soal</p>
                            </div>
                        </div>
                    </div>

                    @if(session('error'))
                        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg mb-8 border border-yellow-200 dark:border-yellow-700/50">
                        <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200 mb-2">Peringatan Penting</h3>
                        <ul class="list-disc pl-5 text-yellow-700 dark:text-yellow-300 space-y-1 text-sm">
                            <li>Waktu ujian akan otomatis berjalan sejak Anda menekan tombol "Mulai Ujian".</li>
                            <li>Pastikan koneksi internet Anda stabil.</li>
                            <li>Jangan menutup / me-refresh layar saat sedang mengerjakan soal agar waktu tidak terganggu (kecuali jika ada kendala koneksi, jawaban tetap tersimpan secara *real-time*).</li>
                        </ul>
                    </div>

                    <form action="{{ route('cbt.start', $exam->id) }}" method="POST">
                        @csrf
                        
                        @if($exam->token)
                            <div class="mb-6">
                                <label for="token" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Masukkan Token Ujian</label>
                                <input type="text" name="token" id="token" required 
                                    class="w-full text-center text-2xl tracking-widest uppercase p-4 border border-gray-300 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-zinc-900 dark:text-white transition-all shadow-sm"
                                    placeholder="XXXXXX"
                                    autocomplete="off">
                                <p class="mt-2 text-sm text-gray-500">Minta token ujian kepada pengawas ruangan Anda.</p>
                            </div>
                        @else
                            <div class="mb-6 p-4 bg-gray-50 dark:bg-zinc-900 rounded-lg text-center text-gray-600 dark:text-gray-400">
                                Ujian ini tidak memerlukan token. Anda bisa langsung memulai.
                            </div>
                        @endif

                        <button type="submit" class="w-full py-4 px-6 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-md transition-all font-bold text-lg transform hover:scale-[1.01] active:scale-[0.99] flex justify-center items-center">
                            Mulai Ujian Sekarang
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </flux:main>
</x-layouts.app.sidebar>
