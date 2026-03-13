<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $ceoRole = Role::where('name', 'CEO')->first();

        User::create([
            'name' => 'CEO Admin',
            'email' => 'ceo@ipms.com',
            'password' => Hash::make('password'),
            'role_id' => $ceoRole->id
        ]);
    }
}