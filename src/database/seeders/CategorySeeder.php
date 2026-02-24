<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = config('category.names');

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        };
    }
}
