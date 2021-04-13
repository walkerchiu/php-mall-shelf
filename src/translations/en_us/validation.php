<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MallShelf: Validation
    |--------------------------------------------------------------------------
    |
    */

    'catalog_enabled'      => 'Catalog must be enabled first.',
    'product_enabled'      => 'Product must be enabled first.',
    'product_catalog_nums' => 'Each product can only add up to '. config('wk-core.class.mall-shelf.product_catalog_nums') .' catalog|Each product can only add up to '. config('wk-core.class.mall-shelf.product_catalog_nums') .' catalogs'
];
