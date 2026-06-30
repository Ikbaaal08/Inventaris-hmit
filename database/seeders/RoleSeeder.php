<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = collect([
            ['name' => 'Administrator'],
            ['name' => 'Staff Himpunan'],
            ['name' => 'Anggota'],
            ['name' => 'Ketua Himpunan'],
            ['name' => 'Wakil Ketua Himpunan'],
            ['name' => 'Bendahara'],
            ['name' => 'Sekretaris'],
        ]);

        $roles = $roles->map(function ($role) {
            $role['guard_name'] = 'web';
            $role['created_at'] = now();
            $role['updated_at'] = $role['created_at'];

            return $role;
        });

        Role::insert($roles->toArray());
    }
}
