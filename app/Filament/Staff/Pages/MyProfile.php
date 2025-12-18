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
        $employee = DataInduk::where('user_id', Auth::id())->first();
        if ($employee) {
            $this->form->fill($employee->toArray());
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
            ]);
    }
}
