<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Coupon;
use Faker\Generator as Faker;

$factory->define(Coupon::class, function (Faker $faker) {
    $type = $faker->randomElement(array_keys(Coupon::$typeMap));

    $value = $type == Coupon::TYPE_FIXED ? random_int(1, 100) : random_int(1, 50);

    if ($type === Coupon::TYPE_FIXED) {
        $min_amount = $value + 0.01;
    } else {
        if (random_int(1, 100) > 50) {
            $min_amount = 0;
        } else {
            $min_amount = random_int(100, 2000);
        }
    }
    return [
        'name' => join(' ', $faker->words),
        'code'=>Coupon::createCode(),
        'type'=>$type,
        'value'=>$value,
        'min_amount'=>$min_amount,
        'start_time'=>date('Y-m-d H:i:s'),
        'end_time'=>date('Y-m-d H:i:s'),
        'enable'=>1,
        'total'=>1000,
    ];
});
