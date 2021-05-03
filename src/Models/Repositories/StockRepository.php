<?php

namespace WalkerChiu\MallShelf\Models\Repositories;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Forms\FormHasHostTrait;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryHasHostTrait;
use WalkerChiu\MallShelf\Models\Forms\StockFormTrait;
use WalkerChiu\MorphComment\Models\Repositories\CommentRepositoryTrait;
use WalkerChiu\MorphImage\Models\Repositories\ImageRepositoryTrait;

class StockRepository extends Repository
{
    use FormHasHostTrait;
    use RepositoryHasHostTrait;
    use StockFormTrait;
    use CommentRepositoryTrait;
    use ImageRepositoryTrait;

    protected $entity;

    public function __construct()
    {
        $this->entity = App::make(config('wk-core.class.mall-shelf.stock'));
    }

    /**
     * @param String  $host_type
     * @param String  $host_id
     * @param String  $code
     * @param Array   $data
     * @param Int     $page
     * @param Int     $nums per page
     * @param Boolean $is_enabled
     * @param String  $target
     * @param Boolean $target_is_enabled
     * @return Array
     */
    public function list($host_type, $host_id, String $code, Array $data, $page = null, $nums = null, $is_enabled = null, $target = null, $target_is_enabled = null)
    {
        $this->assertForPagination($page, $nums);

        if (empty($host_type) || empty($host_id)) {
            $entity = $this->entity;
        } else {
            $entity = $this->baseQueryForRepository($host_type, $host_id, $target, $target_is_enabled);
        }
        if ($is_enabled === true)      $entity = $entity->ofEnabled();
        elseif ($is_enabled === false) $entity = $entity->ofDisabled();

        $records = $entity->with(['langs' => function ($query) use ($code) {
                                $query->ofCurrent()
                                      ->ofCode($code);
                             }])
                            ->when( config('wk-mall-shelf.onoff.morph-tag') && !empty(config('wk-core.class.morph-tag.tag')), function ($query) {
                                return $query->with(['tags', 'tags.langs']);
                            })
                            ->when($data, function ($query, $data) {
                                return $query->unless(empty($data['id']), function ($query) use ($data) {
                                            return $query->where('id', $data['id']);
                                        })
                                        ->unless(empty($data['type']), function ($query) use ($data) {
                                            return $query->where('type', $data['type']);
                                        })
                                        ->unless(empty($data['attribute_set']), function ($query) use ($data) {
                                            return $query->where('attribute_set', $data['attribute_set']);
                                        })
                                        ->unless(empty($data['sku']), function ($query) use ($data) {
                                            return $query->where('sku', $data['sku']);
                                        })
                                        ->unless(empty($data['identifier']), function ($query) use ($data) {
                                            return $query->where('identifier', $data['identifier']);
                                        })
                                        ->unless(empty($data['product_id']), function ($query) use ($data) {
                                            return $query->where('product_id', $data['product_id']);
                                        })
                                        ->unless(empty($data['catalog_id']), function ($query) use ($data) {
                                            return $query->where('catalog_id', $data['catalog_id']);
                                        })
                                        ->unless(empty($data['cost']), function ($query) use ($data) {
                                            return $query->where('cost', $data['cost']);
                                        })
                                        ->unless(empty($data['cost_min']), function ($query) use ($data) {
                                            return $query->where('cost', '>=', $data['cost_min']);
                                        })
                                        ->unless(empty($data['cost_max']), function ($query) use ($data) {
                                            return $query->where('cost', '<=', $data['cost_max']);
                                        })
                                        ->unless(empty($data['price_original']), function ($query) use ($data) {
                                            return $query->where('price_original', $data['price_original']);
                                        })
                                        ->unless(empty($data['price_original_min']), function ($query) use ($data) {
                                            return $query->where('price_original', '>=', $data['price_original_min']);
                                        })
                                        ->unless(empty($data['price_original_max']), function ($query) use ($data) {
                                            return $query->where('price_original', '<=', $data['price_original_max']);
                                        })
                                        ->unless(empty($data['price_discount']), function ($query) use ($data) {
                                            return $query->where('price_discount', $data['price_discount']);
                                        })
                                        ->unless(empty($data['price_discount_min']), function ($query) use ($data) {
                                            return $query->where('price_discount', '>=', $data['price_discount_min']);
                                        })
                                        ->unless(empty($data['price_discount_max']), function ($query) use ($data) {
                                            return $query->where('price_discount', '<=', $data['price_discount_max']);
                                        })
                                        ->unless(empty($data['inventory']), function ($query) use ($data) {
                                            return $query->where('inventory', $data['inventory']);
                                        })
                                        ->unless(empty($data['inventory_min']), function ($query) use ($data) {
                                            return $query->where('inventory', '>=', $data['inventory_min']);
                                        })
                                        ->unless(empty($data['inventory_max']), function ($query) use ($data) {
                                            return $query->where('inventory', '<=', $data['inventory_max']);
                                        })
                                        ->unless(empty($data['quantity']), function ($query) use ($data) {
                                            return $query->where('quantity', $data['quantity']);
                                        })
                                        ->unless(empty($data['quantity_min']), function ($query) use ($data) {
                                            return $query->where('quantity', '>=', $data['quantity_min']);
                                        })
                                        ->unless(empty($data['quantity_max']), function ($query) use ($data) {
                                            return $query->where('quantity', '<=', $data['quantity_max']);
                                        })
                                        ->unless(empty($data['qty_per_order']), function ($query) use ($data) {
                                            return $query->where('qty_per_order', $data['qty_per_order']);
                                        })
                                        ->unless(empty($data['qty_per_order_min']), function ($query) use ($data) {
                                            return $query->where('qty_per_order_min', '>=', $data['qty_per_order_min']);
                                        })
                                        ->unless(empty($data['qty_per_order_max']), function ($query) use ($data) {
                                            return $query->where('qty_per_order_max', '<=', $data['qty_per_order_max']);
                                        })
                                        ->unless(empty($data['fee']), function ($query) use ($data) {
                                            return $query->where('fee', $data['fee']);
                                        })
                                        ->unless(empty($data['fee_min']), function ($query) use ($data) {
                                            return $query->where('fee', '>=', $data['fee_min']);
                                        })
                                        ->unless(empty($data['fee_max']), function ($query) use ($data) {
                                            return $query->where('fee', '<=', $data['fee_max']);
                                        })
                                        ->unless(empty($data['tax']), function ($query) use ($data) {
                                            return $query->where('tax', $data['tax']);
                                        })
                                        ->unless(empty($data['tax_min']), function ($query) use ($data) {
                                            return $query->where('tax', '>=', $data['tax_min']);
                                        })
                                        ->unless(empty($data['tax_max']), function ($query) use ($data) {
                                            return $query->where('tax', '<=', $data['tax_max']);
                                        })
                                        ->unless(empty($data['tip']), function ($query) use ($data) {
                                            return $query->where('tip', $data['tip']);
                                        })
                                        ->unless(empty($data['tip_min']), function ($query) use ($data) {
                                            return $query->where('tip', '>=', $data['tip_min']);
                                        })
                                        ->unless(empty($data['tip_max']), function ($query) use ($data) {
                                            return $query->where('tip', '<=', $data['tip_max']);
                                        })
                                        ->unless(empty($data['weight']), function ($query) use ($data) {
                                            return $query->where('weight', $data['weight']);
                                        })
                                        ->unless(empty($data['weight_min']), function ($query) use ($data) {
                                            return $query->where('weight', '>=', $data['weight_min']);
                                        })
                                        ->unless(empty($data['weight_max']), function ($query) use ($data) {
                                            return $query->where('weight', '<=', $data['weight_max']);
                                        })
                                        ->unless(empty($data['recommendation']), function ($query) use ($data) {
                                            return $query->whereJsonContains('recommendation', $data['recommendation']);
                                        })
                                        ->when(isset($data['is_new']), function ($query) use ($data) {
                                            return $query->where('is_new', $data['is_new']);
                                        })
                                        ->when(isset($data['is_featured']), function ($query) use ($data) {
                                            return $query->where('is_featured', $data['is_featured']);
                                        })
                                        ->when(isset($data['is_highlighted']), function ($query) use ($data) {
                                            return $query->where('is_highlighted', $data['is_highlighted']);
                                        })
                                        ->when(isset($data['is_sellable']), function ($query) use ($data) {
                                            return $query->where('is_sellable', $data['is_sellable']);
                                        })
                                        ->unless(empty($data['name']), function ($query) use ($data) {
                                            return $query->whereHas('langs', function($query) use ($data) {
                                                $query->ofCurrent()
                                                      ->where('key', 'name')
                                                      ->where('value', 'LIKE', "%".$data['name']."%");
                                            });
                                        })
                                        ->unless(empty($data['abstract']), function ($query) use ($data) {
                                            return $query->whereHas('langs', function($query) use ($data) {
                                                $query->ofCurrent()
                                                      ->where('key', 'abstract')
                                                      ->where('value', 'LIKE', "%".$data['abstract']."%");
                                            });
                                        })
                                        ->unless(empty($data['description']), function ($query) use ($data) {
                                            return $query->whereHas('langs', function($query) use ($data) {
                                                $query->ofCurrent()
                                                      ->where('key', 'description')
                                                      ->where('value', 'LIKE', "%".$data['description']."%");
                                            });
                                        })
                                        ->unless(empty($data['keywords']), function ($query) use ($data) {
                                            return $query->whereHas('langs', function($query) use ($data) {
                                                $query->ofCurrent()
                                                      ->where('key', 'keywords')
                                                      ->where('value', 'LIKE', "%".$data['keywords']."%");
                                            });
                                        })
                                        ->unless(empty($data['remarks']), function ($query) use ($data) {
                                            return $query->whereHas('langs', function($query) use ($data) {
                                                $query->ofCurrent()
                                                      ->where('key', 'remarks')
                                                      ->where('value', 'LIKE', "%".$data['remarks']."%");
                                            });
                                        })
                                        ->unless(empty($data['categories']), function ($query) use ($data) {
                                            return $query->whereHas('categories', function($query) use ($data) {
                                                $query->ofEnabled()
                                                      ->whereIn('id', $data['categories']);
                                            });
                                        })
                                        ->unless(empty($data['tags']), function ($query) use ($data) {
                                            return $query->whereHas('tags', function($query) use ($data) {
                                                $query->ofEnabled()
                                                      ->whereIn('id', $data['tags']);
                                            });
                                        })
                                        ->unless(!empty($data['orderBy']) && !empty($data['orderType']), function ($query) use ($data) {
                                            return $query->orderBy($data['orderBy'], $data['orderType']);
                                        }, function ($query) {
                                            return $query->orderBy('updated_at', 'DESC');
                                        });
                            }, function ($query) {
                                return $query->orderBy('updated_at', 'DESC');
                            })
                            ->get()
                            ->when(is_integer($page) && is_integer($nums), function ($query) use ($page, $nums) {
                                return $query->forPage($page, $nums);
                            });
        $list = [];
        foreach ($records as $record) {
            $this->setEntity($record);

            $data = $record->toArray();
            array_push($list,
                array_merge($data, [
                    'name'        => $record->findLangByKey('name'),
                    'abstract'    => $record->findLangByKey('abstract'),
                    'description' => $record->findLangByKey('description'),
                    'keywords'    => $record->findLangByKey('keywords'),
                    'catalog'     => $record->catalog,
                    'covers'      => $this->getlistOfCovers($code)
                ])
            );
        }

        return $list;
    }

