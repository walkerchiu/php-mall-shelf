<?php

namespace WalkerChiu\MallShelf\Models\Services;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Services\CheckExistTrait;

class CatalogService
{
    use CheckExistTrait;

    protected $repository;

    public function __construct()
    {
        $this->repository = App::make(config('wk-core.class.mall-shelf.catalogRepository'));
    }
}
