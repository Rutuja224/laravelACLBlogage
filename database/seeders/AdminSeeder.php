<?php

namespace Database\Seeders;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;


use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $role = Role::firstOrCreate(['name' => 'superadmin']);

        $superadmin = User::firstOrCreate(
        ['email' => 'superadmin@example.com'],
        [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
            'role_id' => $role->id,  // Now $role is defined
        ]
        );

        if (method_exists($superadmin, 'roles')) {
                $superadmin->roles()->syncWithoutDetaching([$role->id]);
        }

        $permissions = ['create post', 'approve post', 'assign role', 'manage acl'];

        foreach ($permissions as $perm) {
        $permission = Permission::firstOrCreate(['name' => $perm]);
        $role->permissions()->syncWithoutDetaching([$permission->id]);
        }
    }
}
