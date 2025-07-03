<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class GetUserRole extends Command
{
    protected $signature = 'user:role {userId}';
    protected $description = 'Get the role of a user by their ID';

    public function handle()
    {
        $userId = $this->argument('userId');
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return;
        }

        $roleName = $user->roles()->get() ?? 'No role assigned';
        $this->info("User ID {$userId} has role: {$roleName}");
    }
}
