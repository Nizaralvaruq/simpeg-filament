<?php

namespace Modules\Pegawai\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Resign extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'tanggal_resign' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(DataInduk::class, 'data_induk_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
