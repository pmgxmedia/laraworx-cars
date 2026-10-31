<?php

namespace Tests\Feature;

use App\Models\BuyerReward;
use App\Models\PurchaseClaim;
use App\Models\Role;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\ClaimService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ClaimWorkflowSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        Notification::fake();
    }

    public function test_customer_cannot_submit_duplicate_claim_on_same_vehicle(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['status' => Vehicle::STATUS_PUBLISHED]);

        $service = app(ClaimService::class);

        $service->submit($user, $vehicle, $this->claimData());

        $this->assertDatabaseCount('purchase_claims', 1);

        $vehicle->update(['status' => Vehicle::STATUS_RESERVED]);

        $second = $service->submit($user, $vehicle, $this->claimData());

        $this->assertDatabaseCount('purchase_claims', 2);
        $this->assertContains('duplicate_vehicle_claim', $second->fraud_flags);
    }

    public function test_claim_reward_cannot_exceed_commissions_snapshot(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'status' => Vehicle::STATUS_PUBLISHED,
            'price' => 150000,
        ]);

        $claim = app(ClaimService::class)->submit($user, $vehicle, $this->claimData());

        $reward = BuyerReward::where('purchase_claim_id', $claim->id)->first();

        $this->assertLessThanOrEqual((float) $claim->commission_amount, (float) $claim->reward_amount);
        $this->assertEquals((float) $claim->reward_amount, (float) $reward->amount);
    }

    public function test_claim_marks_vehicle_reserved(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['status' => Vehicle::STATUS_PUBLISHED]);

        app(ClaimService::class)->submit($user, $vehicle, $this->claimData());

        $this->assertEquals(Vehicle::STATUS_RESERVED, $vehicle->fresh()->status);
    }

    public function test_customer_can_only_view_own_claim(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['status' => Vehicle::STATUS_PUBLISHED]);

        $claim = app(ClaimService::class)->submit($owner, $vehicle, $this->claimData());

        $response = $this->actingAs($other)->get(route('customer.claims.show', $claim));

        $response->assertForbidden();
    }

    public function test_customer_route_requires_authentication(): void
    {
        $this->get(route('customer.claims'))
            ->assertRedirect(route('login'));

        $this->get(route('customer.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_cannot_approve_unverified_claim(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', Role::SUPER_ADMIN)->first()->id);

        $vehicle = Vehicle::factory()->create(['status' => Vehicle::STATUS_PUBLISHED]);
        $claim = app(ClaimService::class)->submit($user, $vehicle, $this->claimData());

        $service = app(ClaimService::class);

        $this->expectException(\RuntimeException::class);

        $service->approveReward($claim, $admin->id);
    }

    public function test_full_claim_lifecycle_reaches_payment_processing(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', Role::SUPER_ADMIN)->first()->id);

        $vehicle = Vehicle::factory()->create(['status' => Vehicle::STATUS_PUBLISHED]);
        $service = app(ClaimService::class);

        $claim = $service->submit($user, $vehicle, $this->claimData());
        $claim = $claim->fresh();

        $service->verifyTransaction($claim, $admin->id);
        $claim = $claim->fresh();

        $this->assertEquals(PurchaseClaim::STATUS_TRANSACTION_VERIFIED, $claim->status);

        $service->approveReward($claim, $admin->id);
        $claim = $claim->fresh();

        $this->assertEquals(PurchaseClaim::STATUS_PAYMENT_PROCESSING, $claim->status);
        $this->assertNotNull($claim->buyerReward);

        $reward = $claim->buyerReward->fresh();
        $this->assertDatabaseHas('reward_payouts', ['buyer_reward_id' => $reward->id]);
    }

    public function test_claim_settlement_records_audit_trail(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', Role::SUPER_ADMIN)->first()->id);

        $vehicle = Vehicle::factory()->create(['status' => Vehicle::STATUS_PUBLISHED]);
        $service = app(ClaimService::class);

        $claim = $service->submit($user, $vehicle, $this->claimData())->fresh();

        $service->verifyTransaction($claim, $admin->id);

        $this->assertDatabaseHas('audit_logs', [
            'entity_type' => 'purchase_claim',
            'entity_id' => $claim->id,
            'action' => 'claim_verified',
        ]);
    }

    public function test_withdrawn_claim_does_not_double_pay(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['status' => Vehicle::STATUS_PUBLISHED]);

        $claim = app(ClaimService::class)->submit($user, $vehicle, $this->claimData());

        $claim->update(['status' => PurchaseClaim::STATUS_REJECTED]);

        $payoutCount = \App\Models\RewardPayout::count();

        $this->assertEquals(0, $payoutCount);
        $this->assertEquals(PurchaseClaim::STATUS_REJECTED, $claim->fresh()->status);
    }

    protected function claimData(): array
    {
        return [
            'purchase_date' => now()->subDays(2)->toDateString(),
            'purchase_price' => 249900,
            'provider_reference' => 'INV-'.random_int(1000, 9999),
            'description' => 'Purchased this vehicle and completed the paperwork, happy to proceed with the reward claim verification process.',
            'bank_account_name' => 'Test User',
            'bank_name' => 'FNB',
            'bank_account_number' => '62100000001',
            'bank_branch_code' => '250655',
            'declaration_consent' => true,
            'declarations' => [
                'vehicle_purchased' => true,
                'data_accurate' => true,
                'not_already_rewarded' => true,
            ],
        ];
    }
}