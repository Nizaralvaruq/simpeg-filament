<?php

namespace Modules\Kepegawaian\Filament\Resources\DataIndukResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Kepegawaian\Filament\Resources\DataIndukResource;

use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DataIndukImport;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ListDataInduks extends ListRecords
{
    protected static string $resource = DataIndukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Pegawai'),

            Actions\Action::make('importExcel')
                ->label('Import Data')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->visible(function () {
                    /** @var \App\Models\User $user */
                    $user = Auth::user();
                    return $user && $user->hasRole('super_admin');
                })
                ->schema([
                    FileUpload::make('file')
                        ->label('File Excel')
                        ->helperText('Gunakan template Excel sesuai form Create Pegawai')
                        ->required()
                        ->disk('public')
                        ->directory('imports')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // xlsx
                            'application/vnd.ms-excel', // xls
                        ])
                        ->hintAction(
                            Actions\Action::make('downloadTemplate')
                                ->label('Download Template')
                                ->icon('heroicon-o-document-arrow-down')
                                ->url(asset('template/template_data_induk.xlsx'))
                                ->openUrlInNewTab()
                        ),
                ])
                ->action(function (array $data) {
                    $path = storage_path('app/public/' . $data['file']);

                    Excel::queueImport(new DataIndukImport, $path);

                    Notification::make()
                        ->title('Import masuk antrian.')
                        ->body('Data akan diproses di background. Cek log jika data tidak muncul.')
                        ->success()
                        ->send();
                }),

        ];
    }
}
