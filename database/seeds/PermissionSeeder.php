<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'Pengurusan Pengguna' => [
                ['name' => 'Tambah Pengguna', 'category' => 'Pengurusan Pengguna'],
                ['name' => 'Edit Pengguna', 'category' => 'Pengurusan Pengguna'],
                ['name' => 'Padam Pengguna', 'category' => 'Pengurusan Pengguna'],
                ['name' => 'Lihat Pengguna', 'category' => 'Pengurusan Pengguna'],
            ],
            'Pengurusan Kampus' => [
                ['name' => 'Tambah Kampus', 'category' => 'Pengurusan Kampus'],
                ['name' => 'Edit Kampus', 'category' => 'Pengurusan Kampus'],
                ['name' => 'Padam Kampus', 'category' => 'Pengurusan Kampus'],
                ['name' => 'Lihat Kampus', 'category' => 'Pengurusan Kampus'],
            ],
            'Pengurusan Makmal Komputer' => [
                ['name' => 'Tambah Makmal Komputer', 'category' => 'Pengurusan Makmal Komputer'],
                ['name' => 'Edit Makmal Komputer', 'category' => 'Pengurusan Makmal Komputer'],
                ['name' => 'Padam Makmal Komputer', 'category' => 'Pengurusan Makmal Komputer'],
                ['name' => 'Lihat Makmal Komputer', 'category' => 'Pengurusan Makmal Komputer'],
            ],
            'Pengurusan Perisian' => [
                ['name' => 'Tambah Perisian', 'category' => 'Pengurusan Perisian'],
                ['name' => 'Edit Perisian', 'category' => 'Pengurusan Perisian'],
                ['name' => 'Padam Perisian', 'category' => 'Pengurusan Perisian'],
                ['name' => 'Lihat Perisian', 'category' => 'Pengurusan Perisian'],
            ],
            'Pengurusan Senarai Semak Proses Kerja' => [
                ['name' => 'Tambah Senarai Semak Proses Kerja', 'category' => 'Pengurusan Senarai Semak Proses Kerja'],
                ['name' => 'Edit Senarai Semak Proses Kerja', 'category' => 'Pengurusan Senarai Semak Proses Kerja'],
                ['name' => 'Padam Senarai Semak Proses Kerja', 'category' => 'Pengurusan Senarai Semak Proses Kerja'],
                ['name' => 'Lihat Senarai Semak Proses Kerja', 'category' => 'Pengurusan Senarai Semak Proses Kerja'],
            ],
            'Pengurusan Senarai Semak Makmal' => [
                ['name' => 'Tambah Senarai Semak Makmal', 'category' => 'Pengurusan Senarai Semak Makmal'],
                ['name' => 'Edit Senarai Semak Makmal', 'category' => 'Pengurusan Senarai Semak Makmal'],
                ['name' => 'Padam Senarai Semak Makmal', 'category' => 'Pengurusan Senarai Semak Makmal'],
                ['name' => 'Lihat Senarai Semak Makmal', 'category' => 'Pengurusan Senarai Semak Makmal'],
            ],
            'Pengurusan Rekod Selenggara Makmal' => [
                ['name' => 'Tambah Rekod Selenggara Makmal', 'category' => 'Pengurusan Rekod Selenggara Makmal'],
                ['name' => 'Edit Rekod Selenggara Makmal', 'category' => 'Pengurusan Rekod Selenggara Makmal'],
                ['name' => 'Padam Rekod Selenggara Makmal', 'category' => 'Pengurusan Rekod Selenggara Makmal'],
                ['name' => 'Lihat Rekod Selenggara Makmal', 'category' => 'Pengurusan Rekod Selenggara Makmal'],
            ],
            'Pengurusan Rekod Selenggara Komputer' => [
                ['name' => 'Tambah Rekod Selenggara Komputer', 'category' => 'Pengurusan Rekod Selenggara Komputer'],
                ['name' => 'Edit Rekod Selenggara Komputer', 'category' => 'Pengurusan Rekod Selenggara Komputer'],
                ['name' => 'Padam Rekod Selenggara Komputer', 'category' => 'Pengurusan Rekod Selenggara Komputer'],
                ['name' => 'Lihat Rekod Selenggara Komputer', 'category' => 'Pengurusan Rekod Selenggara Komputer'],
            ],
            'Pengurusan Jawatan' => [
                ['name' => 'Tambah Jawatan', 'category' => 'Pengurusan Jawatan'],
                ['name' => 'Edit Jawatan', 'category' => 'Pengurusan Jawatan'],
                ['name' => 'Padam Jawatan', 'category' => 'Pengurusan Jawatan'],
                ['name' => 'Lihat Jawatan', 'category' => 'Pengurusan Jawatan'],
            ],
            'Pengurusan Makluman' => [
                ['name' => 'Tambah Makluman', 'category' => 'Pengurusan Makluman'],
                ['name' => 'Edit Makluman', 'category' => 'Pengurusan Makluman'],
                ['name' => 'Padam Makluman', 'category' => 'Pengurusan Makluman'],
                ['name' => 'Lihat Makluman', 'category' => 'Pengurusan Makluman'],
            ],
        ];

        foreach ($permissions as $category => $permissionArray) {
            foreach ($permissionArray as $permissionData) {
                Permission::firstOrCreate([
                    'name' => $permissionData['name'],
                    'category' => $permissionData['category'],
                    'guard_name' => 'web',
                ]);
            }
        }

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
