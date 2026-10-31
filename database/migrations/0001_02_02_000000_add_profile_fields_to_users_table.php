<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('surname')->nullable()->after('name');
            $table->string('phone')->nullable()->after('email');
            $table->string('city')->nullable()->after('phone');
            $table->string('province')->nullable()->after('city');
            $table->string('avatar_path')->nullable()->after('province');
            $table->string('id_number_tail')->nullable()->after('avatar_path');
            $table->boolean('is_active')->default(true)->after('id_number_tail');
            $table->integer('login_attempts')->default(0)->after('is_active');
            $table->timestamp('locked_until')->nullable()->after('login_attempts');
            $table->timestamp('last_login_at')->nullable()->after('locked_until');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'surname', 'phone', 'city', 'province', 'avatar_path', 'id_number_tail',
                'is_active', 'login_attempts', 'locked_until', 'last_login_at',
            ]);
        });
    }
};