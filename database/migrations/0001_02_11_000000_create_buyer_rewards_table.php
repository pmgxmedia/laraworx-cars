<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buyer_rewards', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('reward_reference')->nullable();
            $table->foreignId('purchase_claim_id')->nullable()->constrained('purchase_claims')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('campaign_id')->nullable()->constrained('vehicle_campaigns')->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('ZAR');
            $table->string('status')->default('pending');
            // pending|eligible|under_verification|approved|paid|rejected
            $table->dateTime('status_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('eligibility_confirmed_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->json('configuration_snapshot')->nullable()->comment('immutable rules snapshot at calculation');
            $table->json('payout_snapshot')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('user_id');
            $table->index('reward_reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buyer_rewards');
    }
};