<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        
        // Find user with specific Steam ID and assign admin role
        $user = User::where('steam_id', '76561197984176210')->first();
        
        if ($user) {
            $user->assignRole('admin');
        }
    }
}
