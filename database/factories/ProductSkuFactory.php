<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ProductSku;
use Faker\Generator as Faker;

$factory->define(ProductSku::class, function (Faker $faker) {
    return [
        'title'=>$faker->word,
        'description'=>$faker->sentence,
        'price'=>$faker->numberBetween(1,99999),
        'stock'=>$faker->randomNumber(3)
    ];
});
