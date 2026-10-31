<?php

namespace App\Support;

use App\Models\Setting;
use App\Models\Vehicle;

class FinanceEstimator
{
    public static function estimate(Vehicle $vehicle, ?float $price = null): ?array
    {
        $price = (float) ($price ?? $vehicle->price);

        if ($price <= 0) {
            return null;
        }

        $term = (int) Setting::get('worxcars.finance_default_term_months', 72);
        $depositPercent = (float) Setting::get('worxcars.finance_default_deposit_percent', 10);
        $annualRate = (float) Setting::get('worxcars.finance_default_interest_rate', 12.5);

        $deposit = $price * ($depositPercent / 100);
        $principal = $price - $deposit;

        if ($principal <= 0 || $term <= 0) {
            return null;
        }

        $monthlyRate = $annualRate / 100 / 12;

        if ($monthlyRate <= 0) {
            $monthly = $principal / $term;
        } else {
            $monthly = $principal * ($monthlyRate * pow(1 + $monthlyRate, $term)) / (pow(1 + $monthlyRate, $term) - 1);
        }

        return [
            'monthly' => round($monthly, 2),
            'term' => $term,
            'deposit' => round($deposit, 2),
            'deposit_percent' => $depositPercent,
            'annual_rate' => $annualRate,
            'principal' => round($principal, 2),
            'total_payable' => round($monthly * $term, 2),
        ];
    }
}