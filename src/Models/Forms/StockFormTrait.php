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
     * @param String  $host_type
     * @param String  $host_id
     * @param String  $id
     * @param Mixed   $value
     * @return Bool
     */
    public function checkExistSKU($host_type, $host_id, $id, $value): bool
    {
        return $this->baseQueryForForm($host_type, $host_id, $id, null, null)
                    ->where('sku', $value)
                    ->exists();
    }

    /**
     * @param String  $host_type
     * @param String  $host_id
     * @param String  $id
     * @param Mixed   $value
     * @return Bool
     */
    public function checkExistSKUOfEnabled($host_type, $host_id, $id, $value): bool
    {
        return $this->baseQueryForForm($host_type, $host_id, $id, null, null)
                    ->where('sku', $value)
                    ->ofEnabled()
                    ->exists();
    }
}
