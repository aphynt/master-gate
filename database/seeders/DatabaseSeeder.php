<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'UUID' => (string) Uuid::uuid4()->toString(),
        //     'STATUSENABLED' => true,
        //     'NRP' => 'IT002',
        //     'NAME' => 'Administrator2',
        //     'ROLE' => 'ADMIN',
        //     'PASSWORD' => Hash::make('1234qwer'),
        // ]);
    }
}
