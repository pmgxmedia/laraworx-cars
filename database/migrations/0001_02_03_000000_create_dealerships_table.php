<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dealerships', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('business_name');
            $table->string('trading_name')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('logo_path')->nullable();
            $table->text('description')->nullable();
            $table->string('street_address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('status')->default('pending'); // pending|under_review|verified|suspended|rejected
            $table->timestamp('verified_at')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_branch_code')->nullable();
            $table->string('bank_account_type')->nullable();
            $table->string('commission_agreement_path')->nullable();
            $table->string('contract_path')->nullable();
            $table->text('admin_notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index(['province', 'city']);
        });

        Schema::create('dealership_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dealership_id')->constrained('dealerships')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('role_title')->default('manager');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->unique(['dealership_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dealership_users');
        Schema::dropIfExists('dealerships');
    }
};