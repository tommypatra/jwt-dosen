<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $dtdef = [
            ['name' => 'H. Nur Alim', 'email' => 'nuralim@app.com'],
            ['name' => 'H. Pairin', 'email' => 'pairin@app.com'],
            ['name' => 'H. Yahya Obaid', 'email' => 'yahya@app.com'],
        ];

        foreach ($dtdef as $dt) {
            User::create([
                'name' => $dt['name'],
                'email' => $dt['email'],
                'password' => Hash::make('00000000'),
            ]);
        }
    }
}
