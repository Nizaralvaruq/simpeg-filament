<?php

namespace Modules\CBT\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CBTController extends Controller
{
    /**
     * Menampilkan daftar Ujian (Dashboard Siswa)
     */
    public function index()
    {
        $user = auth()->user();
        
        // Asumsi siswa memiliki record `Student` (opsional) untuk memfilter jenjang/unit
        $student = $user->student; 
        
        // Ambil ujian yang aktif dan waktunya sudah/sedang berlangsung
        $exams = \Modules\CBT\Models\Exam::where('is_active', true)
            ->where(function ($query) {
                // Ujian yang tidak pakai waktu spesifik ATAU waktu sekarang <= end_time
                $query->whereNull('end_time')
                      ->orWhere('end_time', '>=', now());
            });
            
        // Jika filter jenjang atau unit dibutuhkan
        if ($student) {
            $exams->where(function ($q) use ($student) {
                $q->whereNull('unit_type_id')->orWhere('unit_type_id', $student->unit_type_id);
            });
            $exams->where(function ($q) use ($student) {
                $q->whereNull('unit_id')->orWhere('unit_id', $student->unit_id);
            });
        }
        
        $exams = $exams->latest()->get();
        
        // Ambil sesi ujian yang sudah pernah/sedang dikerjakan user ini
        $sessions = \Modules\CBT\Models\ExamSession::where('user_id', $user->id)
            ->get()
            ->keyBy('exam_id');

        return view('cbt::student.dashboard', compact('exams', 'sessions'));
    }

    /**
     * Tampilkan form masukkan Token (Token Gateway)
     */
    public function showTokenGateway($examId)
    {
        $exam = \Modules\CBT\Models\Exam::findOrFail($examId);
        
        // Cek apakah user sudah punya sesi ujian yang selesai
        $session = \Modules\CBT\Models\ExamSession::where('user_id', auth()->id())
            ->where('exam_id', $examId)
            ->first();
            
        if ($session && $session->end_time) {
            return redirect()->route('cbt.index')->with('error', 'Anda sudah menyelesaikan ujian ini.');
        }

        return view('cbt::student.token', compact('exam', 'session'));
    }

    /**
     * Memulai ujian (Simpan sesi atau lanjut)
     */
    public function startExam(Request $request, $examId)
    {
        $exam = \Modules\CBT\Models\Exam::findOrFail($examId);
        $user = auth()->user();

        // Validasi Token jika ujian mensyaratkan token
        if ($exam->token) {
            if ($request->input('token') !== $exam->token) {
                return back()->with('error', 'Token yang dimasukkan salah!');
            }
        }

        $session = \Modules\CBT\Models\ExamSession::firstOrCreate(
            ['user_id' => $user->id, 'exam_id' => $exam->id],
            [
                'start_time' => now(),
                // end_time dibiarkan null sampai ujian selesai
            ]
        );

        return redirect()->route('cbt.play', $exam->id);
    }
    
    /**
     * Render antarmuka Player Ujian
     */
    public function play($examId)
    {
        $exam = \Modules\CBT\Models\Exam::findOrFail($examId);
        $session = \Modules\CBT\Models\ExamSession::where('user_id', auth()->id())
            ->where('exam_id', $examId)
            ->firstOrFail();
            
        if ($session->end_time) {
            return redirect()->route('cbt.index')->with('error', 'Ujian telah selesai.');
        }
            
        // Render menggunakan view yang nantinya memuat komponen Livewire "Player"
        return view('cbt::student.play', compact('exam', 'session'));
    }
}