    /*
    |--------------------------------------------------------------------------
    | For Auto Complete
    |--------------------------------------------------------------------------
    */

    /**
     * @param String  $host_type
     * @param String  $host_id
     * @param String  $code
     * @param Any     $value
     * @param String  $target
     * @param Boolean $target_is_enabled
     * @return Array
     */
    public function autoCompleteNameOfEnabled($host_type, $host_id, String $code, $value, $count = 10, $target = null, $target_is_enabled = null)
    {
        $records = $this->entity->lang()::with('morph')
                                        ->ofCurrent()
                                        ->ofCodeAndKey($code, 'name')
                                        ->whereHas('host', function($query) use ($host_type, $host_id) {
                                                $query->ofEnabled()
                                                      ->unless(empty($host_type) || empty($host_id), function ($query) use ($host_type, $host_id) {
                                                            return $query->whereHasMorph('host', $host_type, function($query) {
                                                                $query->ofEnabled();
                                                            });
                                                        });
                                           })
                                        ->where('value', 'LIKE', $value .'%')
                                        ->orderBy('updated_at', 'DESC')
                                        ->select('host_id', 'value')
                                        ->take($count)
                                        ->get();
        $list = [];
        foreach ($records as $record) {
            $list[] = ['id'   => $record->morph->id,
                       'sku'  => $record->morph->sku,
                       'name' => $record->value];
        }

        return $list;
    }

