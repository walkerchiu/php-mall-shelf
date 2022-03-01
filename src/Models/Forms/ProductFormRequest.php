<?php

namespace WalkerChiu\MallShelf\Models\Forms;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use WalkerChiu\Core\Models\Forms\FormRequest;

class ProductFormRequest extends FormRequest
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
            'host_type'     => trans('php-mall-shelf::product.host_type'),
            'host_id'       => trans('php-mall-shelf::product.host_id'),
            'type'          => trans('php-mall-shelf::product.type'),
            'attribute_set' => trans('php-mall-shelf::product.attribute_set'),
            'serial'        => trans('php-mall-shelf::product.serial'),
            'identifier'    => trans('php-mall-shelf::product.identifier'),
            'cost'          => trans('php-mall-shelf::product.cost'),
            'price_base'    => trans('php-mall-shelf::product.price_base'),
            'covers'        => trans('php-mall-shelf::product.covers'),
            'images'        => trans('php-mall-shelf::product.images'),
            'videos'        => trans('php-mall-shelf::product.videos'),
            'is_enabled'    => trans('php-mall-shelf::product.is_enabled'),
            'name'          => trans('php-mall-shelf::product.name'),
            'abstract'      => trans('php-mall-shelf::product.abstract'),
            'description'   => trans('php-mall-shelf::product.description'),
            'keywords'      => trans('php-mall-shelf::product.keywords'),
            'remarks'       => trans('php-mall-shelf::product.remarks')
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
            'host_type'     => 'required_with:host_id|string',
            'host_id'       => 'required_with:host_type|string',
            'type'          => '',
            'attribute_set' => '',
            'serial'        => '',
            'identifier'    => 'required|string|max:255',
            'cost'          => 'nullable|numeric|min:0|not_in:0',
            'price_base'    => 'nullable|numeric|min:0|not_in:0',
            'covers'        => 'nullable|json',
            'images'        => 'nullable|json',
            'videos'        => 'nullable|json',
            'is_enabled'    => 'required|boolean',

            'name'        => 'required|string|max:255',
            'abstract'    => '',
            'description' => '',
            'keywords'    => '',
            'remarks'     => ''
        ];

        $request = Request::instance();
        if (
            $request->isMethod('put')
            && isset($request->id)
        ) {
            $rules = array_merge($rules, ['id' => ['required','string','exists:'.config('wk-core.table.mall-shelf.products').',id']]);
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
            'id.required'             => trans('php-core::validation.required'),
            'id.string'               => trans('php-core::validation.string'),
            'id.exists'               => trans('php-core::validation.exists'),
            'host_type.required_with' => trans('php-core::validation.required_with'),
            'host_type.string'        => trans('php-core::validation.string'),
            'host_id.required_with'   => trans('php-core::validation.required_with'),
            'host_id.string'          => trans('php-core::validation.string'),
            'identifier.required'     => trans('php-core::validation.required'),
            'identifier.max'          => trans('php-core::validation.max'),
            'cost.numeric'            => trans('php-core::validation.numeric'),
            'cost.min'                => trans('php-core::validation.min'),
            'cost.not_in'             => trans('php-core::validation.not_in'),
            'price_base.numeric'      => trans('php-core::validation.numeric'),
            'price_base.min'          => trans('php-core::validation.min'),
            'price_base.not_in'       => trans('php-core::validation.not_in'),
            'covers.json'             => trans('php-core::validation.json'),
            'images.json'             => trans('php-core::validation.json'),
            'videos.json'             => trans('php-core::validation.json'),
            'is_enabled.required'     => trans('php-core::validation.required'),
            'is_enabled.boolean'      => trans('php-core::validation.boolean'),

            'name.required'           => trans('php-core::validation.required'),
            'name.string'             => trans('php-core::validation.string'),
            'name.max'                => trans('php-core::validation.max')
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
            if (
                isset($data['host_type'])
                && isset($data['host_id'])
            ) {
                if (
                    config('wk-mall-shelf.onoff.site')
                    && !empty(config('wk-core.class.site.site'))
                    && $data['host_type'] == config('wk-core.class.site.site')
                ) {
                    $result = DB::table(config('wk-core.table.site.sites'))
                                ->where('id', $data['host_id'])
                                ->exists();
                    if (!$result)
                        $validator->errors()->add('host_id', trans('php-core::validation.exists'));

                }
            }
            if (isset($data['identifier'])) {
                $result = config('wk-core.class.mall-shelf.product')::where('identifier', $data['identifier'])
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
                    $validator->errors()->add('identifier', trans('php-core::validation.unique', ['attribute' => trans('php-mall-shelf::product.identifier')]));
            }
        });
    }
}
