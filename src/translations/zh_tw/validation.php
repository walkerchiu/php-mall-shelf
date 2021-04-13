<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MallShelf: Validation
    |--------------------------------------------------------------------------
    |
    */

    'catalog_enabled'      => '所屬規格必須先被啟用。',
    'product_enabled'      => '所屬品項必須先被啟用。',
    'product_catalog_nums' => '每個品項最多僅可新增'. config('wk-core.class.mall-shelf.product_catalog_nums') .'筆規格。'
];
