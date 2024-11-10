<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin']);

        $superadmin = User::create([
            'email' => 'hazimahpte@gmail.com',
            'name' => 'Super Admin',
            'staff_id' => '100001',
            'password' => Hash::make('superadmin123'),
            'position_id' => 1,
            'office_phone_no' => '082000000',
            'publish_status' => true,
            'email_verified_at' => now(),
        ]);

        // associate the users with campuses in the pivot table (campus_user)
        $userData = [
            ['user_id' => 1, 'campus_id' => 1],
        ];

        // Insert associations into the campus_user pivot table
        DB::table('campus_user')->insert($userData);

        $superadmin->assignRole($superadminRole);

        $this->assignPermissionsToSuperAdmin();
    }

    /**
     * Assign all permissions to the Super Admin role.
     */
    protected function assignPermissionsToSuperAdmin()
    {
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $superadminRole->syncPermissions(Permission::all());
    }
}
