<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class GrantAdminPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:grant-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grant full User and Role permissions to admin role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Granting admin permissions...');

        // Get or create admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // User permissions
        $userPermissions = [
            'view_any_user',
            'view_user',
            'create_user',
            'update_user',
            'delete_user',
            'delete_any_user',
            'force_delete_user',
            'force_delete_any_user',
            'restore_user',
            'restore_any_user',
            'replicate_user',
            'reorder_user',
        ];

        // Role permissions
        $rolePermissions = [
            'view_any_role',
            'view_role',
            'create_role',
            'update_role',
            'delete_role',
            'delete_any_role',
        ];

        // Combine all permissions
        $allPermissions = array_merge($userPermissions, $rolePermissions);

        // Create permissions if they don't exist and assign to admin role
        foreach ($allPermissions as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName]);
            
            if (!$adminRole->hasPermissionTo($permission)) {
                $adminRole->givePermissionTo($permission);
                $this->line("✓ Granted permission: {$permissionName}");
            } else {
                $this->line("- Already has permission: {$permissionName}");
            }
        }

        $this->info("\nAdmin role now has full User and Role permissions!");
        $this->info("Total permissions granted: " . count($allPermissions));

        return Command::SUCCESS;
    }
}
