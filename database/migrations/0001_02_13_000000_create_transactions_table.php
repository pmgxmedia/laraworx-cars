<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('transaction_reference')->nullable();
            $table->string('type'); // purchase|commission|reward|payout|refund|reversal
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('purchase_claim_id')->nullable()->constrained('purchase_claims')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('dealership_id')->nullable()->constrained('dealerships')->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->decimal('commission_amount', 12, 2)->nullable();
            $table->decimal('reward_amount', 12, 2)->nullable();
            $table->decimal('retained_amount', 12, 2)->nullable();
            $table->string('currency', 3)->default('ZAR');
            $table->string('direction')->default('in'); // in|out
            $table->string('status')->default('completed'); // pending|completed|failed|reversed
            $table->text('description')->nullable();
            $table->json('metadata')->nullable()->comment('immutable financial snapshot');
            $table->dateTime('occurred_at');
            $table->timestamps();

            $table->index('type');
            $table->index('purchase_claim_id');
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};