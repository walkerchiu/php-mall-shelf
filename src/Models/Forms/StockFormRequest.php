<?php

namespace WalkerChiu\MallShelf\Models\Forms;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use WalkerChiu\Core\Models\Forms\FormRequest;

class StockFormRequest extends FormRequest
{
    /**
     * @Override Illuminate\Foundation\Http\FormRequest::getValidatorInstance
     */
    protected function getValidatorInstance()
    {
        $request = Request::instance();
        $data = $this->all();
        if (
            $request->isMethod('put')
            && empty($data['id'])
            && isset($request->id)
        ) {
            $data['id'] = (string) $request->id;
            $this->getInputSource()->replace($data);
        }

        return parent::getValidatorInstance();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return Array
     */
    public function attributes()
    {
        return [
            'host_type'         => trans('php-mall-shelf::stock.host_type'),
            'host_id'           => trans('php-mall-shelf::stock.host_id'),
            'product_id'        => trans('php-mall-shelf::stock.product_id'),
            'catalog_id'        => trans('php-mall-shelf::stock.catalog_id'),
            'type'              => trans('php-mall-shelf::stock.type'),
            'attribute_set'     => trans('php-mall-shelf::stock.attribute_set'),
            'sku'               => trans('php-mall-shelf::stock.sku'),
            'identifier'        => trans('php-mall-shelf::stock.identifier'),
            'cost'              => trans('php-mall-shelf::stock.cost'),
            'price_original'    => trans('php-mall-shelf::stock.price_original'),
            'price_discount'    => trans('php-mall-shelf::stock.price_discount'),
            'options'           => trans('php-mall-shelf::stock.options'),
            'covers'            => trans('php-mall-shelf::stock.covers'),
            'images'            => trans('php-mall-shelf::stock.images'),
            'videos'            => trans('php-mall-shelf::stock.videos'),
            'inventory'         => trans('php-mall-shelf::stock.inventory'),
            'quantity'          => trans('php-mall-shelf::stock.quantity'),
            'qty_per_order'     => trans('php-mall-shelf::stock.qty_per_order'),
            'fee'               => trans('php-mall-shelf::stock.fee'),
            'tax'               => trans('php-mall-shelf::stock.tax'),
            'tip'               => trans('php-mall-shelf::stock.tip'),
            'weight'            => trans('php-mall-shelf::stock.weight'),
            'binding_supported' => trans('php-mall-shelf::stock.binding_supported'),
            'recommendation'    => trans('php-mall-shelf::stock.recommendation'),
            'is_new'            => trans('php-mall-shelf::stock.is_new'),
            'is_featured'       => trans('php-mall-shelf::stock.is_featured'),
            'is_highlighted'    => trans('php-mall-shelf::stock.is_highlighted'),
            'is_sellable'       => trans('php-mall-shelf::stock.is_sellable'),
            'is_enabled'        => trans('php-mall-shelf::stock.is_enabled'),
            'name'              => trans('php-mall-shelf::stock.name'),
            'abstract'          => trans('php-mall-shelf::stock.abstract'),
            'description'       => trans('php-mall-shelf::stock.description'),
            'keywords'          => trans('php-mall-shelf::stock.keywords'),
            'remarks'           => trans('php-mall-shelf::stock.remarks')
        ];
    }

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
            'product_id'        => ['required','string','exists:'.config('wk-core.table.mall-shelf.products').',id'],
            'catalog_id'        => ['nullable','string','exists:'.config('wk-core.table.mall-shelf.catalogs').',id'],
            'type'              => '',
            'attribute_set'     => '',
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

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return Array
     */
    public function messages()
    {
        return [
            'id.required'                => trans('php-core::validation.required'),
            'id.string'                  => trans('php-core::validation.string'),
            'id.exists'                  => trans('php-core::validation.exists'),
            'host_type.required_with'    => trans('php-core::validation.required_with'),
            'host_type.string'           => trans('php-core::validation.string'),
            'host_id.required_with'      => trans('php-core::validation.required_with'),
            'host_id.string'             => trans('php-core::validation.string'),
            'product_id.required'        => trans('php-core::validation.required'),
            'product_id.string'          => trans('php-core::validation.string'),
            'product_id.exists'          => trans('php-core::validation.exists'),
            'catalog_id.string'          => trans('php-core::validation.string'),
            'catalog_id.exists'          => trans('php-core::validation.exists'),
            'identifier.required'        => trans('php-core::validation.required'),
            'identifier.max'             => trans('php-core::validation.max'),
            'cost.numeric'               => trans('php-core::validation.numeric'),
            'cost.min'                   => trans('php-core::validation.min'),
            'cost.not_in'                => trans('php-core::validation.not_in'),
            'price_original.numeric'     => trans('php-core::validation.numeric'),
            'price_original.min'         => trans('php-core::validation.min'),
            'price_original.not_in'      => trans('php-core::validation.not_in'),
            'price_discount.required_if' => trans('php-core::validation.required_if'),
            'price_discount.numeric'     => trans('php-core::validation.numeric'),
            'price_discount.min'         => trans('php-core::validation.min'),
            'price_discount.not_in'      => trans('php-core::validation.not_in'),
            'options.json'               => trans('php-core::validation.json'),
            'covers.json'                => trans('php-core::validation.json'),
            'images.json'                => trans('php-core::validation.json'),
            'videos.json'                => trans('php-core::validation.json'),
            'inventory.numeric'          => trans('php-core::validation.numeric'),
            'inventory.min'              => trans('php-core::validation.min'),
            'inventory.gte'              => trans('php-core::validation.gte'),
            'quantity.numeric'           => trans('php-core::validation.numeric'),
            'quantity.min'               => trans('php-core::validation.min'),
            'quantity.lte'               => trans('php-core::validation.lte'),
            'qty_per_order.numeric'      => trans('php-core::validation.numeric'),
            'qty_per_order.min'          => trans('php-core::validation.min'),
            'qty_per_order.not_in'       => trans('php-core::validation.not_in'),
            'fee.numeric'                => trans('php-core::validation.numeric'),
            'fee.min'                    => trans('php-core::validation.min'),
            'tax.numeric'                => trans('php-core::validation.numeric'),
            'tax.min'                    => trans('php-core::validation.min'),
            'tip.numeric'                => trans('php-core::validation.numeric'),
            'tip.min'                    => trans('php-core::validation.min'),
            'weight.numeric'             => trans('php-core::validation.numeric'),
            'weight.min'                 => trans('php-core::validation.min'),
            'binding_supported.json'     => trans('php-core::validation.json'),
            'recommendation.json'        => trans('php-core::validation.json'),
            'is_new.required'            => trans('php-core::validation.boolean'),
            'is_new.boolean'             => trans('php-core::validation.boolean'),
            'is_featured.required'       => trans('php-core::validation.boolean'),
            'is_featured.boolean'        => trans('php-core::validation.boolean'),
            'is_highlighted.required'    => trans('php-core::validation.boolean'),
            'is_highlighted.boolean'     => trans('php-core::validation.boolean'),
            'is_sellable.required'       => trans('php-core::validation.boolean'),
            'is_sellable.boolean'        => trans('php-core::validation.boolean'),
            'is_enabled.required'        => trans('php-core::validation.boolean'),
            'is_enabled.boolean'         => trans('php-core::validation.boolean'),

            'name.required'              => trans('php-core::validation.required'),
            'name.string'                => trans('php-core::validation.string'),
            'name.max'                   => trans('php-core::validation.max')
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after( function ($validator) {
            $data = $validator->getData();
            if ( !empty(config('wk-core.class.site-mall.site')) ) {
                if (
                    isset($data['host_type'])
                    && isset($data['host_id'])
                ) {
                    if (
                        config('wk-mall-shelf.onoff.site-mall')
                        && !empty(config('wk-core.class.site-mall.site'))
                        && $data['host_type'] == config('wk-core.class.site-mall.site')
                    ) {
                        $result = DB::table(config('wk-core.table.site-mall.sites'))
                                    ->where('id', $data['host_id'])
                                    ->exists();
                        if (!$result)
                            $validator->errors()->add('host_id', trans('php-core::validation.exists', ['attribute' => trans('php-mall-shelf::stock.host_id')]));

                    }
                }
            }
            if (isset($data['catalog_id'])) {
                $result = config('wk-core.class.mall-shelf.catalog')::where('id', $data['catalog_id'])
                                                                    ->exists();
                if (!$result)
                    $validator->errors()->add('catalog_id', trans('php-core::validation.exists', ['attribute' => trans('php-mall-shelf::stock.catalog_id')]));

                if (
                    isset($data['is_enabled'])
                    && $data['is_enabled']
                ) {
                    $result = config('wk-core.class.mall-shelf.catalog')::where('id', $data['catalog_id'])
                                                                        ->where('is_enabled', 1)
                                                                        ->exists();
                    if (!$result)
                        $validator->errors()->add('catalog_id', trans('php-mall-shelf::validation.catalog_enabled'));
                }
            }
            if (isset($data['product_id'])) {
                $result = config('wk-core.class.mall-shelf.product')::where('id', $data['product_id'])
                                                                    ->exists();
                if (!$result)
                    $validator->errors()->add('product_id', trans('php-core::validation.exists', ['attribute' => trans('php-mall-shelf::stock.product_id')]));

                if (
                    isset($data['is_enabled'])
                    && $data['is_enabled']
                ) {
                    $result = config('wk-core.class.mall-shelf.product')::where('id', $data['product_id'])
                                                                        ->where('is_enabled', 1)
                                                                        ->exists();
                    if (!$result)
                        $validator->errors()->add('product_id', trans('php-mall-shelf::validation.product_enabled'));
                }
            }
            if (isset($data['identifier'])) {
                $result = config('wk-core.class.mall-shelf.stock')::where('identifier', $data['identifier'])
                                ->when(isset($data['host_type']), function ($query) use ($data) {
                                    return $query->where('host_type', $data['host_type']);
                                  })
                                ->when(isset($data['host_id']), function ($query) use ($data) {
                                    return $query->where('host_id', $data['host_id']);
                                  })
                                ->when(isset($data['id']), function ($query) use ($data) {
                                    return $query->where('id', '<>', $data['id']);
                                  })
                                ->exists();
                if ($result)
                    $validator->errors()->add('identifier', trans('php-core::validation.unique', ['attribute' => trans('php-mall-shelf::stock.identifier')]));
            }
        });
    }
}
