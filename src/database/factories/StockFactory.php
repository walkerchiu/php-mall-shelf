<?php

/** @var \Illuminate\Database\Eloquent\Factory  $factory */

use Faker\Generator as Faker;
use WalkerChiu\MallShelf\Models\Entities\Stock;
use WalkerChiu\MallShelf\Models\Entities\StockLang;

$factory->define(Stock::class, function (Faker $faker) {
    return [
        'sku'            => $faker->isbn10,
        'identifier'     => $faker->slug,
        'product_id'     => 1,
        'catalog_id'     => 1,
        'cost'           => $faker->randomFloat($nbMaxDecimals = null, $min = 0, $max = 10),
        'price_original' => $faker->randomFloat,
        'price_discount' => $faker->randomFloat,
        'inventory'      => 3,
        'quantity'       => 3
    ];
});

$factory->define(StockLang::class, function (Faker $faker) {
    return [
        'code'  => $faker->locale,
        'key'   => $faker->randomElement(['name', 'description']),
        'value' => $faker->sentence
    ];
});
