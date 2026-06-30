<?php

namespace Modules\MasterData\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @property string $app_name
 * @property array|null $working_days
 * @property string|null $office_start_time
 * @property string|null $auto_alpha_time
 * @property string|null $office_end_time
 * @property float $office_latitude
 * @property float $office_longitude
 * @property int $office_radius
 * @property int|null $late_tolerance
 */
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

    public const CACHE_KEY = 'app_settings';
    public const CACHE_TTL = 300;

    protected static function booted(): void
    {
        static::created(function (Setting $setting) {
            Cache::forget(self::CACHE_KEY);
        });

        static::updated(function (Setting $setting) {
            Cache::forget(self::CACHE_KEY);
        });

        static::deleted(function (Setting $setting) {
            Cache::forget(self::CACHE_KEY);
        });
    }

    /**
     * Get the single setting record with caching.
     * Cache is invalidated when Setting is created/updated/deleted.
     */
    public static function get(): self
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return self::firstOrCreate([], [
                'app_name' => 'SIMPEG',
                'working_days' => [1, 2, 3, 4, 5],
                'office_start_time' => '07:00:00',
                'auto_alpha_time' => '11:00:00',
                'office_end_time' => '16:00:00',
                'office_latitude' => -6.2088,
                'office_longitude' => 106.8456,
                'office_radius' => 500,
                'late_tolerance' => 0,
            ]);
        });
    }
}
