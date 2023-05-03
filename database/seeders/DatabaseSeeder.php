<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\division;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $div = new Division();
        $div -> name = 'Work';
        $div -> save();

        $user = new User();
        $user -> username = 'admin';
        $user -> password = bcrypt('admin');
        $user -> role = 'admin';
        $user -> division_id = 1;
        $user -> save();

        $user = new User();
        $user -> username = 'user';
        $user -> password = bcrypt('user');
        $user -> role = 'user';
        $user -> division_id = 1;
        $user -> save();

    }
}
