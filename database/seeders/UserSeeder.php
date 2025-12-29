<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'kayky@gmail.com'],
            [
                'name' => 'Kayky',
                'password' => Hash::make('123'),
                'is_admin' => true,
                'photo' => 'https://res.cloudinary.com/ddfgsoh5g/image/upload/pedbook/profiles/default-user',
            ]
        );
    }
}
