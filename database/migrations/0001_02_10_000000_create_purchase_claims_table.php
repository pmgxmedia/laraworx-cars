<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_claims', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('claim_reference')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('dealership_id')->nullable()->constrained('dealerships')->nullOnDelete();
            $table->foreignId('private_seller_id')->nullable()->constrained('private_sellers')->nullOnDelete();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->string('provider_reference')->nullable()->comment('invoice or sales agreement number');
            $table->text('description')->nullable();
            $table->string('status')->default('submitted');
            // submitted|under_review|seller_verification|transaction_verified|reward_approved|payment_processing|paid|rejected|disputed
            $table->decimal('commission_amount', 12, 2)->nullable()->comment('snapshot at claim time');
            $table->decimal('reward_amount', 12, 2)->nullable()->comment('snapshot at claim time');
            $table->decimal('retained_amount', 12, 2)->nullable();
            $table->json('commission_snapshot')->nullable();
            $table->json('reward_snapshot')->nullable();
            $table->json('fraud_flags')->nullable();
            $table->json('declarations')->nullable();
            $table->text('bank_account_name')->nullable();
            $table->text('bank_name')->nullable();
            $table->text('bank_account_number')->nullable();
            $table->text('bank_branch_code')->nullable();
            $table->boolean('declaration_consent')->default(false);
            $table->foreignId('seller_verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('seller_verified_at')->nullable();
            $table->string('seller_verification_note')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('verified_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('rejected_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index(['user_id', 'vehicle_id']);
            $table->index('claim_reference');
        });

        Schema::create('purchase_claim_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_claim_id')->constrained('purchase_claims')->onDelete('cascade');
            $table->string('type'); // proof_of_purchase|invoice|sales_agreement|registration|identity|bank_details
            $table->string('file_path');
            $table->string('file_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedInteger('size')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_claim_documents');
        Schema::dropIfExists('purchase_claims');
    }
};