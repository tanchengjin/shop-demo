<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
        'description' => $faker->sentence,
        'image' => asset('images/350.png'),
        'category_id' => (\App\Category::query()->inRandomOrder()->first())->id,
        'min_price' => 0,
        'max_price' => 0
    ];
});
