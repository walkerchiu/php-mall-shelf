<?php

namespace WalkerChiu\MallShelf\Models\Services;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Exceptions\NotFoundEntityException;
use WalkerChiu\Core\Models\Services\CheckExistTrait;

class StockService
{
    use CheckExistTrait;

    protected $repository;

    public function __construct()
    {
        $this->repository = App::make(config('wk-core.class.mall-shelf.stockRepository'));
    }

    /*
    |--------------------------------------------------------------------------
    | Get Stock
    |--------------------------------------------------------------------------
    */

    /**
     * @param Int $stock_id
     * @return Stock
     */
    public function find(Int $stock_id)
    {
        $entity = $this->repository->find($stock_id);

        if (empty($entity))
            throw new NotFoundEntityException($entity);

        return $entity;
    }

    /**
     * @param Stock|Int $source
     * @return Stock
     */
    public function findBySource($source)
    {
        if (is_integer($source))
            $entity = $this->find($source);
        elseif (is_a($source, config('wk-core.class.mall-shelf.stock')))
            $entity = $source;
        else
            throw new NotExpectedEntityException($source);

        return $entity;
    }



    /*
    |--------------------------------------------------------------------------
    | Operation
    |--------------------------------------------------------------------------
    */

    /**
     * @param String $sku
     * @param Int    $id
     * @return Boolean
     */
    public function checkExistSKU(String $sku, $id = null)
    {
        return $this->repository->where('sku', '=', $sku)
                                ->when($id, function ($query, $id) {
                                    return $query->where('id', '<>', $id);
                                  })
                                ->exists();
    }

    /**
     * @param Stock  $entity
     * @param String $code
     * @return Array
     */
    public function showForItem($entity, $code)
    {
        $data = $this->repository->showForFrontend($entity, $code);
        $data['stock']['product']  = $data['product'];
        $data['stock']['catalogs'] = $data['catalog'];

        return $data['stock'];
    }
}
