<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleUserSeeder extends Seeder
{
    // Roles that can be assigned to multiple users
    private array $multiUserableRoles = ['guest', 'injection_worker'];

    public function run(): void
    {
        // Get or create admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'gender' => 'male',
                'password' => bcrypt('password'),
                'status' => true,
            ]
        );
        
        // Get all roles and users
        $roles = Role::all();
        $users = User::where('email', '!=', 'admin@example.com')->get();
        
        // If no roles or users exist, create some
        if ($roles->isEmpty()) {
            $roles = Role::factory()->count(5)->create(['created_by' => $adminUser->id]);
        }
        
        if ($users->isEmpty()) {
            $users = User::factory()->count(10)->create();
        }
        
        // Assign roles to users
        foreach ($users as $user) {
            $this->assignRolesToUser($user, $roles, $adminUser);
        }
    }

    private function assignRolesToUser($user, $roles, $adminUser): void
    {
        foreach ($roles as $role) {
            // Check if this role is multi-userable
            if (in_array(strtolower($role->name), $this->multiUserableRoles)) {
                // Multi-userable roles: assign to this user
                $this->assignRoleToUser($user, $role, $adminUser);
            } else {
                // Single-user roles: check if already assigned
                $alreadyAssigned = DB::table('role_user')
                    ->where('role_id', $role->id)
                    ->exists();
                
                if (!$alreadyAssigned) {
                    // Assign to this user if not already assigned
                    $this->assignRoleToUser($user, $role, $adminUser);
                }
            }
        }
    }

    private function assignRoleToUser($user, $role, $adminUser): void
    {
        DB::table('role_user')->updateOrInsert(
            [
                'user_id' => $user->id,
                'role_id' => $role->id,
            ],
            [
                'assigned_by' => $adminUser->id,
                'assigned_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}