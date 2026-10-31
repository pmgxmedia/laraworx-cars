<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Centralised, configurable financial rules for WorxCars.
 *
 * Read from the settings table so reward/commission configuration is
 * never hard-coded and can be changed from the administration panel.
 */
class FinancialRules
{
    public const KEY_MIN_REWARD = 'worxcars.min_reward';
    public const KEY_DEFAULT_COMMISSION_PERCENT = 'worxcars.default_commission_percent';
    public const KEY_REWARD_PERCENT_OF_COMMISSION = 'worxcars.reward_percent_of_commission';
    public const KEY_MAX_REWARD = 'worxcars.max_reward';

    public static function instance(): self
    {
        return new self;
    }

    public function minReward(): float
    {
        return (float) Setting::get(self::KEY_MIN_REWARD, 1500);
    }

    public function defaultCommissionPercent(): float
    {
        return (float) Setting::get(self::KEY_DEFAULT_COMMISSION_PERCENT, 2.5);
    }

    public function rewardPercentOfCommission(): float
    {
        return (float) Setting::get(self::KEY_REWARD_PERCENT_OF_COMMISSION, 40);
    }

    public function maxReward(): float
    {
        return (float) Setting::get(self::KEY_MAX_REWARD, 25000);
    }
}