<?php

namespace Modules\Retirement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Modules\Kepegawaian\Models\DataInduk;

class Retirement extends Model
{
    use HasFactory;

    protected $table = 'retirements';

    protected $guarded = [];

    protected $casts = [
        'tanggal_pensiun' => 'date',
        'is_khidmah' => 'boolean',
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
