<?php

namespace WalkerChiu\MallShelf\Models\Forms;

trait StockFormTrait
{
    /*
    |--------------------------------------------------------------------------
    | Check Exist on SKU
    |--------------------------------------------------------------------------
    */

    /**
     * @param String $host_type
     * @param Int    $host_id
     * @param Int    $id
     * @param Any    $value
     * @return Boolean
     */
    public function checkExistSKU($host_type, $host_id, $id, $value)
    {
        return $this->baseQueryForForm($host_type, $host_id, $id)
                    ->where('sku', $value)
                    ->exists();
    }

    /**
     * @param String $host_type
     * @param Int    $host_id
     * @param Int    $id
     * @param Any    $value
     * @return Boolean
     */
    public function checkExistSKUOfEnabled($host_type, $host_id, $id, $value)
    {
        return $this->baseQueryForForm($host_type, $host_id, $id)
                    ->where('sku', $value)
                    ->ofEnabled()
                    ->exists();
    }
}
