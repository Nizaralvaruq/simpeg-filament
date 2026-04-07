<?php

namespace Modules\CBT\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Modules\CBT\Models\Question;
use Modules\CBT\Models\QuestionOption;

class QuestionsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    use SkipsFailures;

    protected $questionBankId;
    protected int $importedCount = 0;

    public function __construct($questionBankId)
    {
        $this->questionBankId = $questionBankId;
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Lewati baris kosong atau baris instruksi
        if (empty($row['pertanyaan']) || str_starts_with(trim($row['pertanyaan']), '#')) {
            return null;
        }

        // Tentukan Tipe Soal (PG = multiple_choice, Essay = essay)
        $tipe = strtolower(trim($row['tipe'] ?? 'pg'));
        $type = $tipe === 'essay' ? 'essay' : 'multiple_choice';

        // Buat Record Pertanyaan
        $question = Question::create([
            'question_bank_id' => $this->questionBankId,
            'type'             => $type,
            'content'          => nl2br(htmlspecialchars_decode(trim($row['pertanyaan']))),
            'score_weight'     => is_numeric($row['bobot'] ?? null) ? (float) $row['bobot'] : 1,
        ]);

        // Jika Pilihan Ganda, Buat Record Opsi
        if ($type === 'multiple_choice') {
            $options      = ['A', 'B', 'C', 'D', 'E'];
            $correctLabel = strtoupper(trim($row['jawaban_benar'] ?? ''));

            foreach ($options as $label) {
                $contentKey = 'opsi_' . strtolower($label);
                $content    = trim($row[$contentKey] ?? '');

                if (filled($content)) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'label'       => $label,
                        'content'     => $content,
                        'is_correct'  => ($label === $correctLabel),
                    ]);
                }
            }
        }

        $this->importedCount++;
        return null;
    }

    /**
     * Aturan validasi per baris
     */
    public function rules(): array
    {
        return [
            'pertanyaan' => ['required', 'string'],
            'bobot'      => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'pertanyaan.required' => 'Kolom "pertanyaan" tidak boleh kosong.',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function headingRow(): int
    {
        return 8;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}
