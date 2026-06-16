<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
            [
                'name' => 'Reporter User',
                'email' => 'reporter@example.com',
                'password' => Hash::make('password'),
                'role' => 'reporter',
            ],
            [
                'name' => 'Receiver User',
                'email' => 'receiver@example.com',
                'password' => Hash::make('password'),
                'role' => 'receiver',
            ],
            [
                'name' => 'Executor User',
                'email' => 'executor@example.com',
                'password' => Hash::make('password'),
                'role' => 'executor',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(['email' => $userData['email']], $userData);
        }
    }
}
