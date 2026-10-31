<?php

namespace Database\Seeders;

use App\Models\BuyerEnquiry;
use App\Models\BuyerReward;
use App\Models\CampaignVehicle;
use App\Models\Dealership;
use App\Models\DealershipUser;
use App\Models\PrivateSeller;
use App\Models\PurchaseClaim;
use App\Models\PurchaseClaimDocument;
use App\Models\RewardPayout;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleCampaign;
use App\Models\VehicleImage;
use App\Services\CommissionService;
use App\Support\PlaceholderImageService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public const DEMO_PASSWORD = 'password';

    protected CommissionService $commissions;

    protected PlaceholderImageService $images;

    protected ?int $adminId = null;

    public function __construct()
    {
        $this->commissions = new CommissionService;
        $this->images = new PlaceholderImageService;
    }

    public function run(): void
    {
        $adminPassword = config('worxcars.admin_password', 'worxcars-admin');
        if (strlen($adminPassword) < 8) {
            $adminPassword = 'worxcars-admin';
        }

        $this->createAdmins($adminPassword);
        $this->adminId = User::where('email', 'admin@worxcars.local')->value('id');
        $this->createCustomers();
        $dealerships = $this->createDealerships();
        $sellers = $this->createPrivateSellers();
        $campaigns = $this->createCampaigns();
        $vehicles = $this->createVehicles($dealerships, $sellers, $campaigns);

        $this->createEnquiries($vehicles);
        $this->createClaimsAndRewards($vehicles);
        $this->assets();
    }

    protected function createAdmins(string $password): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@worxcars.local'],
            [
                'name' => 'Platform', 'surname' => 'Administrator',
                'email_verified_at' => now(), 'password' => $password, 'is_active' => true,
            ]
        );
        $this->assignRole($admin, Role::SUPER_ADMIN);

        $roles = [
            ['operations@worxcars.local', 'Operations', 'Administrator', Role::OPERATIONS_ADMIN],
            ['finance@worxcars.local', 'Finance', 'Administrator', Role::FINANCE_ADMIN],
            ['content@worxcars.local', 'Content', 'Administrator', Role::CONTENT_ADMIN],
            ['support@worxcars.local', 'Support', 'Administrator', Role::SUPPORT_ADMIN],
        ];

        foreach ($roles as [$email, $name, $surname, $role]) {
            $user = User::firstOrCreate(
                ['email' => $email],
                ['name' => $name, 'surname' => $surname, 'email_verified_at' => now(), 'password' => $password, 'is_active' => true]
            );
            $this->assignRole($user, $role);
        }
    }

    protected function createCustomers(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            $email = "customer{$i}@example.com";
            $user = User::where('email', $email)->first();

            if (! $user) {
                $user = User::factory()->create(['email' => $email]);
            }

            $this->assignRole($user, Role::CUSTOMER);
        }
    }

    protected function createDealerships(): array
    {
        $dealershipData = [
            ['Sandton Motor Group', 'Gauteng', 'Sandton', 'Mpumelelo Nkosi', 1],
            ['Cape Town Car Emporium', 'Western Cape', 'Cape Town', 'Susan Jacobs', 2],
            ['Durban Auto Excellence', 'KwaZulu-Natal', 'Durban', 'Ravi Pillay', 3],
            ['Pretoria Prestige Motors', 'Gauteng', 'Pretoria', 'Thomas Mokoena', 4],
            ['Highveld Truck & Fleet Centre', 'Gauteng', 'Johannesburg', 'Lerato Molefe', 5],
        ];

        $dealerships = [];
        foreach ($dealershipData as $i => [$name, $province, $city, $contact, $userIndex]) {
            $dealership = Dealership::where('business_name', $name)->first();

            if (! $dealership) {
                $dealership = Dealership::factory()->create([
                    'business_name' => $name,
                    'province' => $province,
                    'city' => $city,
                    'contact_person_name' => $contact,
                ]);
            }

            $user = User::firstOrCreate(
                ['email' => "dealer{$i}@example.com"],
                [
                    'name' => $contact, 'surname' => 'Sales', 'email_verified_at' => now(),
                    'phone' => '0'.random_int(7, 9).random_int(10000000, 99999999),
                    'city' => $city, 'province' => $province, 'is_active' => true, 'password' => self::DEMO_PASSWORD,
                ]
            );
            $this->assignRole($user, Role::DEALERSHIP);
            DealershipUser::firstOrCreate(
                ['dealership_id' => $dealership->id, 'user_id' => $user->id],
                ['role_title' => 'Manager', 'is_primary' => true]
            );

            $this->comment("- Dealership: {$name} (".$user->email.' / '.self::DEMO_PASSWORD.')');
            $dealerships[$dealership->id] = $dealership;
        }

        return $dealerships;
    }

    protected function createPrivateSellers(): array
    {
        $sellerData = [
            ['Thabo Mkhize', 'tbuilder@example.com', 'Gauteng', 'Johannesburg'],
            ['Anita Fourie', 'afourie@example.com', 'Western Cape', 'Stellenbosch'],
            ['Sipho Dlamini', 'sdlamini@example.com', 'KwaZulu-Natal', 'Pietermaritzburg'],
        ];

        $sellers = [];
        foreach ($sellerData as $i => [$name, $email, $province, $city]) {
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => strtok($name, ' '), 'surname' => substr($name, strpos($name, ' ') + 1),
                    'email_verified_at' => now(), 'phone' => '0'.random_int(7, 9).random_int(10000000, 99999999),
                    'city' => $city, 'province' => $province, 'is_active' => true, 'password' => self::DEMO_PASSWORD,
                ]
            );
            $this->assignRole($user, Role::PRIVATE_SELLER);

            $seller = PrivateSeller::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'contact_email' => $email, 'phone' => $user->phone, 'city' => $city, 'province' => $province,
                    'status' => PrivateSeller::STATUS_VERIFIED, 'verified_at' => now()->subDays(20), 'is_active' => true,
                ]
            );
            $this->comment("- Private seller: {$name} ({$email} / ".self::DEMO_PASSWORD.')');
            $sellers[$seller->id] = $seller;
        }

        return $sellers;
    }

    protected function createCampaigns(): array
    {
        $campaignData = [
            ['Buy & Get Paid 2026', 'Active', now()->subDays(40), now()->addMonths(4), true],
            ['Festive Reward Drive', 'Active', now()->subDays(20), now()->addMonths(2), false],
            ['First Quarter Buyer Rewards', 'Active', now()->subDays(5), now()->addMonths(3), false],
            ['Fleet & Business Reward Week', 'Scheduled', now()->addDays(10), now()->addMonths(1), false],
            ['Winter Clearance Rewards', 'Paused', now()->subMonths(2), now()->addMonths(5), false],
            ['Auto Weekend 2026', 'Expired', now()->subMonths(6), now()->subMonths(4), false],
        ];

        $campaigns = [];
        $index = 1;
        foreach ($campaignData as $i => [$name, $status, $start, $end, $featured]) {
            $campaign = VehicleCampaign::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => 'A curated WorxCars reward campaign. Buy a qualifying vehicle within the campaign window and the WorxCars Buyer Reward may be payable after verified purchase.',
                    'terms' => 'The WorxCars Buyer Reward is subject to eligibility, successful purchase, seller confirmation, WorxCars verification and the campaign terms. The reward is not payable merely for enquiring.',
                    'start_date' => $start, 'end_date' => $end,
                    'status' => $status, 'is_featured' => $featured, 'created_by' => $this->adminId,
                ]
            );
            $campaigns[$campaign->id] = $campaign;
            $index++;
        }

        return $campaigns;
    }

    protected function createVehicles(array $dealerships, array $sellers, array $campaigns): array
    {
        $catalog = [
            ['Toyota', 'Hilux', '2.8 GD-6 RB6', 2023, 42000, 749900, 'Automatic', 'Diesel', 'Bakkie', 'Gauteng', 'Sandton', true],
            ['Toyota', 'Corolla', '1.8 Hybrid XS', 2022, 35000, 419900, 'Automatic', 'Hybrid', 'Sedan', 'Gauteng', 'Johannesburg', true],
            ['Volkswagen', 'Golf', '8 GTI DSG', 2022, 28000, 699900, 'Automatic', 'Petrol', 'Hatchback', 'Western Cape', 'Cape Town', true],
            ['Volkswagen', 'Polo', '1.0 TSI Comfortline', 2023, 18000, 319900, 'Manual', 'Petrol', 'Hatchback', 'Gauteng', 'Centurion', false],
            ['Ford', 'Ranger', '2.0 SiT XLT 4x4', 2023, 30000, 759900, 'Automatic', 'Diesel', 'Bakkie', 'Gauteng', 'Midrand', true],
            ['BMW', 'X1', 'sDrive20i M Sport', 2022, 31000, 759900, 'Automatic', 'Petrol', 'SUV', 'Western Cape', 'Cape Town', true],
            ['Mercedes-Benz', 'C-Class', 'C200 AMG Line', 2021, 52000, 699900, 'Automatic', 'Petrol', 'Sedan', 'Gauteng', 'Sandton', true],
            ['Audi', 'A3', '35 TFSI S Line', 2022, 26000, 549900, 'Automatic', 'Petrol', 'Hatchback', 'KwaZulu-Natal', 'Durban', false],
            ['Hyundai', 'i20', '1.2 Dynamic', 2023, 12000, 289900, 'Manual', 'Petrol', 'Hatchback', 'Gauteng', 'Pretoria', false],
            ['Kia', 'Sportage', '2.0 CRDi GT-Line', 2022, 40000, 629900, 'Automatic', 'Diesel', 'SUV', 'Gauteng', 'Johannesburg', false],
            ['Nissan', 'Navara', '2.5 D SE 4x4', 2021, 65000, 559900, 'Manual', 'Diesel', 'Bakkie', 'Western Cape', 'Cape Town', false],
            ['Isuzu', 'D-Max', '3.0 TD LS', 2022, 38000, 619900, 'Automatic', 'Diesel', 'Bakkie', 'Gauteng', 'Sandton', true],
            ['Suzuki', 'Swift', '1.2 GLX', 2023, 10000, 229900, 'Manual', 'Petrol', 'Hatchback', 'Gauteng', 'Johannesburg', false],
            ['Mahindra', 'Pik Up', '2.2 S10 4x4', 2022, 45000, 389900, 'Manual', 'Diesel', 'Bakkie', 'Eastern Cape', 'Port Elizabeth', false],
            ['Toyota', 'RAV4', '2.0 GX', 2021, 47000, 529900, 'Automatic', 'Petrol', 'SUV', 'KwaZulu-Natal', 'Durban', true],
            ['Volkswagen', 'Amarok', '3.0 TDI V6', 2023, 22000, 859900, 'Automatic', 'Diesel', 'Bakkie', 'Gauteng', 'Pretoria', false],
            ['BMW', '3 Series', '320i M Sport', 2022, 30000, 646000, 'Automatic', 'Petrol', 'Sedan', 'Gauteng', 'Midrand', true],
            ['Mercedes-Benz', 'GLC', 'GLC 300d 4MATIC', 2022, 28000, 949900, 'Automatic', 'Diesel', 'SUV', 'Western Cape', 'Cape Town', false],
            ['Toyota', 'Land Cruiser', '79 4.5D', 2021, 35000, 1149900, 'Manual', 'Diesel', 'SUV', 'Gauteng', 'Pretoria', false],
            ['Hyundai', 'Tucson', '1.6 T-GDi Elite', 2022, 25000, 559900, 'Automatic', 'Petrol', 'SUV', 'Gauteng', 'Johannesburg', false],
            ['Kia', 'Sorento', '2.2 CRDi EX', 2021, 48000, 749900, 'Automatic', 'Diesel', 'SUV', 'Western Cape', 'Cape Town', false],
            ['Ford', 'Everest', '2.0 BiT Trend 4x4', 2022, 36000, 869900, 'Automatic', 'Diesel', 'SUV', 'KwaZulu-Natal', 'Durban', false],
            ['Audi', 'Q5', '45 TFSI quattro', 2021, 54000, 849900, 'Automatic', 'Petrol', 'SUV', 'Gauteng', 'Sandton', false],
            ['Volkswagen', 'Tiguan', '1.4 TSI Life', 2022, 24000, 559900, 'Automatic', 'Petrol', 'SUV', 'Gauteng', 'Midrand', false],
            ['Nissan', 'X-Trail', '2.5 Acenta', 2021, 58000, 429900, 'Automatic', 'Petrol', 'SUV', 'Western Cape', 'Cape Town', false],
            ['Toyota', 'Fortuner', '2.8 GD-6', 2022, 40000, 719900, 'Automatic', 'Diesel', 'SUV', 'Gauteng', 'Johannesburg', true],
            ['Suzuki', 'Jimny', '1.5 GLX ALLGRIP', 2022, 15000, 419900, 'Manual', 'Petrol', 'SUV', 'Gauteng', 'Pretoria', false],
            ['Isuzu', 'MU-X', '3.0 TD LS', 2021, 51000, 649900, 'Automatic', 'Diesel', 'SUV', 'Gauteng', 'Sandton', false],
            ['Mercedes-Benz', 'E-Class', 'E200 AMG Line', 2021, 46000, 999900, 'Automatic', 'Petrol', 'Sedan', 'Gauteng', 'Johannesburg', false],
            ['BMW', 'X3', 'xDrive20i M Sport', 2022, 27000, 908100, 'Automatic', 'Petrol', 'SUV', 'KwaZulu-Natal', 'Durban', true],
        ];

        $dealershipIds = array_keys($dealerships);
        $sellerIds = array_keys($sellers);
        $campaignIds = array_keys($campaigns);

        $vehicles = [];
        $rewardConfigs = [
            [3000, 8000], [2500, 6500], [4000, 10000], [2000, 6000], [3500, 9000],
        ];

        foreach ($catalog as $i => $row) {
            [$make, $model, $variant, $year, $mileage, $price, $transmission, $fuel, $body, $province, $city, $featured] = $row;
            $isPrivate = $i % 7 === 5;

            $reference = 'WC-'.(1000 + $i);
            $vehicle = Vehicle::where('vehicle_reference', $reference)->first();
            $isNewVehicle = $vehicle === null;

            if ($isNewVehicle) {
                $vehicle = Vehicle::factory()->create([
                    'dealership_id' => $isPrivate ? null : $dealershipIds[$i % count($dealershipIds)],
                    'private_seller_id' => $isPrivate ? $sellerIds[$i % count($sellerIds)] : null,
                    'seller_type' => $isPrivate ? 'private' : 'dealership',
                    'make' => $make, 'model' => $model, 'variant' => $variant,
                    'year' => $year, 'mileage' => $mileage, 'price' => $price,
                    'transmission' => $transmission, 'fuel_type' => $fuel, 'body_type' => $body,
                    'location_province' => $province, 'location_city' => $city,
                    'is_featured' => $featured,
                    'slug' => Str::slug(implode('-', [$make, $model, $variant, $year])).'-'.($i + 1),
                    'vehicle_reference' => $reference,
                    'status' => Vehicle::STATUS_PUBLISHED,
                    'published_at' => now()->subDays($i),
                ]);
            }

            $campaignId = null;
            if ($featured && count($campaignIds)) {
                $campaignId = $campaignIds[$i % count($campaignIds)];
                $vehicle->update(['campaign_id' => $campaignId]);
            }

            if ($campaignId) {
                [$reward, $commission] = $rewardConfigs[$i % count($rewardConfigs)];
                CampaignVehicle::updateOrCreate(
                    ['campaign_id' => $campaignId, 'vehicle_id' => $vehicle->id],
                    [
                        'commission_type' => 'fixed', 'commission_value' => $commission,
                        'reward_type' => 'fixed', 'reward_value' => $reward,
                        'reward_percent' => 0, 'reward_min' => 1500, 'reward_max' => 25000,
                    ]
                );
            } else {
                [$reward, $commission] = $rewardConfigs[($i + 2) % count($rewardConfigs)];
                $this->commissions->syncForVehicle($vehicle, [
                    'commission_type' => 'fixed', 'commission_value' => $commission,
                    'reward_type' => 'fixed', 'reward_value' => $reward,
                    'reward_min' => 1500, 'reward_max' => 25000,
                ], null, $this->adminId);
            }

            if ($isNewVehicle) {
                $paths = $this->images->generateForVehicle($vehicle);
                foreach ($paths as $p) {
                    VehicleImage::create([
                        'vehicle_id' => $vehicle->id,
                        'image_path' => $p['path'],
                        'thumbnail_path' => $p['thumbnail'],
                        'alt_text' => $p['alt'],
                        'sort_order' => $p['sort_order'],
                        'is_main' => $p['main'],
                    ]);
                }
            }

            $vehicles[] = $vehicle;
        }

        $this->comment('- Vehicles created: '.count($vehicles));

        return $vehicles;
    }

    protected function createEnquiries(array $vehicles): void
    {
        if (! count($vehicles)) {
            return;
        }

        $customers = User::role('customer')->get();
        $statuses = ['new', 'contacted', 'qualified', 'appointment', 'new', 'closed'];

        for ($i = 0; $i < 10; $i++) {
            $customer = $customers[$i % $customers->count()];
            $vehicle = $vehicles[($i * 3) % count($vehicles)];

            BuyerEnquiry::firstOrCreate(
                ['email' => $customer->email, 'vehicle_id' => $vehicle->id],
                [
                    'user_id' => $customer->id,
                    'name' => $customer->fullName(),
                    'phone' => $customer->phone,
                    'preferred_contact_method' => ['email', 'phone', 'whatsapp'][$i % 3],
                    'message' => "I'm interested in the {$vehicle->displayName()}. Could I arrange a viewing and get more details?",
                    'finance_interest' => $i % 2 === 0,
                    'trade_in_interest' => $i % 3 === 0,
                    'status' => $statuses[$i % count($statuses)],
                ]
            );
        }

        $this->comment('- Enquiries created: 10');
    }

    protected function createClaimsAndRewards(array $vehicles): void
    {
        $customers = User::role('customer')->get();

        $flows = [
            ['submitted', PurchaseClaim::STATUS_SUBMITTED, null, null],
            ['under_review', PurchaseClaim::STATUS_UNDER_REVIEW, null, null],
            ['seller_verification', PurchaseClaim::STATUS_SELLER_VERIFICATION, null, null],
            ['verified', PurchaseClaim::STATUS_TRANSACTION_VERIFIED, null, null],
            ['approved', PurchaseClaim::STATUS_REWARD_APPROVED, BuyerReward::STATUS_APPROVED, null],
            ['paid', PurchaseClaim::STATUS_PAID, BuyerReward::STATUS_PAID, RewardPayout::STATUS_COMPLETED],
            ['rejected', PurchaseClaim::STATUS_REJECTED, BuyerReward::STATUS_REJECTED, null],
        ];

        $index = 1;
        foreach ($flows as $i => [$label, $claimStatus, $rewardStatus, $payoutStatus]) {
            $vehicle = $vehicles[($i * 4 + 2) % count($vehicles)];
            $customer = $customers[$i % $customers->count()];
            $breakdown = $this->commissions->breakdown($vehicle);

            $claim = PurchaseClaim::firstOrCreate(
                ['claim_reference' => 'WC-C-'.str_pad((string) $index, 4, '0', STR_PAD_LEFT)],
                [
                    'user_id' => $customer->id,
                    'vehicle_id' => $vehicle->id,
                    'dealership_id' => $vehicle->dealership_id,
                    'private_seller_id' => $vehicle->private_seller_id,
                    'purchase_date' => now()->subDays(15 + $i * 5),
                    'purchase_price' => $vehicle->price,
                    'provider_reference' => 'INV-'.(202600 + $i * 7),
                    'description' => 'Purchased via WorxCars marketplace listing.',
                    'status' => $claimStatus,
                    'commission_amount' => $breakdown['commission_amount'],
                    'reward_amount' => $breakdown['reward_amount'],
                    'retained_amount' => $breakdown['retained_amount'],
                    'commission_snapshot' => $breakdown['config'],
                    'reward_snapshot' => ['reward_amount' => $breakdown['reward_amount'], 'note' => 'Snapshot taken at claim submission.'],
                    'declaration_consent' => true,
                    'bank_account_name' => $customer->fullName(),
                    'bank_name' => ['FNB', 'Standard Bank', 'Nedbank', 'ABSA', 'Capitec'][$i % 5],
                    'bank_account_number' => '1'.random_int(100000000, 999999999),
                    'bank_branch_code' => '250655',
                ]
            );

            $this->addClaimDocument($claim, 'proof_of_purchase', 'Proof of purchase - '.$claim->claim_reference);

            $verifiedAt = match ($claimStatus) {
                PurchaseClaim::STATUS_TRANSACTION_VERIFIED, PurchaseClaim::STATUS_REWARD_APPROVED,
                PurchaseClaim::STATUS_PAID => now()->subDays(6),
                default => null,
            };

            if ($verifiedAt) {
                $claim->update([
                    'status' => $claimStatus,
                    'verified_by' => $this->adminId, 'verified_at' => $verifiedAt,
                    'seller_verified_by' => $this->adminId, 'seller_verified_at' => $verifiedAt->copy()->subHour(),
                    'seller_verification_note' => 'Sale confirmed by seller.',
                ]);
            }

            if ($claimStatus === PurchaseClaim::STATUS_REWARD_APPROVED || $claimStatus === PurchaseClaim::STATUS_PAID) {
                $claim->update([
                    'approved_by' => $this->adminId, 'approved_at' => now()->subDays(4),
                    'status' => $claimStatus,
                ]);
            }

            if ($claimStatus === PurchaseClaim::STATUS_REJECTED) {
                $claim->update(['rejected_by' => $this->adminId, 'rejected_at' => now()->subDays(2), 'rejection_reason' => 'Purchase could not be verified against seller records.']);
            }

            $reward = BuyerReward::firstOrCreate(
                ['purchase_claim_id' => $claim->id],
                [
                    'reward_reference' => 'WC-R-'.str_pad((string) $index, 4, '0', STR_PAD_LEFT),
                    'user_id' => $customer->id,
                    'vehicle_id' => $vehicle->id,
                    'campaign_id' => $vehicle->campaign_id,
                    'amount' => $breakdown['reward_amount'],
                    'status' => $rewardStatus ?? ($claimStatus === PurchaseClaim::STATUS_REJECTED ? BuyerReward::STATUS_REJECTED : BuyerReward::STATUS_PENDING),
                    'status_at' => now()->subDays(3),
                    'configuration_snapshot' => ['config' => $breakdown['config'], 'recorded_at' => now()->toDateTimeString()],
                ]
            );

            if ($rewardStatus === BuyerReward::STATUS_APPROVED || $rewardStatus === BuyerReward::STATUS_PAID) {
                $reward->update(['approved_by' => $this->adminId, 'approved_at' => now()->subDays(3), 'eligibility_confirmed_at' => now()->subDays(3), 'status_at' => now()->subDays(3)]);
            }

            $this->recordLedger($claim, $breakdown);

            if ($payoutStatus === RewardPayout::STATUS_COMPLETED) {
                $payout = RewardPayout::firstOrCreate(
                    ['buyer_reward_id' => $reward->id],
                    [
                        'payout_reference' => 'WC-P-'.str_pad((string) $index, 4, '0', STR_PAD_LEFT),
                        'user_id' => $customer->id,
                        'amount' => $breakdown['reward_amount'],
                        'status' => RewardPayout::STATUS_COMPLETED,
                        'provider' => 'manual',
                        'provider_transaction_id' => 'TXN-'.str_pad((string) $index, 6, '0', STR_PAD_LEFT),
                        'requested_at' => now()->subDays(2),
                        'completed_at' => now()->subDays(1),
                    ]
                );

                $reward->update(['status' => BuyerReward::STATUS_PAID, 'paid_at' => $payout->completed_at, 'payout_snapshot' => ['reference' => $payout->payout_reference]]);
                $claim->update(['status' => PurchaseClaim::STATUS_PAID]);
            }

            $index++;
        }

        $this->comment('- Claims & rewards created: '.count($flows));
    }

    protected function recordLedger(PurchaseClaim $claim, array $breakdown): void
    {
        Transaction::firstOrCreate(
            ['transaction_reference' => 'WC-T-'.str_pad((string) $claim->id * 7, 6, '0', STR_PAD_LEFT), 'purchase_claim_id' => $claim->id],
            [
                'type' => 'commission',
                'vehicle_id' => $claim->vehicle_id,
                'user_id' => $claim->user_id,
                'dealership_id' => $claim->dealership_id,
                'amount' => $breakdown['commission_amount'],
                'commission_amount' => $breakdown['commission_amount'],
                'reward_amount' => $breakdown['reward_amount'],
                'retained_amount' => $breakdown['retained_amount'],
                'direction' => 'in',
                'status' => 'completed',
                'description' => 'WorxCars commission on verified purchase for claim '.$claim->claim_reference,
                'metadata' => ['claim_reference' => $claim->claim_reference, 'sale_price' => $claim->purchase_price],
                'occurred_at' => $claim->verified_at ?? now(),
            ]
        );
    }

    protected function addClaimDocument(PurchaseClaim $claim, string $type, string $label): void
    {
        $storage = Storage::disk('public');
        $path = 'claims/'.$claim->ulid.'/'.Str::kebab($label).'.txt';
        $storage->put($path, implode("\n", [
            'WorxCars - '.$label,
            'Claim reference: '.$claim->claim_reference,
            'Date: '.now()->toDateString(),
            'Vehicle: '.$claim->vehicle->displayName().' ('.$claim->vehicle->vehicle_reference.')',
            'Purchase price: R'.number_format((float) $claim->purchase_price, 2),
        ]));

        PurchaseClaimDocument::firstOrCreate(
            ['purchase_claim_id' => $claim->id, 'type' => $type],
            ['file_path' => $path, 'file_name' => Str::kebab($label).'.txt', 'mime_type' => 'text/plain', 'size' => 300]
        );
    }

    protected function assignRole(User $user, string $role): void
    {
        $user->roles()->syncWithoutDetaching(Role::where('name', $role)->value('id'));
    }

    protected function assets(): void
    {
        $this->images->generateLogo();
        $this->comment('- Brand assets generated.');
    }

    protected function comment(string $message): void
    {
        $this->command?->line($message);
    }
}