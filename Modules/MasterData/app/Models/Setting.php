<?php

namespace Modules\MasterData\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'app_name',
        'working_days',
        'office_start_time',
        'auto_alpha_time',
        'office_end_time',
        'office_latitude',
        'office_longitude',
        'office_radius',
        'late_tolerance',
    ];

    protected $casts = [
        'working_days' => 'array',
        'late_tolerance' => 'integer',
        'office_radius' => 'integer',
        'office_latitude' => 'float',
        'office_longitude' => 'float',
    ];

    /**
     * Get the single setting record.
     */
    public static function get(): self
    {
        return self::firstOrCreate([], [
            'app_name' => 'SIMPEG',
            'working_days' => [1, 2, 3, 4, 5], // Mon-Fri
            'office_start_time' => '07:00:00',
            'auto_alpha_time' => '11:00:00',
            'office_end_time' => '16:00:00',
            'office_latitude' => -6.2088, // Jakarta Default
            'office_longitude' => 106.8456,
            'office_radius' => 500,
            'late_tolerance' => 0,
        ]);
    }
}
