<?php

namespace Modules\CBT\Filament\Resources\QuestionBankResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Width;
use Modules\CBT\Imports\QuestionsImport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    protected static ?string $recordTitleAttribute = 'content';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Tabs::make('Formulir Soal')
                ->columnSpanFull()
                ->tabs([
                    \Filament\Schemas\Components\Tabs\Tab::make('Konfigurasi & Pertanyaan')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->schema([
                            Section::make('Konten Pertanyaan')
                                ->icon('heroicon-o-pencil-square')
                                ->description('Tuliskan teks pertanyaan secara lengkap. Gunakan toolbar untuk memformat teks.')
                                ->schema([
                                    \Filament\Schemas\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Select::make('type')
                                                ->label('Tipe Soal')
                                                ->options([
                                                    'multiple_choice' => 'Pilihan Ganda',
                                                    'essay' => 'Essay',
                                                ])
                                                ->default('multiple_choice')
                                                ->required()
                                                ->live()
                                                ->native(false),

                                            Forms\Components\TextInput::make('score_weight')
                                                ->label('Bobot Nilai')
                                                ->numeric()
                                                ->default(1)
                                                ->required()
                                                ->prefix('Poin'),
                                        ]),

                                    Forms\Components\RichEditor::make('content')
                                        ->label('Pertanyaan')
                                        ->required()
                                        ->columnSpanFull()
                                        ->extraInputAttributes(['style' => 'min-height: 400px;']),
                                    
                                    Forms\Components\FileUpload::make('media')
                                        ->label('Gambar Pendukung')
                                        ->image()
                                        ->directory('cbt/questions')
                                        ->columnSpanFull()
                                        ->helperText('Unggah gambar di sini jika pertanyaan ini membutuhkan ilustrasi.'),
                                ]),
                        ]),

                    \Filament\Schemas\Components\Tabs\Tab::make('Pilihan Jawaban')
                        ->icon('heroicon-o-list-bullet')
                        ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('type') === 'multiple_choice')
                        ->schema([
                            Section::make('Daftar Pilihan')
                                ->description('Tentukan pilihan jawaban dan tandai satu sebagai jawaban yang benar.')
                                ->schema([
                                    Forms\Components\Repeater::make('options')
                                        ->relationship('options')
                                        ->schema([
                                            \Filament\Schemas\Components\Grid::make(12)
                                                ->schema([
                                                    Forms\Components\TextInput::make('label')
                                                        ->label('Label')
                                                        ->placeholder('A')
                                                        ->required()
                                                        ->columnSpan(2),

                                                    Forms\Components\Toggle::make('is_correct')
                                                        ->label('Benar')
                                                        ->onColor('success')
                                                        ->offColor('gray')
                                                        ->inline(false)
                                                        ->columnSpan(2),

                                                    Forms\Components\Textarea::make('content')
                                                        ->label('Konten Jawaban')
                                                        ->required()
                                                        ->columnSpan(8)
                                                        ->rows(2),
                                                ]),
                                        ])
                                        ->columns(1)
                                        ->defaultItems(4)
                                        ->addActionLabel('Tambah Pilihan')
                                        ->reorderableWithButtons()
                                        ->collapsible()
                                        ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'Pilihan'),
                                ]),
                        ]),
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->colors([
                        'primary' => 'multiple_choice',
                        'warning' => 'essay',
                    ]),

                Tables\Columns\TextColumn::make('content')
                    ->label('Pertanyaan')
                    ->html()
                    ->limit(50),

                Tables\Columns\TextColumn::make('score_weight')
                    ->label('Bobot')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('download_template')
                    ->label('Download Template')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function () {
                        $callback = function () {
                            $file = fopen('php://output', 'w');

                            // Baris instruksi (diawali # akan dilewati saat import)
                            fputcsv($file, ['# PETUNJUK PENGISIAN TEMPLATE SOAL']);
                            fputcsv($file, ['# Kolom tipe   : Isi dengan PG (Pilihan Ganda) atau Essay']);
                            fputcsv($file, ['# Kolom bobot  : Angka bobot nilai per soal (default 1)']);
                            fputcsv($file, ['# opsi_a s/d e : Teks pilihan jawaban (khusus PG, opsi_e boleh kosong)']);
                            fputcsv($file, ['# jawaban_benar: Tulis huruf kunci jawaban (A/B/C/D/E)']);
                            fputcsv($file, ['# Baris yang diawali # akan diabaikan saat import']);
                            fputcsv($file, []); // baris kosong sebagai pemisah

                            // Header kolom
                            fputcsv($file, ['pertanyaan', 'tipe', 'bobot', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'opsi_e', 'jawaban_benar']);

                            // Contoh soal Pilihan Ganda
                            fputcsv($file, [
                                'Apa ibukota negara Indonesia?',
                                'PG', '1',
                                'Jakarta', 'Bandung', 'Medan', 'Surabaya', '',
                                'A',
                            ]);

                            // Contoh soal dengan 5 opsi
                            fputcsv($file, [
                                'Berapakah hasil dari 5 x 8?',
                                'PG', '2',
                                '30', '35', '40', '45', '50',
                                'C',
                            ]);

                            // Contoh soal Essay
                            fputcsv($file, [
                                'Jelaskan secara singkat latar belakang proklamasi kemerdekaan Indonesia!',
                                'Essay', '5',
                                '', '', '', '', '',
                                '',
                            ]);

                            fclose($file);
                        };

                        return response()->stream($callback, 200, [
                            'Content-Type'        => 'text/csv',
                            'Content-Disposition' => 'attachment; filename="template_soal_cbt.csv"',
                        ]);
                    })->link(),

                Action::make('import_soal')
                    ->label('Import Soal (CSV)')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->modalHeading('Import Soal dari File CSV')
                    ->modalDescription('💡 Alur terbaik: Ketik soal di Excel/Google Sheets → Save As CSV → Upload di sini.')
                    ->schema([
                        Forms\Components\Placeholder::make('info_workflow')
                            ->label('📋 Panduan Singkat')
                            ->content(new \Illuminate\Support\HtmlString(
                                '<ol class="list-decimal list-inside text-sm space-y-1 text-gray-600">' .
                                '<li>Download template CSV dengan tombol <strong>Download Template</strong>.</li>' .
                                '<li>Buka file di <strong>Microsoft Excel</strong> atau <strong>Google Sheets</strong>.</li>' .
                                '<li>Isi soal sesuai format (baris bertanda # adalah instruksi, jangan dihapus).</li>' .
                                '<li>Simpan file: <strong>File → Save As → CSV (Comma delimited)</strong>.</li>' .
                                '<li>Upload file CSV hasil simpan di bawah ini.</li>' .
                                '</ol>'
                            )),

                        Forms\Components\FileUpload::make('file')
                            ->label('Upload File CSV')
                            ->required()
                            ->disk('public')
                            ->directory('cbt-imports')
                            ->acceptedFileTypes(['text/csv', 'text/plain'])
                            ->helperText('Hanya file .csv yang diterima. Ukuran maks: 5MB.')
                            ->maxSize(5120),
                    ])
                    ->action(function (array $data, RelationManager $livewire) {
                        $filePath = storage_path('app/public/' . $data['file']);

                        if (! file_exists($filePath)) {
                            Notification::make()
                                ->title('File tidak ditemukan!')
                                ->body('Pastikan file sudah berhasil diunggah.')
                                ->danger()
                                ->send();
                            return;
                        }

                        try {
                            $import = new QuestionsImport($livewire->getOwnerRecord()->id);
                            Excel::import($import, $filePath);

                            $importedCount = $import->getImportedCount();
                            $failures      = $import->failures();
                            $failureCount  = count($failures);

                            if ($failureCount > 0) {
                                $failureMessages = collect($failures)
                                    ->take(5)
                                    ->map(fn ($f) => "Baris {$f->row()}: " . implode(', ', $f->errors()))
                                    ->join("\n");

                                Notification::make()
                                    ->title("{$importedCount} soal berhasil | {$failureCount} soal gagal")
                                    ->body($failureMessages . ($failureCount > 5 ? "\n... dan " . ($failureCount - 5) . ' lainnya.' : ''))
                                    ->warning()
                                    ->persistent()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Import Berhasil!')
                                    ->body("{$importedCount} soal telah berhasil ditambahkan ke bank soal.")
                                    ->success()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Terjadi Kesalahan!')
                                ->body('Pesan: ' . $e->getMessage())
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    }),
                
                CreateAction::make()
                    ->slideOver(),
            ])
            ->recordActions([
                Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalContent(fn ($record) => view('cbt::admin.preview-question', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->slideOver(),
                EditAction::make()
                    ->slideOver(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
