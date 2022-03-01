<?php

namespace WalkerChiu\MallShelf\Models\Forms;

use Illuminate\Support\Facades\Request;
use WalkerChiu\Core\Models\Forms\FormRequest;

class CatalogFormRequest extends FormRequest
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
            'product_id'  => trans('php-mall-shelf::catalog.product_id'),
            'serial'      => trans('php-mall-shelf::catalog.serial'),
            'color'       => trans('php-mall-shelf::catalog.color'),
            'size'        => trans('php-mall-shelf::catalog.size'),
            'material'    => trans('php-mall-shelf::catalog.material'),
            'taste'       => trans('php-mall-shelf::catalog.taste'),
            'weight'      => trans('php-mall-shelf::catalog.weight'),
            'length'      => trans('php-mall-shelf::catalog.length'),
            'width'       => trans('php-mall-shelf::catalog.width'),
            'height'      => trans('php-mall-shelf::catalog.height'),
            'cost'        => trans('php-mall-shelf::catalog.cost'),
            'covers'      => trans('php-mall-shelf::catalog.covers'),
            'images'      => trans('php-mall-shelf::catalog.images'),
            'videos'      => trans('php-mall-shelf::catalog.videos'),
            'is_enabled'  => trans('php-mall-shelf::catalog.is_enabled'),
            'name'        => trans('php-mall-shelf::catalog.name'),
            'description' => trans('php-mall-shelf::catalog.description'),
            'keywords'    => trans('php-mall-shelf::catalog.keywords'),
            'remarks'     => trans('php-mall-shelf::catalog.remarks')
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
            'product_id'  => ['required','string','exists:'.config('wk-core.table.mall-shelf.products').',id'],
            'serial'      => '',
            'color'       => 'nullable|string',
            'size'        => 'nullable|string',
            'material'    => 'nullable|string',
            'taste'       => 'nullable|string',
            'weight'      => 'nullable|numeric|min:0|not_in:0',
            'length'      => 'nullable|numeric|min:0|not_in:0',
            'width'       => 'nullable|numeric|min:0|not_in:0',
            'height'      => 'nullable|numeric|min:0|not_in:0',
            'cost'        => 'nullable|numeric|min:0|not_in:0',
            'covers'      => 'nullable|json',
            'images'      => 'nullable|json',
            'videos'      => 'nullable|json',
            'is_enabled'  => 'required|boolean',

            'name'        => 'required|string|max:255',
            'description' => '',
            'keywords'    => '',
            'remarks'     => ''
        ];

        $request = Request::instance();
        if (
            $request->isMethod('put')
            && isset($request->id)
        ) {
            $rules = array_merge($rules, ['id' => ['required','string','exists:'.config('wk-core.table.mall-shelf.catalogs').',id']]);
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
            'id.required'         => trans('php-core::validation.required'),
            'id.string'           => trans('php-core::validation.string'),
            'id.exists'           => trans('php-core::validation.exists'),
            'product_id.required' => trans('php-core::validation.required'),
            'product_id.string'   => trans('php-core::validation.string'),
            'product_id.exists'   => trans('php-core::validation.exists'),
            'color.string'        => trans('php-core::validation.string'),
            'size.string'         => trans('php-core::validation.string'),
            'material.string'     => trans('php-core::validation.string'),
            'taste.string'        => trans('php-core::validation.string'),
            'weight.numeric'      => trans('php-core::validation.numeric'),
            'weight.min'          => trans('php-core::validation.min'),
            'weight.not_in'       => trans('php-core::validation.not_in'),
            'length.numeric'      => trans('php-core::validation.numeric'),
            'length.min'          => trans('php-core::validation.min'),
            'length.not_in'       => trans('php-core::validation.not_in'),
            'width.numeric'       => trans('php-core::validation.numeric'),
            'width.min'           => trans('php-core::validation.min'),
            'width.not_in'        => trans('php-core::validation.not_in'),
            'height.numeric'      => trans('php-core::validation.numeric'),
            'height.min'          => trans('php-core::validation.min'),
            'height.not_in'       => trans('php-core::validation.not_in'),
            'cost.numeric'        => trans('php-core::validation.numeric'),
            'cost.min'            => trans('php-core::validation.min'),
            'cost.not_in'         => trans('php-core::validation.not_in'),
            'covers.json'         => trans('php-core::validation.json'),
            'images.json'         => trans('php-core::validation.json'),
            'videos.json'         => trans('php-core::validation.json'),
            'is_enabled.required' => trans('php-core::validation.required'),
            'is_enabled.boolean'  => trans('php-core::validation.boolean'),

            'name.required'       => trans('php-core::validation.required'),
            'name.string'         => trans('php-core::validation.string'),
            'name.max'            => trans('php-core::validation.max')
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
            $attributes = $this->attributes();
            $data = $validator->getData();

            $request = Request::instance();
            if (
                isset($data['product_id'])
                && $request->isMethod('post')
            ) {
                $count = config('wk-core.class.mall-shelf.catalog')::where('product_id', $data['product_id'])
                                ->count();
                if ($count >= config('wk-mall-shelf.product_catalog_nums'))
                    $validator->errors()->add('product_id', trans('php-mall-shelf::validation.product_catalog_nums'));
            }

            if (
                empty($data['color'])
                && empty($data['size'])
                && empty($data['material'])
                && empty($data['taste'])
                && empty($data['weight'])
                && empty($data['length'])
                && empty($data['width'])
                && empty($data['height'])
            ) {
                $validator->errors()->add('color',    trans('php-core::validation.required_atleast', ['attribute' => $attributes['color']]));
                $validator->errors()->add('size',     trans('php-core::validation.required_atleast', ['attribute' => $attributes['size']]));
                $validator->errors()->add('material', trans('php-core::validation.required_atleast', ['attribute' => $attributes['material']]));
                $validator->errors()->add('taste', trans('php-core::validation.required_atleast', ['attribute' => $attributes['taste']]));
                $validator->errors()->add('weight',   trans('php-core::validation.required_atleast', ['attribute' => $attributes['weight']]));
                $validator->errors()->add('length',   trans('php-core::validation.required_atleast', ['attribute' => $attributes['length']]));
                $validator->errors()->add('width',    trans('php-core::validation.required_atleast', ['attribute' => $attributes['width']]));
                $validator->errors()->add('height',   trans('php-core::validation.required_atleast', ['attribute' => $attributes['height']]));
            }
        });
    }
}
