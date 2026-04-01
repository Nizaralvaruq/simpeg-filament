<?php

namespace Modules\CBT\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CBT\Models\Subject;
use Modules\CBT\Models\QuestionBank;
use Modules\CBT\Models\Question;
use Modules\CBT\Models\QuestionOption;
use Modules\CBT\Models\Exam;
use Carbon\Carbon;

class CBTDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Mata Pelajaran
        $subject = Subject::create([
            'name' => 'Bahasa Indonesia',
            'code' => 'BIND',
            'is_active' => true,
        ]);

        // 2. Buat Bank Soal
        $questionBank = QuestionBank::create([
            'code' => 'BI-001',
            'name' => 'Bank Soal Bahasa Indonesia Kelas X',
            'subject_id' => $subject->id,
            'is_active' => true,
        ]);

        // 3. Buat Soal-soal (Pilihan Ganda)
        $questionsData = [
            [
                'content' => '<p>Apa ibukota dari negara Indonesia?</p>',
                'options' => [
                    ['label' => 'A', 'content' => 'Jakarta', 'is_correct' => true],
                    ['label' => 'B', 'content' => 'Bandung', 'is_correct' => false],
                    ['label' => 'C', 'content' => 'Surabaya', 'is_correct' => false],
                    ['label' => 'D', 'content' => 'Medan', 'is_correct' => false],
                ]
            ],
            [
                'content' => '<p>Siapakah presiden pertama Republik Indonesia?</p>',
                'options' => [
                    ['label' => 'A', 'content' => 'B.J. Habibie', 'is_correct' => false],
                    ['label' => 'B', 'content' => 'Soekarno', 'is_correct' => true],
                    ['label' => 'C', 'content' => 'Soeharto', 'is_correct' => false],
                    ['label' => 'D', 'content' => 'Joko Widodo', 'is_correct' => false],
                ]
            ],
            [
                'content' => '<p>Pancasila memiliki berapa sila?</p>',
                'options' => [
                    ['label' => 'A', 'content' => '3', 'is_correct' => false],
                    ['label' => 'B', 'content' => '4', 'is_correct' => false],
                    ['label' => 'C', 'content' => '5', 'is_correct' => true],
                    ['label' => 'D', 'content' => '6', 'is_correct' => false],
                ]
            ]
        ];

        foreach ($questionsData as $qData) {
            $question = Question::create([
                'question_bank_id' => $questionBank->id,
                'type' => 'multiple_choice',
                'content' => $qData['content'],
                'score_weight' => 10,
            ]);

            foreach ($qData['options'] as $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'label' => $opt['label'],
                    'content' => $opt['content'],
                    'is_correct' => $opt['is_correct'],
                ]);
            }
        }

        // 4. Buat Ujian
        Exam::create([
            'title' => 'Ujian Akhir Semester - Bahasa Indonesia',
            'description' => 'Ujian akhir semester ganjil untuk mata pelajaran bahasa indonesia. Kerjakan dengan jujur.',
            'question_bank_id' => $questionBank->id,
            'start_time' => Carbon::now()->addDay(),
            'end_time' => Carbon::now()->addDays(2),
            'duration_minutes' => 90,
            'token' => 'UASBI24',
            'show_result' => true,
            'is_active' => true,
        ]);
    }
}
