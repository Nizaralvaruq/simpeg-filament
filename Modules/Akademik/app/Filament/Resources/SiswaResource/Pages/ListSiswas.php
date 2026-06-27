<?php

namespace Modules\Akademik\Filament\Resources\SiswaResource\Pages;

use Modules\Akademik\Filament\Resources\SiswaResource;
use Modules\Akademik\Imports\SiswaImport;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListSiswas extends ListRecords
{
    protected static string $resource = SiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('importExcel')
                ->label('Import Data')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->schema([
                    FileUpload::make('file')
                        ->label('File Excel / CSV')
                        ->helperText('Gunakan template Excel (.xlsx) yang tersedia. Kolom wajib: nis, nama_lengkap. Kolom opsional: kelas, nomor_wa_ortu.')
                        ->required()
                        ->disk('public')
                        ->directory('imports')
                        ->maxSize(5120)
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // xlsx
                            'application/vnd.ms-excel',                                           // xls
                            'text/csv',
                            'text/plain',
                        ])
                        ->hintAction(
                            Actions\Action::make('downloadTemplate')
                                ->label('Download Template')
                                ->icon('heroicon-o-document-arrow-down')
                                ->action(function () {
                                    return Excel::download(new \Modules\Akademik\Exports\SiswaTemplateExport, 'template_siswa.xlsx');
                                })
                        ),
                ])
                ->action(function (array $data) {
                    $path = storage_path('app/public/' . $data['file']);

                    try {
                        $import = new SiswaImport;
                        Excel::import($import, $path);

                        $imported = $import->importedCount;
                        $skipped  = $import->skippedCount;
                        $body     = "{$imported} data siswa berhasil diimport.";
                        if ($skipped > 0) {
                            $body .= " {$skipped} baris dilewati (kosong / tidak valid).";
                        }

                        Notification::make()
                            ->title('Import Berhasil')
                            ->body($body)
                            ->success()
                            ->send();

                        $this->redirect(static::getResource()::getUrl(), navigate: false);
                    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                        $failures = $e->failures();
                        $errors   = collect($failures)
                            ->map(fn($f) => "Baris {$f->row()}: " . implode(', ', $f->errors()))
                            ->take(5)
                            ->implode('<br>');
                        $more = count($failures) > 5 ? '<br>...dan ' . (count($failures) - 5) . ' error lainnya.' : '';

                        Notification::make()
                            ->title('Gagal: Validasi Data')
                            ->body("Terjadi kesalahan pada data Excel Anda:<br><br>{$errors}{$more}")
                            ->danger()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Import Gagal')
                            ->body('Terjadi kesalahan: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\CreateAction::make()
                ->label('Tambah Siswa'),
        ];
    }
}
