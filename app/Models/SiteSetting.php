<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SiteSetting extends Model
{
    use HasFactory;

    protected $primaryKey = 'key';
    public $incrementing = false;

    protected $fillable = [
        'key',
        'value',
        'default_value',
        'description',
        'type',
        'data',
    ];

    protected $casts = [
        'value' => 'json',
        'default_value' => 'json',
        'data' => 'json',
    ];

    public function getCurrentValueAttribute()
    {
        return $this->value ?? $this->default_value;
    }

    public static function get(string $key, $default = null)
    {
        $setting = static::find($key);
        return $setting ? ($setting->value ?? $setting->default_value) : $default;
    }

    public static function initializeDefaults(): void
    {
        $defaultSettings = [
            'nav_type' => [
                'value' => null,
                'default_value' => 'sidebar',
                'description' => 'Determines if the main navigation is a sidebar or a top bar.',
                'type' => 'enum',
                'options' => ['sidebar', 'topbar'],
            ],
            'homepage_layout' => [
                'value' => null,
                'default_value' => 'hero',
                'description' => 'Layout style for the homepage.',
                'type' => 'enum',
                'options' => ['hero', 'minimal'],
            ],
            'allow_registration' => [
                'value' => null,
                'default_value' => true,
                'description' => 'Enable or disable new user registrations.',
                'type' => 'boolean',
            ],
        ];

        foreach ($defaultSettings as $key => $data) {
            static::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $data['value'],
                    'default_value' => $data['default_value'],
                    'description' => $data['description'],
                    'type' => $data['type'],
                    'data' => ($data['type'] === 'enum' && isset($data['options'])) ? ['options' => $data['options']] : null,
                ]
            );
        }
    }
}