<?php

namespace App\Filament\Staff\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Modules\Kepegawaian\Models\DataInduk;

class MyProfile extends Page implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Profil Saya';
    protected static ?string $title = 'Profil Staff';
    protected static ?string $slug = 'my-profile';

    protected string $view = 'filament.staff.pages.my-profile';

    public ?array $data = [];

    public function mount(): void
    {
        $employee = DataInduk::with(['golongan', 'units'])->where('user_id', Auth::id())->first();
        if ($employee) {
            $this->form->fill($employee->toArray());
            // Manually fill nested/dotted relationships if needed, but simple ones might work or need mapping.
            // For readonly display, we can just flatten or use computed values.
            // Actually, form fill with array works, but dotted keys like 'golongan.nama' need special handling in array
            // or we use a model for the form.

            // Simpler: Use model binding for the form if possible? 
            // Better: just fill attributes manually for display.
            $this->form->fill([
                ...$employee->attributesToArray(),
                'golongan.nama' => $employee->golongan?->nama,
                'units.nama' => $employee->units->pluck('nama')->join(', '), // Units is many-to-many?
            ]);
        }
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->data)
            ->schema([
                Section::make('Data Pribadi')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('nip')
                                    ->label('NIP'),
                                TextEntry::make('nama')
                                    ->label('Nama Lengkap'),
                                TextEntry::make('jenis_kelamin')
                                    ->label('Jenis Kelamin'),
                                TextEntry::make('tempat_lahir')
                                    ->label('Tempat Lahir'),
                                TextEntry::make('tanggal_lahir')
                                    ->label('Tanggal Lahir')
                                    ->date('d F Y'),
                                TextEntry::make('email')
                                    ->label('Email'),
                                TextEntry::make('no_hp')
                                    ->label('No. Handphone'),
                                TextEntry::make('alamat_ktp')
                                    ->label('Alamat (KTP)')
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Section::make('Kepegawaian')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('status_pegawai')
                                    ->label('Status Pegawai')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'tetap' => 'success',
                                        'kontrak' => 'warning',
                                        default => 'gray',
                                    }),
                                TextEntry::make('tanggal_masuk')
                                    ->label('Tanggal Masuk')
                                    ->date('d F Y'),
                                TextEntry::make('golongan.nama')
                                    ->label('Golongan'),
                                TextEntry::make('units.nama')
                                    ->label('Unit Kerja')
                                    ->badge(),
                            ]),
                    ]),
            ]);
    }
}
