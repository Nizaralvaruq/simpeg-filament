<?php

namespace App\Filament\Pages;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Illuminate\Support\Facades\Auth;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'default' => 1,
                    'lg' => 2,
                ])
                    ->schema([
                        Section::make('Informasi Akun')
                            ->description('Kelola detail akun login Anda.')
                            ->icon('heroicon-o-user-circle')
                            ->schema([
                                \Filament\Forms\Components\FileUpload::make('avatar_url')
                                    ->label('Foto Profil')
                                    ->image()
                                    ->avatar()
                                    ->imageEditor()
                                    ->directory('avatars')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->alignCenter()
                                    ->columnSpanFull(),
                                $this->getNameFormComponent(),
                                $this->getEmailFormComponent(),
                                $this->getPasswordFormComponent(),
                                $this->getPasswordConfirmationFormComponent(),
                            ])
                            ->columnSpan(1),

                        Section::make('Informasi Pribadi')
                            ->description('Data ini tersinkronisasi dengan data kepegawaian Anda.')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                TextInput::make('employee_nik')
                                    ->label('NIK')
                                    ->maxLength(255),
                                TextInput::make('employee_no_hp')
                                    ->label('No HP')
                                    ->tel(),
                                Select::make('employee_jenis_kelamin')
                                    ->label('Jenis Kelamin')
                                    ->options([
                                        'Laki-laki' => 'Laki-laki',
                                        'Perempuan' => 'Perempuan',
                                    ])
                                    ->native(false),
                                TextInput::make('employee_tempat_lahir')
                                    ->label('Tempat Lahir'),
                                DatePicker::make('employee_tanggal_lahir')
                                    ->label('Tanggal Lahir')
                                    ->displayFormat('d/m/Y'),
                                Select::make('employee_pendidikan')
                                    ->label('Pendidikan')
                                    ->options([
                                        'SMA' => 'SMA',
                                        'D1'  => 'D1',
                                        'D3'  => 'D3',
                                        'D4'  => 'D4',
                                        'S1'  => 'S1',
                                        'S2'  => 'S2',
                                        'S3'  => 'S3',
                                    ])
                                    ->searchable()
                                    ->native(false),
                                Select::make('employee_status_perkawinan')
                                    ->label('Status Perkawinan')
                                    ->options([
                                        'Belum Menikah' => 'Belum Menikah',
                                        'Menikah'       => 'Menikah',
                                        'Cerai Hidup'   => 'Cerai Hidup',
                                        'Cerai Mati'    => 'Cerai Mati',
                                    ])
                                    ->native(false),
                                TextInput::make('employee_suami_istri')
                                    ->label('Suami / Istri'),
                                Textarea::make('employee_alamat')
                                    ->label('Alamat')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->columnSpan(1),

                        Section::make('Informasi Kepegawaian')
                            ->description('Data berikut bersifat hanya baca (read-only). Hubungi HR untuk perubahan.')
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                TextInput::make('employee_nip')
                                    ->label('NPA')
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('employee_jabatan')
                                    ->label('Jabatan')
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('employee_status_kepegawaian')
                                    ->label('Status Kepegawaian')
                                    ->disabled()
                                    ->dehydrated(false),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function fillForm(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $data = $user->toArray();

        // Load employee data into the form state with prefix
        if ($user->employee) {
            foreach ($user->employee->toArray() as $key => $value) {
                $data["employee_{$key}"] = $value;
            }
        }

        $this->callHook('beforeFill');

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        // Extract employee data (prefixed with employee_)
        $employeeData = [];
        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'employee_')) {
                $field = str_replace('employee_', '', $key);
                $employeeData[$field] = $value;
                unset($data[$key]);
            }
        }

        // Update the User record (Account Info)
        $record->update($data);

        // Update the Employee record (Personal Info)
        if ($record->employee && !empty($employeeData)) {
            // Remove readonly fields from update just in case
            unset($employeeData['nip']);
            unset($employeeData['jabatan']);
            unset($employeeData['status_kepegawaian']);

            $record->employee->update($employeeData);
        }

        return $record;
    }
}