    /**
     * @param String  $host_type
     * @param String  $host_id
     * @param String  $code
     * @param Any     $value
     * @param String  $target
     * @param Boolean $target_is_enabled
     * @return Array
     */
    public function autoCompleteSKUOfEnabled($host_type, $host_id, String $code, $value, $count = 10, $target = null, $target_is_enabled = null)
    {
        if (!is_integer($count) || $count <= 0)     throw new NotUnsignedIntegerException($count);

        if (empty($host_type) || empty($host_id)) {
            $entity = $this->entity;
        } else {
            $entity = $this->baseQueryForRepository($host_type, $host_id, $target, $target_is_enabled);
        }
        $records = $entity->with(['langs' => function ($query) use ($code) {
                              $query->ofCurrent()
                                    ->ofCodeAndKey($code, 'name');
                           }])
                          ->ofEnabled()
                          ->where('sku', 'LIKE', $value .'%')
                          ->orderBy('updated_at', 'DESC')
                          ->select('id', 'sku')
                          ->take($count)
                          ->get();
        $list = [];
        foreach ($records as $record) {
            $entity_lang = $record->findLangByKey('name');
            $list[] = ['id'   => $record->id,
                       'sku'  => $record->sku,
                       'name' => $entity_lang ?? ''];
        }

        return $list;
    }

