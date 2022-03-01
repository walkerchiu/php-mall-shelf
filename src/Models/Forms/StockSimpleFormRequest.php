<?php

namespace WalkerChiu\MallShelf\Models\Forms;

use Illuminate\Support\Facades\Request;
use WalkerChiu\MallShelf\Models\Forms\StockFormRequest;

class StockSimpleFormRequest extends StockFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return Array
     */
    public function rules()
    {
        $rules = [
            'host_type'         => 'required_with:host_id|string',
            'host_id'           => 'required_with:host_type|string',
            'product_id'        => ['nullable','string','exists:'.config('wk-core.table.mall-shelf.products').',id'],
            'catalog_id'        => ['nullable','string','exists:'.config('wk-core.table.mall-shelf.catalogs').',id'],
            'type'              => '',
            'sku'               => '',
            'identifier'        => 'required|string|max:255',
            'cost'              => 'nullable|numeric|min:0|not_in:0',
            'price_original'    => 'nullable|numeric|min:0|not_in:0',
            'price_discount'    => 'nullable|numeric|min:0|not_in:0|required_if:is_sellable,1',
            'options'           => 'nullable|json',
            'covers'            => 'nullable|json',
            'images'            => 'nullable|json',
            'videos'            => 'nullable|json',
            'inventory'         => 'nullable|numeric|min:0|gte:quantity',
            'quantity'          => 'nullable|numeric|min:0|lte:inventory',
            'qty_per_order'     => 'nullable|numeric|min:0|not_in:0',
            'fee'               => 'nullable|numeric|min:0',
            'tax'               => 'nullable|numeric|min:0',
            'tip'               => 'nullable|numeric|min:0',
            'weight'            => 'nullable|numeric|min:0',
            'binding_supported' => 'nullable|json',
            'recommendation'    => 'nullable|json',
            'is_new'            => 'required|boolean',
            'is_featured'       => 'required|boolean',
            'is_highlighted'    => 'required|boolean',
            'is_sellable'       => 'required|boolean',
            'is_enabled'        => 'required|boolean',

            'name'        => 'required|string|max:255',
            'abstract'    => '',
            'description' => '',
            'keywords'    => '',
            'remarks'     => ''
        ];

        $request = Request::instance();

        if (
            is_null($request->inventory)
            || is_null($request->quantity)
        ) {
            $rules['inventory'] = 'nullable|numeric|min:0';
            $rules['quantity']  = 'nullable|numeric|min:0';
        }

        if (
            $request->isMethod('put')
            && isset($request->id)
        ) {
            $rules = array_merge($rules, ['id' => ['required','string','exists:'.config('wk-core.table.mall-shelf.stocks').',id']]);
        }

        return $rules;
    }
}
