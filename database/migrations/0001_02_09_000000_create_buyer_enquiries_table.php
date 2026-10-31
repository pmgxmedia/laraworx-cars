<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buyer_enquiries', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('preferred_contact_method')->default('email'); // email|phone|whatsapp
            $table->text('message')->nullable();
            $table->date('preferred_date')->nullable();
            $table->boolean('finance_interest')->default(false);
            $table->boolean('trade_in_interest')->default(false);
            $table->string('status')->default('new'); // new|contacted|qualified|appointment|converted|closed|spam
            $table->string('source')->default('website');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('internal_note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('vehicle_id');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buyer_enquiries');
    }
};