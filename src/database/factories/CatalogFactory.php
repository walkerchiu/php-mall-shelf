<?php

/** @var \Illuminate\Database\Eloquent\Factory  $factory */

use Faker\Generator as Faker;
use WalkerChiu\MallShelf\Models\Entities\Catalog;
use WalkerChiu\MallShelf\Models\Entities\CatalogLang;

$factory->define(Catalog::class, function (Faker $faker) {
    return [
        'serial' => $faker->isbn10,
        'color'  => $faker->colorName,
        'weight' => $faker->randomFloat($nbMaxDecimals = null, $min = 0, $max = 10),
        'length' => $faker->randomFloat($nbMaxDecimals = null, $min = 0, $max = 10),
        'width'  => $faker->randomFloat($nbMaxDecimals = null, $min = 0, $max = 10),
        'height' => $faker->randomFloat($nbMaxDecimals = null, $min = 0, $max = 10),
        'cost'   => $faker->randomFloat($nbMaxDecimals = null, $min = 0, $max = 10)
    ];
});

$factory->define(CatalogLang::class, function (Faker $faker) {
    return [
        'code'  => $faker->locale,
        'key'   => $faker->randomElement(['name', 'description']),
        'value' => $faker->sentence
    ];
});
