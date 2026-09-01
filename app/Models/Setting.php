<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key, with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set (upsert) a setting value by key.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * Get multiple settings with defaults in a single query.
     */
    public static function getMany(array $defaults): array
    {
        $keys = array_keys($defaults);
        $settings = static::whereIn('key', $keys)->pluck('value', 'key');

        $result = [];
        foreach ($defaults as $key => $default) {
            $value = $settings[$key] ?? $default;
            $result[$key] = is_numeric($value) ? (int) $value : $value;
        }

        return $result;
    }

    /**
     * Get all statutory and platform fee settings with defaults.
     */
    public static function getFees(): array
    {
        $defaults = [
            'fee_pistol_new' => 60000,
            'fee_pistol_renewal' => 20000,
            'fee_longgun_new' => 40000,
            'fee_longgun_renewal' => 10000,
            'fee_platform_new' => 850,
            'fee_platform_renewal' => 720,
            'fee_platform_late' => 250,
            'fine_t1_pistol' => 2000,
            'fine_t1_longgun' => 1000,
            'fine_t2_pistol' => 5000,
            'fine_t2_longgun' => 2500,
            'fine_t3_pistol' => 10000,
            'fine_t3_longgun' => 5000,
            // Dealer statutory fees (per license class)
            'dealer_fee_class_a_new' => 150000,
            'dealer_fee_class_a_renewal' => 75000,
            'dealer_fee_class_b_new' => 200000,
            'dealer_fee_class_b_renewal' => 100000,
            'dealer_fee_class_c_new' => 250000,
            'dealer_fee_class_c_renewal' => 125000,
            // Dealer platform charges
            'dealer_platform_new' => 2500,
            'dealer_platform_renewal' => 2500,
        ];

        return static::getMany($defaults);
    }
}
