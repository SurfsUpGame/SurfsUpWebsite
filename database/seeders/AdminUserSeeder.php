<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        
        // Get admin Steam ID from environment variable
        $adminSteamId = env('ADMIN_STEAM_ID');
        
        if (!$adminSteamId) {
            $this->command->warn('No ADMIN_STEAM_ID set in .env file. Skipping admin user creation.');
            return;
        }
        
        // Check if user already exists
        $user = User::where('steam_id', $adminSteamId)->first();
        
        if ($user) {
            // Assign admin role if not already assigned
            if (!$user->hasRole('admin')) {
                $user->assignRole('admin');
                $this->command->info("Admin role assigned to existing user with Steam ID: {$adminSteamId}");
            } else {
                $this->command->info("User with Steam ID {$adminSteamId} already has admin role.");
            }
        } else {
            $this->command->warn("No user found with Steam ID: {$adminSteamId}");
            $this->command->info("Admin role will be automatically assigned when this user logs in via Steam.");
        }
    }
}