<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@ebt.com'],
            [
                'name'              => 'Administrador EBT',
                'role'              => 'admin',
                'password'          => Hash::make('admin'),
                'email_verified_at' => now(),
                'remember_token'    => Str::random(10),
            ]
        );
    }
}
