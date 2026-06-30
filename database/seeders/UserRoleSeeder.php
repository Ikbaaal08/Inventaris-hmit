<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleIds = DB::table('roles')->pluck('id', 'name');
        $users = DB::table('users')->select('id', 'email')->get();
        $modelHasRoles = [];

        foreach ($users as $user) {
            $roleName = null;

            if ($user->email === 'admin@mail.com') {
                $roleName = 'Administrator';
            } elseif ($user->email === 'kahim@mail.com') {
                $roleName = 'Ketua Himpunan';
            } elseif ($user->email === 'wakahim@mail.com') {
                $roleName = 'Wakil Ketua Himpunan';
            } elseif ($user->email === 'bendahara@mail.com') {
                $roleName = 'Bendahara';
            } elseif ($user->email === 'sekretaris@mail.com') {
                $roleName = 'Sekretaris';
            } elseif (str_starts_with($user->email, 'staff')) {
                $roleName = 'Staff Himpunan';
            } elseif (str_starts_with($user->email, 'anggota')) {
                $roleName = 'Anggota';
            }

            if ($roleName && isset($roleIds[$roleName])) {
                $modelHasRoles[] = [
                    'role_id' => $roleIds[$roleName],
                    'model_type' => 'App\User',
                    'model_id' => $user->id,
                ];
            }
        }

        DB::table('model_has_roles')->insert($modelHasRoles);
    }
}
