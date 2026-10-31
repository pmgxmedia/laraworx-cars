<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('vehicle_campaigns')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->string('commission_type')->default('fixed'); // fixed|percent
            $table->decimal('commission_value', 12, 2)->nullable();
            $table->string('reward_type')->default('fixed'); // fixed|percent|commission_based
            $table->decimal('reward_value', 12, 2)->nullable();
            $table->decimal('reward_percent', 6, 2)->nullable();
            $table->decimal('reward_min', 12, 2)->nullable();
            $table->decimal('reward_max', 12, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['campaign_id', 'vehicle_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_vehicles');
    }
};