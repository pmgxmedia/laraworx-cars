<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reward_payouts', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('payout_reference')->nullable();
            $table->foreignId('buyer_reward_id')->constrained('buyer_rewards')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('ZAR');
            $table->string('status')->default('scheduled'); // scheduled|processing|completed|failed|reversed
            $table->string('provider')->default('manual'); // configurable gateway provider
            $table->string('provider_transaction_id')->nullable();
            $table->json('provider_response')->nullable();
            $table->json('recipient')->nullable();
            $table->dateTime('requested_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->string('failure_reason')->nullable();
            $table->timestamps();

            $table->index(['status', 'provider']);
            $table->index('payout_reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reward_payouts');
    }
};