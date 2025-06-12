<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create roles
        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $user = Role::firstOrCreate(['name' => 'user']);

        // Create permissions
        $approvePost = Permission::firstOrCreate(['name' => 'approve-post']);
        $createPost = Permission::firstOrCreate(['name' => 'create-post']);
        $declinePost = Permission::firstOrCreate(['name' => 'decline-post']);
        $editPost = Permission::firstOrCreate(['name' => 'edit-post']);
        $deletePost = Permission::firstOrCreate(['name' => 'delete-post']);

        // Assign permissions to roles dynamically
        $superadmin->permissions()->sync([
            $approvePost->id, 
            $declinePost->id, 
            $createPost->id, 
            $editPost->id, 
            $deletePost->id
        ]);

        $admin->permissions()->sync([
            $approvePost->id, 
            $declinePost->id, 
            $createPost->id, 
            $editPost->id
        ]);

        $user->permissions()->sync([
            $createPost->id
        ]);
    
    }
}
