<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Faker\Factory as Faker;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ja_JP');

        $users = User::all();

        foreach ($users as $user) {
            $user->address()->create([
                'user_id'     => $user->id,
                'postal_code' => $faker->postcode(),
                'address'     => $faker->prefecture()
                                . $faker->city()
                                . $faker->streetAddress(),
                'building'    => $faker->secondaryAddress(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }
}