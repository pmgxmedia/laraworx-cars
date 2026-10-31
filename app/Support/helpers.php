<?php

use App\Models\Setting;
use Illuminate\Support\Number;

if (! function_exists('money')) {
    function money(float|int|string|null $amount, bool $withDecimals = false): string
    {
        $amount = (float) ($amount ?? 0);
        $currency = Setting::get('worxcars.currency', 'ZAR');

        if ($withDecimals) {
            return Number::currency($amount, in: $currency, locale: 'en_ZA');
        }

        return 'R'.number_format($amount, 0, ',', ' ');
    }
}

if (! function_exists('siteName')) {
    function siteName(): string
    {
        return (string) Setting::get('worxcars.site_name', 'WorxCars');
    }
}

if (! function_exists('appSetting')) {
    function appSetting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}