<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('private_sellers', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('contact_email')->nullable();
            $table->string('phone')->nullable();
            $table->string('id_number')->nullable()->comment('stored encrypted');
            $table->string('street_address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('id_document_path')->nullable();
            $table->string('registration_document_path')->nullable();
            $table->string('status')->default('pending'); // pending|under_review|verified|suspended|rejected
            $table->timestamp('verified_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('private_sellers');
    }
};