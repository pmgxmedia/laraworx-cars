<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            Role::SUPER_ADMIN => ['label' => 'Super Administrator', 'description' => 'Full unrestricted platform control.'],
            Role::OPERATIONS_ADMIN => ['label' => 'Operations Administrator', 'description' => 'Manages vehicles, sellers, dealerships, enquiries, claims and transactions.'],
            Role::FINANCE_ADMIN => ['label' => 'Finance Administrator', 'description' => 'Manages commissions, rewards, payouts, financial reports and payment statuses.'],
            Role::CONTENT_ADMIN => ['label' => 'Content Administrator', 'description' => 'Manages homepage, banners, FAQs, blog and campaign content.'],
            Role::SUPPORT_ADMIN => ['label' => 'Support Administrator', 'description' => 'Manages customers, enquiries, support tickets and claims.'],
            Role::DEALERSHIP => ['label' => 'Dealership', 'description' => 'Dealership user account.'],
            Role::PRIVATE_SELLER => ['label' => 'Private Seller', 'description' => 'Approved private seller account.'],
            Role::CUSTOMER => ['label' => 'Customer', 'description' => 'Customer account.'],
        ];

        foreach ($roles as $name => $attributes) {
            Role::firstOrCreate(['name' => $name], $attributes);
        }

        $permissions = [
            // vehicles
            'view_vehicles' => ['Vehicles', 'View vehicles'],
            'create_vehicles' => ['Vehicles', 'Create vehicles'],
            'edit_vehicles' => ['Vehicles', 'Edit vehicles'],
            'delete_vehicles' => ['Vehicles', 'Delete vehicles'],
            'approve_vehicles' => ['Vehicles', 'Approve vehicle listings'],
            'feature_vehicles' => ['Vehicles', 'Feature vehicles'],
            'manage_vehicle_images' => ['Vehicles', 'Manage vehicle images'],
            // dealerships
            'view_dealerships' => ['Dealerships', 'View dealerships'],
            'approve_dealerships' => ['Dealerships', 'Approve dealerships'],
            'manage_dealerships' => ['Dealerships', 'Manage dealerships'],
            'suspend_dealerships' => ['Dealerships', 'Suspend dealerships'],
            // sellers
            'view_sellers' => ['Sellers', 'View private sellers'],
            'approve_sellers' => ['Sellers', 'Approve private sellers'],
            'manage_sellers' => ['Sellers', 'Manage private sellers'],
            // campaigns
            'view_campaigns' => ['Campaigns', 'View campaigns'],
            'manage_campaigns' => ['Campaigns', 'Manage campaigns'],
            'publish_campaigns' => ['Campaigns', 'Publish campaigns'],
            // commissions & rewards
            'view_commissions' => ['Finance', 'View commissions'],
            'manage_commissions' => ['Finance', 'Manage commissions'],
            'view_rewards' => ['Finance', 'View buyer rewards'],
            'approve_rewards' => ['Finance', 'Approve buyer rewards'],
            'manage_rewards' => ['Finance', 'Manage buyer rewards'],
            'manage_payouts' => ['Finance', 'Manage reward payouts'],
            'view_finance' => ['Finance', 'View financial dashboard'],
            // claims
            'view_claims' => ['Claims', 'View purchase claims'],
            'verify_claims' => ['Claims', 'Verify claims and transactions'],
            'approve_claims' => ['Claims', 'Approve claim rewards'],
            'reject_claims' => ['Claims', 'Reject claims'],
            // enquiries
            'view_enquiries' => ['Enquiries', 'View enquiries'],
            'manage_enquiries' => ['Enquiries', 'Manage enquiries'],
            // users
            'view_users' => ['Users', 'View users'],
            'manage_users' => ['Users', 'Manage users'],
            'manage_roles' => ['Users', 'Manage roles'],
            'manage_permissions' => ['Users', 'Manage permissions'],
            // content
            'manage_pages' => ['Content', 'Manage pages'],
            'manage_faqs' => ['Content', 'Manage FAQs'],
            'manage_banners' => ['Content', 'Manage banners'],
            'manage_cms' => ['Content', 'Manage content'],
            'manage_campaign_content' => ['Content', 'Manage campaign content'],
            // settings & reports
            'manage_settings' => ['Settings', 'Manage system settings'],
            'view_reports' => ['Reports', 'View reports'],
            'export_data' => ['Reports', 'Export data'],
            'view_audit_logs' => ['Security', 'View audit logs'],
        ];

        foreach ($permissions as $name => [$group, $label]) {
            Permission::firstOrCreate(['name' => $name], ['label' => $label, 'group' => $group]);
        }

        $all = Permission::pluck('name')->all();
        Role::where('name', Role::SUPER_ADMIN)->firstOrFail()->permissions()->sync(Permission::all()->pluck('id'));

        $map = [
            Role::OPERATIONS_ADMIN => ['view_vehicles', 'create_vehicles', 'edit_vehicles', 'delete_vehicles', 'approve_vehicles', 'feature_vehicles', 'manage_vehicle_images', 'view_dealerships', 'approve_dealerships', 'manage_dealerships', 'view_sellers', 'approve_sellers', 'manage_sellers', 'view_campaigns', 'view_claims', 'verify_claims', 'reject_claims', 'view_enquiries', 'manage_enquiries', 'view_users', 'view_reports'],
            Role::FINANCE_ADMIN => ['view_commissions', 'manage_commissions', 'view_rewards', 'approve_rewards', 'manage_rewards', 'manage_payouts', 'view_finance', 'view_claims', 'verify_claims', 'approve_claims', 'reject_claims', 'view_vehicles', 'view_reports', 'export_data', 'view_audit_logs'],
            Role::CONTENT_ADMIN => ['manage_pages', 'manage_faqs', 'manage_banners', 'manage_cms', 'manage_campaign_content', 'view_vehicles', 'view_campaigns'],
            Role::SUPPORT_ADMIN => ['view_users', 'manage_users', 'view_enquiries', 'manage_enquiries', 'view_claims', 'view_rewards', 'view_vehicles'],
            Role::DEALERSHIP => ['view_vehicles', 'create_vehicles', 'edit_vehicles', 'manage_vehicle_images', 'view_enquiries', 'manage_enquiries', 'view_claims'],
            Role::PRIVATE_SELLER => ['view_vehicles', 'create_vehicles', 'edit_vehicles'],
            Role::CUSTOMER => [],
        ];

        foreach ($map as $role => $perms) {
            Role::where('name', $role)->firstOrFail()
                ->permissions()
                ->sync(Permission::whereIn('name', $perms)->pluck('id'));
        }
    }
}