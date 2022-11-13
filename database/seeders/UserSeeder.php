<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Admin Users
        \App\Models\User::factory(1)->create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'firstName' => 'Attila',
            'lastName' => 'Odri',
        ])->each(function($user) {
            $user->assignRole('admin');
        });

        // Pavle
        \App\Models\User::factory(1)->create([
            'username' => 'pavle',
            'email' => 'pavle@example.com',
            'firstName' => 'Pavle',
            'lastName' => 'Osmajic',
        ])->each(function($user) {
            $user->assignRole('admin');
        });

        // Media Buyer
        $count = 1;
        \App\Models\User::factory(2)->create()->each(function($user) use(&$count) {
            $user->assignRole('staff');

            $user->username = 'staff' . $count++;
            $user->save();
        });
    }
}
