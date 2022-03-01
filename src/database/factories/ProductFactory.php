<?php

/** @var \Illuminate\Database\Eloquent\Factory  $factory */

use Faker\Generator as Faker;
use WalkerChiu\MallShelf\Models\Entities\Product;
use WalkerChiu\MallShelf\Models\Entities\ProductLang;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'serial'     => $faker->isbn10,
        'identifier' => $faker->slug,
        'cost'       => $faker->randomFloat($nbMaxDecimals = null, $min = 0, $max = 10),
        'price_base' => $faker->randomNumber
    ];
});

$factory->define(ProductLang::class, function (Faker $faker) {
    return [
        'code'  => $faker->locale,
        'key'   => $faker->randomElement(['name', 'description']),
        'value' => $faker->sentence
    ];
});
