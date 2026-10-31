<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('vehicle_reference')->nullable();
            $table->foreignId('dealership_id')->nullable()->constrained('dealerships')->nullOnDelete();
            $table->foreignId('private_seller_id')->nullable()->constrained('private_sellers')->nullOnDelete();
            $table->string('seller_type')->default('dealership'); // dealership|private
            $table->string('status')->default('draft'); // draft|pending_approval|published|reserved|sold|expired|archived|rejected
            $table->string('make');
            $table->string('model');
            $table->string('variant')->nullable();
            $table->unsignedSmallInteger('year');
            $table->unsignedInteger('mileage');
            $table->decimal('price', 12, 2);
            $table->string('currency', 3)->default('ZAR');
            $table->string('transmission')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('body_type')->nullable();
            $table->string('engine_size')->nullable();
            $table->decimal('engine_cc', 8, 0)->nullable();
            $table->decimal('power_kw', 8, 2)->nullable();
            $table->decimal('torque_nm', 8, 2)->nullable();
            $table->string('colour')->nullable();
            $table->unsignedTinyInteger('doors')->nullable();
            $table->unsignedTinyInteger('seats')->nullable();
            $table->string('condition')->default('used'); // new|demo|used
            $table->string('vehicle_condition_detail')->nullable();
            $table->string('registration_status')->nullable(); // registered|unregistered
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->string('location_province')->nullable();
            $table->string('location_city')->nullable();
            $table->string('location_suburb')->nullable();
            $table->string('vin')->nullable()->comment('stored encrypted');
            $table->string('reg_number')->nullable()->comment('stored encrypted');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_promoted')->default(false);
            $table->unsignedInteger('views_count')->default(0);
            $table->string('slug')->unique();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->dateTime('date_first_registered')->nullable();
            $table->foreignId('campaign_id')->nullable()->constrained('vehicle_campaigns')->nullOnDelete();
            $table->json('admin_notes')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_featured']);
            $table->index('make');
            $table->index('model');
            $table->index('year');
            $table->index('mileage');
            $table->index('price');
            $table->index('transmission');
            $table->index('fuel_type');
            $table->index('body_type');
            $table->index(['location_province', 'location_city']);
        });

        Schema::create('vehicle_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->string('image_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('alt_text')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_main')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['vehicle_id', 'sort_order']);
        });

        Schema::create('vehicle_specifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->string('spec_key');
            $table->string('label');
            $table->string('value');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_specifications');
        Schema::dropIfExists('vehicle_images');
        Schema::dropIfExists('vehicles');
    }
};