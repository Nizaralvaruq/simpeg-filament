<?php

namespace Modules\PenilaianKinerja\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppraisalCategory extends Model
{
    protected $guarded = [];

    public function indicators(): HasMany
    {
        return $this->hasMany(AppraisalIndicator::class, 'category_id');
    }
}
