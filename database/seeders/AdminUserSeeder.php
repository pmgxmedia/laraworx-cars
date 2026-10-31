<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('worxcars.admin_email', 'admin@worxcars.local');
        $password = config('worxcars.admin_password', 'worxcars-admin');

        if (strlen($password) < 8) {
            $password = 'worxcars-admin';
        }

        $admin = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Platform',
                'surname' => 'Administrator',
                'email_verified_at' => now(),
                'password' => $password,
                'is_active' => true,
            ]
        );

        $admin->roles()->syncWithoutDetaching(Role::where('name', Role::SUPER_ADMIN)->value('id'));
    }
}
