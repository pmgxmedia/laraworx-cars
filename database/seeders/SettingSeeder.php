<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['worxcars.site_name', 'general', 'WorxCars', 'string', true, 'Platform name'],
            ['worxcars.tagline', 'general', 'BUY YOUR CAR. GET PAID.', 'string', true, 'Primary selling message'],
            ['worxcars.supporting_message', 'general', 'Find your next vehicle from trusted sellers and receive a WorxCars Buyer Reward when your qualifying purchase is successfully completed.', 'string', true, 'Supporting message'],
            ['worxcars.contact_email', 'general', 'support@worxcars.co.za', 'string', true, 'Public contact email'],
            ['worxcars.contact_phone', 'general', '010 001 0000', 'string', true, 'Public contact phone'],
            ['worxcars.currency', 'general', 'ZAR', 'string', true, 'Primary currency'],
            // Financial
            ['worxcars.min_reward', 'financial', '1500', 'number', true, 'Minimum advertised buyer reward'],
            ['worxcars.default_commission_percent', 'financial', '2.5', 'number', false, 'Default WorxCars commission as a percentage of the vehicle price'],
            ['worxcars.reward_percent_of_commission', 'financial', '40', 'number', false, 'Percentage of commission allocated to the customer reward by default'],
            ['worxcars.max_reward', 'financial', '25000', 'number', false, 'Maximum buyer reward per qualifying purchase'],
            ['worxcars.fixed_commission', 'financial', '0', 'number', false, 'Optional fixed commission override'],
            ['worxcars.reward_validity_days', 'financial', '90', 'number', false, 'Days a reward remains valid after claim approval'],
            // Platform
            ['worxcars.vehicle_approval_required', 'platform', true, 'boolean', false, 'Require administrative approval before vehicles go live'],
            ['worxcars.dealership_approval_required', 'platform', true, 'boolean', false, 'Require administrative approval for dealerships'],
            ['worxcars.private_seller_approval_required', 'platform', true, 'boolean', false, 'Require administrative approval for private sellers'],
            ['worxcars.reward_verification_required', 'platform', true, 'boolean', false, 'Require full verification before any reward is paid'],
            ['worxcars.images_per_vehicle_max', 'platform', '20', 'integer', false, 'Maximum images per vehicle listing'],
            // Financing assumptions (estimates shown publicly)
            ['worxcars.finance_default_term_months', 'finance', '72', 'integer', true, 'Default finance term used for the monthly estimate'],
            ['worxcars.finance_default_deposit_percent', 'finance', '10', 'number', true, 'Default deposit percentage used for the monthly estimate'],
            ['worxcars.finance_default_interest_rate', 'finance', '12.5', 'number', true, 'Default interest rate assumption used for the monthly estimate'],
            // SEO
            ['worxcars.seo_default_description', 'seo', 'Buy your next vehicle from trusted South African sellers and get paid with a WorxCars Buyer Reward on qualifying purchases.', 'string', true, 'Default meta description'],
        ];

        foreach ($settings as [$key, $group, $value, $type, $public, $description]) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value, 'group' => $group, 'type' => $type, 'is_public' => $public, 'description' => $description]
            );
        }
    }
}