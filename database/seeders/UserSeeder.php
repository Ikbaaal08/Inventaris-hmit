<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $password = Hash::make('secret');
        $now = now();
        $users = [];

        // 1. Administrator
        $users[] = [
            'name' => 'Administrator',
            'email' => 'admin@mail.com',
            'password' => $password,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        // 2. Ketua Himpunan
        $users[] = [
            'name' => 'Ketua Himpunan',
            'email' => 'kahim@mail.com',
            'password' => $password,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        // 3. Wakil Ketua Himpunan
        $users[] = [
            'name' => 'Wakil Ketua Himpunan',
            'email' => 'wakahim@mail.com',
            'password' => $password,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        // 4. Bendahara Himpunan
        $users[] = [
            'name' => 'Bendahara Himpunan',
            'email' => 'bendahara@mail.com',
            'password' => $password,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        // 5. Sekretaris Himpunan
        $users[] = [
            'name' => 'Sekretaris Himpunan',
            'email' => 'sekretaris@mail.com',
            'password' => $password,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        // 6. 40 Staff Himpunan
        for ($i = 1; $i <= 40; $i++) {
            $users[] = [
                'name' => 'Staff Himpunan ' . $i,
                'email' => 'staff' . $i . '@mail.com',
                'password' => $password,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // 7. 500 Anggota Himpunan
        for ($i = 1; $i <= 500; $i++) {
            $users[] = [
                'name' => 'Anggota Himpunan ' . $i,
                'email' => 'anggota' . $i . '@mail.com',
                'password' => $password,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('users')->insert($users);
    }
}
