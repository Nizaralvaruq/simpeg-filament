<?php

namespace Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Akademik\Database\Factories\SetoranNgajiFactory;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Services\FonnteService;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class SetoranNgaji extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted()
    {
        static::created(function ($setoran) {
            $siswa = $setoran->siswa;
            
            if ($siswa && $siswa->nomor_wa_ortu) { 
                $fonnteService = app(FonnteService::class);
                
                $message = "Assalamualaikum, menginfokan setoran Ananda *{$siswa->nama_lengkap}* pada tanggal *" . Carbon::parse($setoran->tanggal_setoran)->format('d/m/Y') . "*:\n\n";
                $message .= "📚 *Jenis:* {$setoran->jenis_setoran}\n";
                $message .= "📖 *Materi:* {$setoran->nama_materi}\n";
                if ($setoran->ayat_halaman) {
                    $message .= "📄 *Ayat/Halaman:* {$setoran->ayat_halaman}\n";
                }
                $message .= "📝 *Nilai:* *{$setoran->predikat_nilai}*\n";
                
                if ($setoran->catatan_guru) {
                    $message .= "💬 *Catatan:* {$setoran->catatan_guru}\n";
                }
                
                $message .= "\nTerima kasih.";

                $response = $fonnteService->sendMessage($siswa->nomor_wa_ortu, $message);

                if ($response['status']) {
                    Notification::make()
                        ->title('Notifikasi WA Terkirim')
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('Gagal Mengirim WA')
                        ->body($response['message'])
                        ->warning()
                        ->send();
                }
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'siswa_id',
        'guru_id',
        'tanggal_setoran',
        'jenis_setoran',
        'nama_materi',
        'ayat_halaman',
        'predikat_nilai',
        'catatan_guru',
        'status_notifikasi',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
}
