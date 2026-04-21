<?php

namespace Modules\Akademik\Filament\Resources;

use Modules\Akademik\Models\Siswa;
use Modules\Akademik\Filament\Resources\SiswaResource\Pages;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Filament\Schemas\Schema;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    protected static ?int $navigationSort = 1;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-users';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Akademik';
    }

    public static function getModelLabel(): string
    {
        return 'Siswa';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Siswa';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Informasi Siswa')
                    ->description('Data identitas siswa. NIS akan diubah menjadi QR Code untuk keperluan scanner setoran.')
                    ->schema([
                        Forms\Components\TextInput::make('nis')
                            ->label('NIS (Nomor Induk Siswa)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('NIS ini akan menjadi isi dari QR Code siswa.'),
                        Forms\Components\TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap')
                            ->required(),
                        Forms\Components\TextInput::make('kelas')
                            ->label('Kelas')
                            ->nullable(),
                        Forms\Components\TextInput::make('nomor_wa_ortu')
                            ->label('Nomor WA Orang Tua')
                            ->tel()
                            ->nullable()
                            ->prefix('62')
                            ->helperText('Contoh: 81234567890. (Isi tanpa angka 0 di depan)'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Siswa Aktif')
                            ->default(true)
                            ->inline(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->copyable()
                    ->copyMessage('NIS disalin!'),
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas')
                    ->label('Kelas')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nomor_wa_ortu')
                    ->label('WA Orang Tua')
                    ->icon('heroicon-o-phone')
                    ->copyable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('setoran_count')
                    ->label('Total Setoran')
                    ->counts('setoranNgajis')
                    ->badge()
                    ->color('success')
                    ->sortable(),
            ])
            ->defaultSort('nama_lengkap')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Siswa')
                    ->trueLabel('Aktif')
                    ->falseLabel('Non-Aktif'),
            ])
            ->recordActions([
                \Filament\Actions\Action::make('lihat_qr')
                    ->label('QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->modalHeading(fn(Siswa $record) => 'QR Code — ' . $record->nama_lengkap)
                    ->modalContent(function (Siswa $record) {
                        // PNG output — more reliable for physical QR scanners
                        // SVG uses path rendering that can blur edges and confuse cameras
                        $options = new QROptions([
                            'version'     => 0,   // auto-detect smallest version
                            'outputType'  => QRCode::OUTPUT_IMAGE_PNG,
                            'eccLevel'    => QRCode::ECC_M,  // 15% error recovery
                            'scale'       => 12,  // 12px per module = high quality
                            'imageBase64' => true,
                        ]);
                        $dataUri = (new QRCode($options))->render($record->nis);
                        $nis   = e($record->nis);
                        $nama  = e($record->nama_lengkap);
                        $kelas = e($record->kelas ?? '-');
                        $html = <<<HTML
                        <div style="display:flex;flex-direction:column;align-items:center;padding:1.5rem;">
                            <div style="background:#fff;padding:1.25rem;border-radius:16px;border:2px solid #e5e7eb;box-shadow:0 4px 20px rgba(0,0,0,.1);">
                                <img src="{$dataUri}"
                                     alt="QR {$nis}"
                                     style="width:260px;height:260px;display:block;image-rendering:pixelated;" />
                            </div>
                            <div style="margin-top:1.25rem;text-align:center;width:100%;">
                                <p style="font-size:1.2rem;font-weight:800;color:#111827;margin:0;">{$nama}</p>
                                <p style="font-size:.9rem;color:#6b7280;margin:.3rem 0 0;">NIS: <strong style="color:#2563eb;font-family:monospace;font-size:1rem;">{$nis}</strong></p>
                                <p style="font-size:.8rem;color:#9ca3af;margin:.2rem 0 0;">Kelas {$kelas}</p>
                            </div>
                            <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid #f3f4f6;width:100%;text-align:center;">
                                <p style="font-size:.75rem;color:#9ca3af;margin:0 0 .75rem;">📖 Scan QR ini di halaman <strong>Scan &amp; Input Setoran</strong></p>
                                <div style="display:flex;gap:.75rem;justify-content:center;">
                                    <a href="{$dataUri}" download="QR-{$nis}.png"
                                       style="padding:.6rem 1.4rem;background:#2563eb;color:#fff;border-radius:8px;font-size:.8rem;font-weight:700;text-decoration:none;">
                                       ⬇ Download PNG
                                    </a>
                                    <button onclick="window.print()"
                                            style="padding:.6rem 1.4rem;background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;font-size:.8rem;font-weight:700;cursor:pointer;">
                                       🖨 Print
                                    </button>
                                </div>
                            </div>
                        </div>
                        HTML;
                        return new HtmlString($html);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\CreateAction::make(),
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
            'index'  => Pages\ListSiswas::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'edit'   => Pages\EditSiswa::route('/{record}/edit'),
            'view'   => Pages\ViewSiswa::route('/{record}'),
        ];
    }
}
