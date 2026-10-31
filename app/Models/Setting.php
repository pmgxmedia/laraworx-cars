<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'group', 'type', 'value', 'is_public', 'description'];

    protected function casts(): array
    {
        return ['is_public' => 'boolean'];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = self::all()->firstWhere('key', $key)?->value;

        if ($value === null) {
            return $default;
        }

        $setting = self::all()->firstWhere('key', $key);

        return match ($setting->type) {
            'number' => (float) $value,
            'integer' => (int) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'string', bool $isPublic = false): void
    {
        $stringValue = match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => json_encode($value),
            default => (string) $value,
        };

        self::updateOrCreate(
            ['key' => $key],
            ['value' => $stringValue, 'group' => $group, 'type' => $type, 'is_public' => $isPublic]
        );
    }
}