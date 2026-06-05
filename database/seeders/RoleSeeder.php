<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@unisya.ac.id'],
            [
                'id' => User::where('email', 'admin@unisya.ac.id')->value('id') ?? Str::uuid()->toString(),
                'name' => 'Super Administrator',
                'password' => Hash::make('Admin@123'),
                'phone' => '628001234567',
                'role' => 'super_admin',
                'is_active' => true,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'created_by' => null,
                'updated_by' => null,
            ]
        );
    }
}