<?php

namespace Modules\Kepegawaian\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Modules\Kepegawaian\Filament\Resources\LeaveRequestResource\Pages;
use Modules\Kepegawaian\Models\LeaveRequest;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;
    protected static ?int $navigationSort = 10;

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-calendar-days';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Kepegawaian';
    }

    public static function getModelLabel(): string
    {
        return 'Pengajuan Cuti';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pengajuan Cuti';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Super Admin & Admin HR: View ALL
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        if ($user->hasRole('kepala_sekolah') || $user->hasRole('koor_jenjang')) {
            if ($user->employee && $user->employee->units->isNotEmpty()) {
                $unitIds = $user->employee->units->pluck('id');
                return $query->whereHas('employee.units', function ($q) use ($unitIds) {
                    $q->whereIn('units.id', $unitIds);
                });
            }

            return $query->whereRaw('1=0');
        }

        // Staff: View Own Requests
        if ($user->hasRole('staff')) {
            if ($user->employee) {
                return $query->where('data_induk_id', $user->employee->id);
            }
            return $query->whereRaw('1=0');
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('Detail Cuti')
                ->schema([

                    Forms\Components\Select::make('data_induk_id')
                        ->label('Nama Pegawai')
                        ->relationship('employee', 'nama') 
                        ->searchable()
                        ->preload()
                        ->required()
                        ->visible(fn () => auth()->user()->hasRole('super_admin'))
                        ->columnSpanFull(),

                    Forms\Components\Hidden::make('data_induk_id')
                        ->default(fn () => auth()->user()->employee?->id)
                        ->visible(fn () => ! auth()->user()->hasRole('super_admin')),

                    \Filament\Schemas\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\DatePicker::make('start_date')
                                ->label('Mulai Tanggal')
                                ->required()
                                ->minDate(now()),
                            Forms\Components\DatePicker::make('end_date')
                                ->label('Sampai Tanggal')
                                ->required()
                                ->afterOrEqual('start_date'),
                        ]),

                    Forms\Components\Textarea::make('reason')
                        ->label('Alasan Cuti')
                        ->required()
                        ->columnSpanFull(),
                    
                    Forms\Components\FileUpload::make('upload_file')
                        ->label('Upload Bukti (PDF/JPG/PNG)')
                        ->directory('upload_file')
                        ->disk('public')
                        ->visibility('public')
                        ->preserveFilenames()
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                        ->maxSize(2048)
                        ->required(fn () => auth()->user()->hasRole('staff'))
                        ->visible(fn () => auth()->user()->hasAnyRole(['staff', 'super_admin']))
                        ->columnSpanFull(),

                    Forms\Components\Hidden::make('status')
                        ->default('pending')
                        ->dehydrated(true)
                        ->visible(fn () => ! auth()->user()->hasAnyRole(['super_admin', 'admin'])),
                ]),

            \Filament\Schemas\Components\Section::make('Persetujuan')
                ->visible(fn () => auth()->user()->hasAnyRole(['super_admin', 'admin']))
                ->schema([
                    Forms\Components\Select::make('status')
                        ->options([
                            'pending' => 'Menunggu',
                            'approved' => 'Disetujui',
                            'rejected' => 'Ditolak',
                        ])
                        ->required()
                        ->default('pending')
                        ->live(), // penting biar note ikut refresh

                    Forms\Components\Textarea::make('note')
                        ->label('Catatan / Alasan Penolakan')
                        ->visible(fn ($get) => $get('status') === 'rejected')
                        ->required(fn ($get) => $get('status') === 'rejected'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.nama')
                    ->label('Nama Pegawai')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->label('Mulai'),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->label('Selesai'),
                Tables\Columns\TextColumn::make('lama_cuti')
                    ->label('Lama Cuti')
                    ->state(fn ($record) =>
                        \Carbon\Carbon::parse($record->start_date)
                            ->diffInDays(\Carbon\Carbon::parse($record->end_date)) + 1 . ' hari'
                    ),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Alasan Cuti')
                    ->searchable(),
                Tables\Columns\TextColumn::make('upload_file')
                    ->label('Bukti')
                    ->formatStateUsing(fn ($state) => $state ? 'Lihat/Download' : '-')
                    ->url(fn ($record) => $record->upload_file ? asset('storage/' . $record->upload_file) : null, true)
                    ->openUrlInNewTab()
                    ->badge()
                    ->color(fn ($record) => $record->upload_file ? 'info' : 'gray'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('note')
                    ->label('Catatan')
                    ->limit(40)
                    ->wrap()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('keterangan_kembali')
                    ->label('Status Kembali')
                    ->badge()
                    ->formatStateUsing(function ($record) {
                        if ($record->status !== 'approved') {
                            return '-';
                        }
                        return $record->keterangan_kembali ?? 'belum kembali';
                    })
                    ->color(fn ($state) => match ($state) {
                        'belum kembali' => 'warning',
                        'sudah kembali' => 'success',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('approve')
                        ->label('Setujui')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn ($record) =>
                            auth()->user()->hasAnyRole(['super_admin', 'admin'])
                            && $record->status === 'pending'
                        )
                        ->action(function ($record) {
                            // 1. UPDATE CUTI
                            $record->update([
                                'status' => 'approved',
                                'note' => 'OK',
                                'approved_by' => auth()->id(),
                                'keterangan_kembali' => 'belum kembali',
                            ]);
                            // 2. UPDATE DATA INDUK → CUTI
                            if ($record->employee) {
                                $record->employee->update([
                                    'status' => 'cuti',
                                    'keterangan' => $record->reason,
                                ]);
                            }
                        }),

                    Action::make('reject')
                        ->label('Tolak')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->form([
                            Forms\Components\Textarea::make('note')
                                ->label('Catatan')
                                ->required(),
                        ])
                        ->visible(fn ($record) =>
                            auth()->user()->hasAnyRole(['super_admin', 'admin'])
                            && $record->status === 'pending'
                        )
                        ->action(fn ($record, array $data) => $record->update([
                            'status' => 'rejected',
                            'note' => $data['note'],
                            'approved_by' => auth()->id(),
                        ])),

                    Action::make('kembali')
                        ->label('Sudah Kembali')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn ($record) =>
                            auth()->user()->hasRole('super_admin')
                            && $record->status === 'approved'
                            && in_array($record->keterangan_kembali, [null, 'belum kembali'])
                        )
                        ->action(function ($record) {
                            // 1. UPDATE CUTI
                            $record->update([
                                'keterangan_kembali' => 'sudah kembali',
                            ]);
                            // 2. UPDATE DATA INDUK → AKTIF
                            if ($record->employee) {
                                $record->employee->update([
                                    'status' => 'aktif',
                                    'keterangan' => null,
                                ]);
                            }
                        }),

                    EditAction::make()
                        ->visible(function ($record) {
                            $user = auth()->user();

                            if ($user->hasAnyRole(['super_admin', 'admin'])) {
                                return true;
                            }

                            return $user->hasRole('staff')
                                && $user->employee
                                && $record->data_induk_id === $user->employee->id
                                && $record->status === 'pending';
                        }),

                ])
                ->icon('heroicon-o-ellipsis-vertical'),
            ])
            ->actionsColumnLabel('Aksi')
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveRequests::route('/'),
            'create' => Pages\CreateLeaveRequest::route('/create'),
            'edit' => Pages\EditLeaveRequest::route('/{record}/edit'),
        ];
    }
}