    /**
     * @param Stock $entity
     * @param Array|String $code
     * @return Array
     */
    public function show($entity, $code)
    {
        $data = [
            'id'       => $entity ? $entity->id : '',
            'basic'    => [],
            'covers'   => [],
            'images'   => [],
            'comments' => []
        ];

        if (empty($entity))
            return $data;

        $this->setEntity($entity);

        if (is_string($code)) {
            $data['basic'] = [
                'id'                => $entity->id,
                'host_type'         => $entity->host_type,
                'host_id'           => $entity->host_id,
                'product_id'        => $entity->product_id,
                'catalog_id'        => $entity->catalog_id,
                'type'              => $entity->type,
                'attribute_set'     => $entity->attribute_set,
                'sku'               => $entity->sku,
                'identifier'        => $entity->identifier,
                'cost'              => $entity->cost,
                'price_original'    => $entity->price_original,
                'price_discount'    => $entity->price_discount,
                'options'           => $entity->options,
                'covers'            => $entity->covers,
                'images'            => $entity->images,
                'videos'            => $entity->videos,
                'inventory'         => $entity->inventory,
                'quantity'          => $entity->quantity,
                'qty_per_order'     => $entity->qty_per_order,
                'fee'               => $entity->fee,
                'tax'               => $entity->tax,
                'tip'               => $entity->tip,
                'name'              => $entity->findLang($code, 'name'),
                'abstract'          => $entity->findLang($code, 'abstract'),
                'description'       => $entity->findLang($code, 'description'),
                'keywords'          => $entity->findLang($code, 'keywords'),
                'remarks'           => $entity->findLang($code, 'remarks'),
                'weight'            => $entity->weight,
                'binding_supported' => $entity->binding_supported,
                'recommendation'    => $entity->recommendation,
                'is_new'            => $entity->is_new,
                'is_featured'       => $entity->is_featured,
                'is_highlighted'    => $entity->is_highlighted,
                'is_sellable'       => $entity->is_sellable,
                'is_enabled'        => $entity->is_enabled,
                'updated_at'        => $entity->updated_at
            ];
            if (config('wk-mall-shelf.onoff.morph-category')) {
                $data['basic']['categories'] = [];
                foreach ($entity->categories as $category) {
                    $data['basic']['categories'] = array_merge($data['basic']['categories'], [
                        $category->id => [
                            'id'          => $category->id,
                            'identifier'  => $category->identifier,
                            'target'      => $category->target,
                            'icon'        => $category->icon,
                            'order'       => $category->order,
                            'name'        => $category->findLang($code, 'name'),
                            'description' => $category->findLang($code, 'description')
                        ]
                    ]);
                }
            }
            if (config('wk-mall-shelf.onoff.morph-tag') && is_iterable($entity->tags)) {
                $data['basic']['tags'] = [];
                foreach ($entity->tags as $tag) {
                    $data['basic']['tags'] = array_merge($data['basic']['tags'], [
                        $tag->id => [
                            'id'          => $tag->id,
                            'identifier'  => $tag->identifier,
                            'order'       => $tag->order,
                            'name'        => $tag->findLang($code, 'name'),
                            'description' => $tag->findLang($code, 'description')
                        ]
                    ]);
                }
            }
            $data['basic']['product'] = [
                'id'          => empty($entity->product_id) ? '' : $entity->product->id,
                'serial'      => empty($entity->product_id) ? '' : $entity->product->serial,
                'price_base'  => empty($entity->product_id) ? '' : $entity->product->price_base,
                'name'        => empty($entity->product_id) ? '' : $entity->product->name,
                'description' => empty($entity->product_id) ? '' : $entity->product->description,
                'updated_at'  => empty($entity->product_id) ? '' : $entity->product->updated_at
            ];
            $data['basic']['catalog'] = [
                'id'          => empty($entity->catalog_id) ? '' : $entity->catalog->id,
                'serial'      => empty($entity->catalog_id) ? '' : $entity->catalog->serial,
                'color'       => empty($entity->catalog_id) ? '' : $entity->catalog->color,
                'size'        => empty($entity->catalog_id) ? '' : $entity->catalog->size,
                'material'    => empty($entity->catalog_id) ? '' : $entity->catalog->material,
                'taste'       => empty($entity->catalog_id) ? '' : $entity->catalog->taste,
                'weight'      => empty($entity->catalog_id) ? '' : $entity->catalog->weight,
                'length'      => empty($entity->catalog_id) ? '' : $entity->catalog->length,
                'width'       => empty($entity->catalog_id) ? '' : $entity->catalog->width,
                'height'      => empty($entity->catalog_id) ? '' : $entity->catalog->height,
                'name'        => empty($entity->catalog_id) ? '' : $entity->catalog->findLang($code, 'name'),
                'description' => empty($entity->catalog_id) ? '' : $entity->catalog->findLang($language, 'description'),
                'updated_at'  => empty($entity->catalog_id) ? '' : $entity->catalog->updated_at
            ];

        } elseif (is_array($code)) {
            foreach ($code as $language) {
                $data['basic'][$language] = [
                    'id'                => $entity->id,
                    'host_type'         => $entity->host_type,
                    'host_id'           => $entity->host_id,
                    'product_id'        => $entity->product_id,
                    'catalog_id'        => $entity->catalog_id,
                    'type'              => $entity->type,
                    'attribute_set'     => $entity->attribute_set,
                    'sku'               => $entity->sku,
                    'identifier'        => $entity->identifier,
                    'cost'              => $entity->cost,
                    'price_original'    => $entity->price_original,
                    'price_discount'    => $entity->price_discount,
                    'options'           => $entity->options,
                    'covers'            => $entity->covers,
                    'images'            => $entity->images,
                    'videos'            => $entity->videos,
                    'inventory'         => $entity->inventory,
                    'quantity'          => $entity->quantity,
                    'qty_per_order'     => $entity->qty_per_order,
                    'fee'               => $entity->fee,
                    'tax'               => $entity->tax,
                    'tip'               => $entity->tip,
                    'name'              => $entity->findLang($language, 'name'),
                    'abstract'          => $entity->findLang($language, 'abstract'),
                    'description'       => $entity->findLang($language, 'description'),
                    'keywords'          => $entity->findLang($language, 'keywords'),
                    'remarks'           => $entity->findLang($language, 'remarks'),
                    'weight'            => $entity->weight,
                    'binding_supported' => $entity->binding_supported,
                    'recommendation'    => $entity->recommendation,
                    'is_new'            => $entity->is_new,
                    'is_featured'       => $entity->is_featured,
                    'is_highlighted'    => $entity->is_highlighted,
                    'is_sellable'       => $entity->is_sellable,
                    'is_enabled'        => $entity->is_enabled,
                    'updated_at'        => $entity->updated_at
                ];
                if (config('wk-mall-shelf.onoff.morph-category')) {
                    $data['basic'][$language]['categories'] = [];
                    foreach ($entity->categories as $category) {
                        $data['basic'][$language]['categories'] = array_merge($data['basic'][$language]['categories'], [
                            $category->id => [
                                'id'          => $category->id,
                                'identifier'  => $category->identifier,
                                'target'      => $category->target,
                                'icon'        => $category->icon,
                                'order'       => $category->order,
                                'name'        => $category->findLang($language, 'name'),
                                'description' => $category->findLang($language, 'description')
                            ]
                        ]);
                    }
                }
                if (config('wk-mall-shelf.onoff.morph-tag') && is_iterable($entity->tags)) {
                    $data['basic'][$language]['tags'] = [];
                    foreach ($entity->tags as $tag) {
                        $data['basic'][$language]['tags'] = array_merge($data['basic'][$language]['tags'], [
                            $tag->id => [
                                'id'          => $tag->id,
                                'identifier'  => $tag->identifier,
                                'order'       => $tag->order,
                                'name'        => $tag->findLang($language, 'name'),
                                'description' => $tag->findLang($language, 'description')
                            ]
                        ]);
                    }
                }
                $data['basic'][$language]['product'] = [
                    'id'          => empty($entity->product_id) ? '' : $entity->product->id,
                    'serial'      => empty($entity->product_id) ? '' : $entity->product->serial,
                    'price_base'  => empty($entity->product_id) ? '' : $entity->product->price_base,
                    'name'        => empty($entity->product_id) ? '' : $entity->product->name,
                    'description' => empty($entity->product_id) ? '' : $entity->product->description,
                    'updated_at'  => empty($entity->product_id) ? '' : $entity->product->updated_at
                ];
                $data['basic'][$language]['catalog'] = [
                    'id'          => empty($entity->catalog_id) ? '' : $entity->catalog->id,
                    'serial'      => empty($entity->catalog_id) ? '' : $entity->catalog->serial,
                    'color'       => empty($entity->catalog_id) ? '' : $entity->catalog->color,
                    'size'        => empty($entity->catalog_id) ? '' : $entity->catalog->size,
                    'material'    => empty($entity->catalog_id) ? '' : $entity->catalog->material,
                    'taste'       => empty($entity->catalog_id) ? '' : $entity->catalog->taste,
                    'weight'      => empty($entity->catalog_id) ? '' : $entity->catalog->weight,
                    'length'      => empty($entity->catalog_id) ? '' : $entity->catalog->length,
                    'width'       => empty($entity->catalog_id) ? '' : $entity->catalog->width,
                    'height'      => empty($entity->catalog_id) ? '' : $entity->catalog->height,
                    'name'        => empty($entity->catalog_id) ? '' : $entity->catalog->findLang($language, 'name'),
                    'description' => empty($entity->catalog_id) ? '' : $entity->catalog->findLang($language, 'description'),
                    'updated_at'  => empty($entity->catalog_id) ? '' : $entity->catalog->updated_at
                ];
            }
        }
        $data['covers'] = $this->getlistOfCovers($code);
        $data['images'] = $this->getlistOfImages($code, true);

        if (config('wk-mall-shelf.onoff.morph-comment'))
            $data['comments'] = $this->getlistOfComments($entity);

        return $data;
    }

