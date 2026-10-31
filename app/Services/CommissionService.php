<?php

namespace App\Services;

use App\Models\CampaignVehicle;
use App\Models\Commission;
use App\Models\Vehicle;
use App\Support\FinancialRules;

class CommissionService
{
    /**
     * Build a financial breakdown for a vehicle based on its active commission/campaign configuration.
     *
     * @return array{commission_amount: float, reward_amount: float, retained_amount: float, config: array, source: string}
     */
    public function breakdown(Vehicle $vehicle, ?float $salePrice = null): array
    {
        $salePrice ??= (float) $vehicle->price;
        $campaignRule = $vehicle->campaignRule();
        $commission = $vehicle->activeCommission();

        $config = $this->resolveConfig($vehicle, $campaignRule, $commission);

        $commissionAmount = $this->computeCommission($config, $salePrice);
        $rewardAmount = $this->computeReward($config, $commissionAmount, $salePrice);

        return [
            'commission_amount' => round($commissionAmount, 2),
            'reward_amount' => round($rewardAmount, 2),
            'retained_amount' => round($commissionAmount - $rewardAmount, 2),
            'config' => $config,
            'source' => $campaignRule ? 'campaign' : ($commission ? 'commission' : 'default'),
        ];
    }

    protected function resolveConfig(Vehicle $vehicle, ?CampaignVehicle $rule, ?Commission $commission): array
    {
        $rules = FinancialRules::instance();

        if ($rule) {
            return [
                'commission_type' => $rule->commission_type ?? 'fixed',
                'commission_value' => (float) ($rule->commission_value ?? 0),
                'reward_type' => $rule->reward_type ?? 'fixed',
                'reward_value' => (float) ($rule->reward_value ?? 0),
                'reward_percent' => (float) ($rule->reward_percent ?? 0),
                'reward_min' => $rule->reward_min !== null ? (float) $rule->reward_min : $rules->minReward(),
                'reward_max' => $rule->reward_max !== null ? (float) $rule->reward_max : $rules->maxReward(),
            ];
        }

        if ($commission) {
            return [
                'commission_type' => $commission->commission_type,
                'commission_value' => (float) $commission->commission_value,
                'reward_type' => $commission->reward_type,
                'reward_value' => (float) $commission->reward_value,
                'reward_percent' => (float) $commission->reward_percent,
                'reward_min' => $commission->reward_min !== null ? (float) $commission->reward_min : $rules->minReward(),
                'reward_max' => $commission->reward_max !== null ? (float) $commission->reward_max : $rules->maxReward(),
            ];
        }

        return [
            'commission_type' => 'percent',
            'commission_value' => (float) $rules->defaultCommissionPercent(),
            'reward_type' => 'commission_based',
            'reward_value' => 0,
            'reward_percent' => (float) $rules->rewardPercentOfCommission(),
            'reward_min' => $rules->minReward(),
            'reward_max' => $rules->maxReward(),
        ];
    }

    public function computeCommission(array $config, float $salePrice): float
    {
        if (($config['commission_type'] ?? 'percent') === 'percent') {
            return ($config['commission_value'] / 100) * $salePrice;
        }

        return (float) ($config['commission_value'] ?? 0);
    }

    public function computeReward(array $config, float $commissionAmount, float $salePrice): float
    {
        $rewardType = $config['reward_type'] ?? 'fixed';

        $reward = match ($rewardType) {
            'fixed' => (float) ($config['reward_value'] ?? 0),
            'percent' => (($config['reward_value'] ?? 0) / 100) * $salePrice,
            'commission_based' => (($config['reward_percent'] ?? 0) / 100) * $commissionAmount,
            default => 0,
        };

        $min = (float) ($config['reward_min'] ?? 0);
        $max = (float) ($config['reward_max'] ?? 0);

        if ($max > 0 && $reward > $max) {
            $reward = $max;
        }

        if ($reward > 0 && $min > 0 && $reward < $min) {
            $reward = $min;
        }

        return round($reward, 2);
    }

    /**
     * Persist the commission/reward configuration for a vehicle.
     */
    public function syncForVehicle(Vehicle $vehicle, array $config, ?int $campaignId = null, ?int $userId = null): Commission
    {
        $payload = [
            'vehicle_id' => $vehicle->id,
            'dealership_id' => $vehicle->dealership_id,
            'seller_type' => $vehicle->seller_type,
            'campaign_id' => $campaignId,
            'commission_type' => $config['commission_type'] ?? 'fixed',
            'commission_value' => $config['commission_value'] ?? 0,
            'reward_type' => $config['reward_type'] ?? 'fixed',
            'reward_value' => $config['reward_value'] ?? 0,
            'reward_percent' => $config['reward_percent'] ?? 0,
            'reward_min' => $config['reward_min'] ?? null,
            'reward_max' => $config['reward_max'] ?? null,
            'recorded_by' => $userId,
        ];

        $breakdown = $this->breakdown($vehicle);

        $payload['commission_amount'] = $breakdown['commission_amount'];
        $payload['reward_amount'] = $breakdown['reward_amount'];
        $payload['retained_amount'] = $breakdown['retained_amount'];

        return Commission::updateOrCreate(
            ['vehicle_id' => $vehicle->id, 'campaign_id' => $campaignId],
            $payload
        );
    }
}