<?php

namespace Tests\Feature;

use App\Models\Commission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\CommissionService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        Setting::set('worxcars.min_reward', 1500, 'financial', 'number');
        Setting::set('worxcars.default_commission_percent', 2.5, 'financial', 'number');
        Setting::set('worxcars.reward_percent_of_commission', 40, 'financial', 'number');
        Setting::set('worxcars.max_reward', 25000, 'financial', 'number');
    }

    public function test_default_breakdown_computes_commission_reward_and_retained(): void
    {
        $vehicle = Vehicle::factory()->create(['price' => 500000, 'status' => Vehicle::STATUS_PUBLISHED]);

        $breakdown = app(CommissionService::class)->breakdown($vehicle);

        $expectedCommission = 500000 * 0.025; // 12500.00
        $expectedReward = min(max($expectedCommission * 0.4, 1500), 25000); // 5000.00

        $this->assertEqualsWithDelta($expectedCommission, $breakdown['commission_amount'], 0.01);
        $this->assertEqualsWithDelta($expectedReward, $breakdown['reward_amount'], 0.01);
        $this->assertEqualsWithDelta($expectedCommission - $expectedReward, $breakdown['retained_amount'], 0.01);
        $this->assertSame('default', $breakdown['source']);
    }

    public function test_reward_is_clamped_to_minimum(): void
    {
        $vehicle = Vehicle::factory()->create(['price' => 100000, 'status' => Vehicle::STATUS_PUBLISHED]);

        $breakdown = app(CommissionService::class)->breakdown($vehicle);

        $this->assertEquals(1500.0, $breakdown['reward_amount']);
        $this->assertEquals(2500.0, $breakdown['commission_amount']);
        $this->assertEquals(1000.0, $breakdown['retained_amount']);
    }

    public function test_reward_is_clamped_to_maximum(): void
    {
        $vehicle = Vehicle::factory()->create(['price' => 50000000, 'status' => Vehicle::STATUS_PUBLISHED]);

        $breakdown = app(CommissionService::class)->breakdown($vehicle);

        $this->assertEquals(25000.0, $breakdown['reward_amount']);
    }

    public function test_custom_commission_configuration_drives_breakdown(): void
    {
        $vehicle = Vehicle::factory()->create(['price' => 200000, 'status' => Vehicle::STATUS_PUBLISHED]);

        $service = app(CommissionService::class);
        $service->syncForVehicle($vehicle, [
            'commission_type' => 'percent',
            'commission_value' => 5,
            'reward_type' => 'percent',
            'reward_value' => 1.5,
            'reward_min' => null,
            'reward_max' => null,
        ]);

        $breakdown = $service->breakdown($vehicle);

        $this->assertEquals(10000.0, $breakdown['commission_amount']);
        $this->assertEquals(3000.0, $breakdown['reward_amount']);
        $this->assertEquals(7000.0, $breakdown['retained_amount']);
        $this->assertSame('commission', $breakdown['source']);
    }

    public function test_sync_for_vehicle_persists_computed_amounts(): void
    {
        $admin = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['price' => 300000, 'status' => Vehicle::STATUS_PUBLISHED]);

        $commission = app(CommissionService::class)->syncForVehicle($vehicle, [
            'commission_type' => 'percent',
            'commission_value' => 2.5,
            'reward_type' => 'commission_based',
            'reward_percent' => 40,
            'reward_min' => null,
            'reward_max' => null,
        ], null, $admin->id);

        $this->assertDatabaseHas('commissions', [
            'vehicle_id' => $vehicle->id,
            'recorded_by' => $admin->id,
        ]);

        $this->assertNotNull($commission->commission_amount);
        $this->assertNotNull($commission->reward_amount);
        $this->assertNotNull($commission->retained_amount);
        $this->assertGreaterThan(0, (float) $commission->reward_amount);
    }

    public function test_with_effective_reward_keeps_model_identity_and_appends_amount(): void
    {
        $vehicle = Vehicle::factory()->create([
            'price' => 400000,
            'status' => Vehicle::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $row = Vehicle::published()
            ->withEffectiveReward()
            ->first();

        $this->assertNotNull($row);
        $this->assertNotNull($row->id);
        $this->assertNotNull($row->slug);
        $this->assertEquals($vehicle->id, $row->id);
        $this->assertArrayHasKey('effective_reward_amount', $row->getAttributes());
        $this->assertEqualsWithDelta(4000.0, (float) $row->effective_reward_amount, 0.01);
    }

    public function test_commission_table_is_always_reward_less_than_commission(): void
    {
        $vehicle = Vehicle::factory()->create(['price' => 123456, 'status' => Vehicle::STATUS_PUBLISHED]);

        $service = app(CommissionService::class);
        $breakdown = $service->breakdown($vehicle);

        $this->assertGreaterThan(0, $breakdown['commission_amount']);
        $this->assertLessThanOrEqual($breakdown['commission_amount'], $breakdown['reward_amount']);

        $retained = round($breakdown['commission_amount'] - $breakdown['reward_amount'], 2);
        $this->assertEquals($retained, $breakdown['retained_amount']);
    }

    public function test_roles_and_permissions_give_super_admin_full_access(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', Role::SUPER_ADMIN)->first()->id);

        $this->assertTrue($admin->hasPermission('view_audit_logs'));
        $this->assertTrue($admin->isAdmin());
    }
}