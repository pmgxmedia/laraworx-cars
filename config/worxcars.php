<?php

return [
    'min_reward' => (float) (env('WORXCARS_MIN_REWARD', 1500)),
    'default_commission_percent' => (float) env('WORXCARS_DEFAULT_COMMISSION_PERCENT', 2.5),
    'reward_percent_of_commission' => (float) env('WORXCARS_REWARD_PERCENT_OF_COMMISSION', 40),
    'max_reward' => (float) env('WORXCARS_MAX_REWARD', 25000),
    'payout_provider' => env('WORXCARS_PAYOUT_PROVIDER', 'manual'),
    'payout_mock_failure' => env('WORXCARS_PAYOUT_MOCK_FAILURE', false),
    'images_per_vehicle_max' => (int) env('WORXCARS_IMAGES_PER_VEHICLE_MAX', 20),
    'admin_prefix' => env('WORXCARS_ADMIN_PREFIX', 'admin'),
    'admin_email' => env('WORXCARS_ADMIN_EMAIL', 'admin@worxcars.local'),
    'admin_password' => env('WORXCARS_ADMIN_PASSWORD', 'worxcars-admin'),
];