<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $administratorPermissions = Permission::all();

        $staffPermissions = $administratorPermissions->reject(function ($permission) {
            return in_array($permission->name, [
                'lihat pengguna', 'tambah pengguna', 'ubah pengguna', 'hapus pengguna',
                'lihat peran dan hak akses', 'tambah peran dan hak akses', 'ubah peran dan hak akses', 'hapus peran dan hak akses',
            ]);
        });

        $roles = Role::all();

        // administrator
        $roles[0]->syncPermissions($administratorPermissions);

        // staff
        $roles[1]->syncPermissions($staffPermissions);

        // anggota
        $anggotaPermissions = Permission::whereIn('name', [
            'lihat barang', 'detail barang',
            'lihat peminjaman', 'tambah peminjaman', 'ubah peminjaman',
            'mengatur profile'
        ])->get();
        $roles[2]->syncPermissions($anggotaPermissions);

        // ketua himpunan
        $roles[3]->syncPermissions($administratorPermissions);

        // wakahim, bendahara, sekretaris
        $roles[4]->syncPermissions($staffPermissions);
        $roles[5]->syncPermissions($staffPermissions);
        $roles[6]->syncPermissions($staffPermissions);
    }
}