    /**
     * @param Stock  $entity
     * @param String $code
     * @return Array
     */
    public function showForFrontend($entity, $code)
    {
        $data = [
            'stock'    => [],
            'product'  => [],
            'catalog'  => [],
            'comments' => []
        ];

        if (empty($entity) || !$entity->is_enabled || ($entity->product && !$entity->product->is_enabled))
            return $data;

        $this->setEntity($entity);

        if (is_string($code)) {
            $data['stock'] = [
                'id'                => $entity->id,
                'type'              => $entity->type,
                'attribute_set'     => $entity->attribute_set,
                'sku'               => $entity->sku,
                'identifier'        => $entity->identifier,
                'price_original'    => $entity->price_original,
                'price_discount'    => $entity->price_discount,
                'options'           => $entity->options,
                'covers'            => $entity->covers,
                'images'            => $entity->images,
                'videos'            => $entity->videos,
                'inventory'         => $entity->inventory,
                'quantity'          => $entity->quantity,
                'qty_per_order'     => $entity->qty_per_order,
                'fee'               => $entity->fee,
                'tax'               => $entity->tax,
                'tip'               => $entity->tip,
                'name'              => $entity->findLang($code, 'name'),
                'abstract'          => $entity->findLang($code, 'abstract'),
                'description'       => $entity->findLang($code, 'description'),
                'keywords'          => $entity->findLang($code, 'keywords'),
                'weight'            => $entity->weight,
                'binding_supported' => $entity->binding_supported,
                'recommendation'    => $entity->recommendation,
                'is_new'            => $entity->is_new,
                'is_featured'       => $entity->is_featured,
                'is_highlighted'    => $entity->is_highlighted,
                'is_sellable'       => $entity->is_sellable,
                'updated_at'        => $entity->updated_at,
                'covers'            => $this->getlistOfCovers($code, true, $entity, true),
                'images'            => $this->getlistOfImages($code, true, true, $entity, true)
            ];
            if (config('wk-mall-shelf.onoff.morph-category')) {
                $data['stock']['categories'] = [];
                foreach ($entity->categories as $category) {
                    if ($category->is_enabled) {
                        array_push($data['stock']['categories'], [
                            'identifier'  => $category->identifier,
                            'target'      => $category->target,
                            'icon'        => $category->icon,
                            'order'       => $category->order,
                            'name'        => $category->findLang($code, 'name'),
                            'description' => $category->findLang($code, 'description')
                        ]);
                    }
                }
            }
            if (config('wk-mall-shelf.onoff.morph-tag') && is_iterable($entity->tags)) {
                $data['stock']['tags'] = [];
                foreach ($entity->tags as $tag) {
                    if ($tag->is_enabled) {
                        array_push($data['stock']['tags'], [
                            'identifier'  => $tag->identifier,
                            'order'       => $tag->order,
                            'name'        => $tag->findLang($code, 'name'),
                            'description' => $tag->findLang($code, 'description')
                        ]);
                    }
                }
            }
            $data['product'] = [
                'name'        => empty($entity->product_id) ? '' : $entity->product->findLang($code, 'name'),
                'description' => empty($entity->product_id) ? '' : $entity->product->findLang($code, 'description'),
                'covers'      => empty($entity->product_id) ? '' : $this->getlistOfCovers($code, true, $entity->product, true),
                'images'      => empty($entity->product_id) ? '' : $this->getlistOfImages($code, true, true, $entity->product, true)
            ];
            $data['catalog'] = [
                'color'       => empty($entity->catalog_id) ? '' : $entity->catalog->color,
                'size'        => empty($entity->catalog_id) ? '' : $entity->catalog->size,
                'material'    => empty($entity->catalog_id) ? '' : $entity->catalog->material,
                'taste'       => empty($entity->catalog_id) ? '' : $entity->catalog->taste,
                'weight'      => empty($entity->catalog_id) ? '' : $entity->catalog->weight,
                'length'      => empty($entity->catalog_id) ? '' : $entity->catalog->length,
                'width'       => empty($entity->catalog_id) ? '' : $entity->catalog->width,
                'height'      => empty($entity->catalog_id) ? '' : $entity->catalog->height,
                'name'        => empty($entity->catalog_id) ? '' : $entity->catalog->findLang($code, 'name'),
                'description' => empty($entity->catalog_id) ? '' : $entity->catalog->findLang($code, 'description'),
                'covers'      => empty($entity->catalog_id) ? [] : $this->getlistOfCovers($code, true, $entity->catalog, true),
                'images'      => empty($entity->catalog_id) ? [] : $this->getlistOfImages($code, true, true, $entity->catalog, true)
            ];

        } elseif (is_array($code)) {
            foreach ($code as $language) {
                $data['stock'][$language] = [
                    'id'                => $entity->id,
                    'type'              => $entity->type,
                    'attribute_set'     => $entity->attribute_set,
                    'sku'               => $entity->sku,
                    'identifier'        => $entity->identifier,
                    'price_original'    => $entity->price_original,
                    'price_discount'    => $entity->price_discount,
                    'options'           => $entity->options,
                    'covers'            => $entity->covers,
                    'images'            => $entity->images,
                    'videos'            => $entity->videos,
                    'inventory'         => $entity->inventory,
                    'quantity'          => $entity->quantity,
                    'qty_per_order'     => $entity->qty_per_order,
                    'fee'               => $entity->fee,
                    'tax'               => $entity->tax,
                    'tip'               => $entity->tip,
                    'name'              => $entity->findLang($language, 'name'),
                    'abstract'          => $entity->findLang($language, 'abstract'),
                    'description'       => $entity->findLang($language, 'description'),
                    'keywords'          => $entity->findLang($language, 'keywords'),
                    'weight'            => $entity->weight,
                    'binding_supported' => $entity->binding_supported,
                    'recommendation'    => $entity->recommendation,
                    'is_new'            => $entity->is_new,
                    'is_featured'       => $entity->is_featured,
                    'is_highlighted'    => $entity->is_highlighted,
                    'is_sellable'       => $entity->is_sellable,
                    'updated_at'        => $entity->updated_at,
                    'covers'            => $this->getlistOfCovers($language, true, $entity, true),
                    'images'            => $this->getlistOfImages($language, true, true, $entity, true)
                ];
                if (config('wk-mall-shelf.onoff.morph-category')) {
                    $data['stock'][$language]['categories'] = [];
                    foreach ($entity->categories as $category) {
                        if ($category->is_enabled) {
                            array_push($data['stock'][$language]['categories'], [
                                'identifier'  => $category->identifier,
                                'target'      => $category->target,
                                'icon'        => $category->icon,
                                'order'       => $category->order,
                                'name'        => $category->findLang($language, 'name'),
                                'description' => $category->findLang($language, 'description')
                            ]);
                        }
                    }
                }
                if (config('wk-mall-shelf.onoff.morph-tag') && is_iterable($entity->tags)) {
                    $data['stock'][$language]['tags'] = [];
                    foreach ($entity->tags as $tag) {
                        if ($tag->is_enabled) {
                            array_push($data['stock'][$language]['tags'], [
                                'identifier'  => $tag->identifier,
                                'order'       => $tag->order,
                                'name'        => $tag->findLang($language, 'name'),
                                'description' => $tag->findLang($language, 'description')
                            ]);
                        }
                    }
                }
                $data['product'][$language] = [
                    'name'        => empty($entity->product_id) ? '' : $entity->product->findLang($language, 'name'),
                    'description' => empty($entity->product_id) ? '' : $entity->product->findLang($language, 'description'),
                    'covers'      => empty($entity->product_id) ? '' : $this->getlistOfCovers($language, true, $entity->product, true),
                    'images'      => empty($entity->product_id) ? '' : $this->getlistOfImages($language, true, true, $entity->product, true)
                ];
                $data['catalog'][$language] = [
                    'color'       => empty($entity->catalog_id) ? '' : $entity->catalog->color,
                    'size'        => empty($entity->catalog_id) ? '' : $entity->catalog->size,
                    'material'    => empty($entity->catalog_id) ? '' : $entity->catalog->material,
                    'taste'       => empty($entity->catalog_id) ? '' : $entity->catalog->taste,
                    'weight'      => empty($entity->catalog_id) ? '' : $entity->catalog->weight,
                    'length'      => empty($entity->catalog_id) ? '' : $entity->catalog->length,
                    'width'       => empty($entity->catalog_id) ? '' : $entity->catalog->width,
                    'height'      => empty($entity->catalog_id) ? '' : $entity->catalog->height,
                    'name'        => empty($entity->catalog_id) ? '' : $entity->catalog->findLang($language, 'name'),
                    'description' => empty($entity->catalog_id) ? '' : $entity->catalog->findLang($language, 'description'),
                    'covers'      => empty($entity->catalog_id) ? [] : $this->getlistOfCovers($language, true, $entity->catalog, true),
                    'images'      => empty($entity->catalog_id) ? [] : $this->getlistOfImages($language, true, true, $entity->catalog, true)
                ];
            }
        }

        if (config('wk-mall-shelf.onoff.morph-comment'))
            $data['comments'] = $this->getlistOfComments($entity);

        return $data;
    }
}
