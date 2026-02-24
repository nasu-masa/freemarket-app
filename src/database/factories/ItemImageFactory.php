<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ItemImageFactory extends Factory
{
    public function definition()
    {
        return [
            'image_path' => 'test.jpg',
        ];
    }
}
