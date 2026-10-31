<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('dealership_id')->nullable()->constrained('dealerships')->nullOnDelete();
            $table->string('seller_type')->default('dealership');
            $table->foreignId('campaign_id')->nullable()->constrained('vehicle_campaigns')->nullOnDelete();
            $table->string('commission_type')->default('fixed'); // fixed|percent
            $table->decimal('commission_value', 12, 2)->nullable();
            $table->decimal('commission_amount', 12, 2)->nullable()->comment('computed commission in ZAR');
            $table->string('reward_type')->default('fixed');
            $table->decimal('reward_value', 12, 2)->nullable();
            $table->decimal('reward_percent', 6, 2)->nullable();
            $table->decimal('reward_min', 12, 2)->nullable();
            $table->decimal('reward_max', 12, 2)->nullable();
            $table->decimal('reward_amount', 12, 2)->nullable()->comment('computed buyer reward');
            $table->decimal('retained_amount', 12, 2)->nullable()->comment('platform retained = commission - reward');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['vehicle_id', 'campaign_id']);
            $table->index('dealership_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};