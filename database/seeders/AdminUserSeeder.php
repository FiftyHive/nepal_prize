<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@nepalprizechecker.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('password'), // Change this!
            ]
        );

        $this->command->info('Admin user created: admin@nepalprizechecker.com / password');
        $this->command->warn('⚠️  Please change the admin password immediately after first login!');
    }
}
