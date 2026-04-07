<div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
    <h3 class="text-lg font-bold text-gray-800">Preview Tampilan Soal</h3>
    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $record->type === 'essay' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }}">
        {{ $record->type === 'essay' ? 'ESSAY' : 'PILIHAN GANDA' }}
    </span>
</div>

<div class="p-8 bg-white space-y-8">
    <div class="flex gap-4">
        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-sm">
            ?
        </div>
        <div class="flex-grow space-y-6">
            {{-- Konten Pertanyaan --}}
            <div class="prose prose-indigo max-w-none text-gray-800 text-lg leading-relaxed">
                {!! $record->content !!}
            </div>

            {{-- Media / Gambar --}}
            @if($record->media)
                <div class="mt-4 rounded-xl overflow-hidden border border-gray-200 shadow-sm max-w-2xl mx-auto">
                    <img src="{{ asset('storage/' . $record->media) }}" alt="Gambar Soal" class="w-full object-contain max-h-[400px]">
                </div>
            @endif

            <hr class="border-gray-100 my-8">

            {{-- Pilihan Jawaban (Jika PG) --}}
            @if($record->type === 'multiple_choice')
                <div class="space-y-3">
                    @foreach($record->options()->orderBy('label')->get() as $option)
                        <div class="flex items-start gap-4 p-4 rounded-xl border-2 transition-all {{ $option->is_correct ? 'border-green-500 bg-green-50' : 'border-gray-100 bg-gray-50 hover:border-indigo-200' }}">
                            <div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center font-bold {{ $option->is_correct ? 'bg-green-600 text-white' : 'bg-white text-indigo-600 border border-gray-200' }}">
                                {{ $option->label }}
                            </div>
                            <div class="flex-grow pt-1 text-gray-700">
                                {{ $option->content }}
                            </div>
                            @if($option->is_correct)
                                <div class="flex-shrink-0 text-green-600" title="Ini adalah kunci jawaban">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Input Essay (Simulasi) --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-500">Jawaban Anda (Simulasi Input Siswa):</label>
                    <textarea readonly class="w-full h-40 rounded-xl border-gray-200 bg-gray-50 text-gray-400 p-4 italic" placeholder="Tuliskan jawaban Anda di sini..."></textarea>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="px-6 py-4 bg-gray-50 border-t border-gray-100 text-xs text-gray-400 italic">
    *Tampilan ini adalah simulasi. Kunci jawaban (centang hijau) hanya ditampilkan kepada Admin.
</div>
