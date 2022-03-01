<?php

namespace WalkerChiu\MallShelf\Models\Repositories;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Forms\FormHasHostTrait;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryHasHostTrait;
use WalkerChiu\Core\Models\Services\PackagingFactory;
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

    protected $instance;



    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->instance = App::make(config('wk-core.class.mall-shelf.stock'));
    }

    /**
     * @param String  $host_type
     * @param String  $host_id
     * @param String  $code
     * @param Array   $data
     * @param Bool    $is_enabled
     * @param String  $target
     * @param Bool    $target_is_enabled
     * @param Bool    $auto_packing
     * @return Array|Collection|Eloquent
     */
    public function list(?string $host_type, ?string $host_id, string $code, array $data, $is_enabled = null, $target = null, $target_is_enabled = null, $auto_packing = false)
    {
        if (
            empty($host_type)
            || empty($host_id)
        ) {
            $instance = $this->instance;
        } else {
            $instance = $this->baseQueryForRepository($host_type, $host_id, $target, $target_is_enabled);
        }
        if ($is_enabled === true)      $instance = $instance->ofEnabled();
        elseif ($is_enabled === false) $instance = $instance->ofDisabled();

        $repository = $instance->with(['langs' => function ($query) use ($code) {
                                    $query->ofCurrent()
                                          ->ofCode($code);
                                }])
                                ->whereHas('langs', function ($query) use ($code) {
                                    return $query->ofCurrent()
                                                 ->ofCode($code);
                                })
                                ->when(
                                    config('wk-mall-shelf.onoff.morph-tag')
                                    && !empty(config('wk-core.class.morph-tag.tag'))
                                , function ($query) {
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
                                                return $query->whereHas('langs', function ($query) use ($data) {
                                                    $query->ofCurrent()
                                                          ->where('key', 'name')
                                                          ->where('value', 'LIKE', "%".$data['name']."%");
                                                });
                                            })
                                            ->unless(empty($data['abstract']), function ($query) use ($data) {
                                                return $query->whereHas('langs', function ($query) use ($data) {
                                                    $query->ofCurrent()
                                                          ->where('key', 'abstract')
                                                          ->where('value', 'LIKE', "%".$data['abstract']."%");
                                                });
                                            })
                                            ->unless(empty($data['description']), function ($query) use ($data) {
                                                return $query->whereHas('langs', function ($query) use ($data) {
                                                    $query->ofCurrent()
                                                          ->where('key', 'description')
                                                          ->where('value', 'LIKE', "%".$data['description']."%");
                                                });
                                            })
                                            ->unless(empty($data['keywords']), function ($query) use ($data) {
                                                return $query->whereHas('langs', function ($query) use ($data) {
                                                    $query->ofCurrent()
                                                          ->where('key', 'keywords')
                                                          ->where('value', 'LIKE', "%".$data['keywords']."%");
                                                });
                                            })
                                            ->unless(empty($data['remarks']), function ($query) use ($data) {
                                                return $query->whereHas('langs', function ($query) use ($data) {
                                                    $query->ofCurrent()
                                                          ->where('key', 'remarks')
                                                          ->where('value', 'LIKE', "%".$data['remarks']."%");
                                                });
                                            })
                                            ->unless(empty($data['categories']), function ($query) use ($data) {
                                                return $query->whereHas('categories', function ($query) use ($data) {
                                                    $query->ofEnabled()
                                                          ->whereIn('id', $data['categories']);
                                                });
                                            })
                                            ->unless(empty($data['tags']), function ($query) use ($data) {
                                                return $query->whereHas('tags', function ($query) use ($data) {
                                                    $query->ofEnabled()
                                                          ->whereIn('id', $data['tags']);
                                                });
                                            })
                                            ->unless(
                                                empty($data['orderBy'])
                                                && empty($data['orderType'])
                                            , function ($query) use ($data) {
                                                return $query->orderBy($data['orderBy'], $data['orderType']);
                                            }, function ($query) {
                                                return $query->orderBy('updated_at', 'DESC');
                                            });
                                }, function ($query) {
                                    return $query->orderBy('updated_at', 'DESC');
                                });

        if ($auto_packing) {
            $factory = new PackagingFactory(config('wk-mall-shelf.output_format'), config('wk-mall-shelf.pagination.pageName'), config('wk-mall-shelf.pagination.perPage'));
            $factory->setFieldsLang(['name', 'abstract', 'description', 'keywords']);

            if (in_array(config('wk-mall-shelf.output_format'), ['array', 'array_pagination'])) {
                switch (config('wk-mall-shelf.output_format')) {
                    case "array":
                        $entities = $factory->toCollection($repository);
                        // no break
                    case "array_pagination":
                        $entities = $factory->toCollectionWithPagination($repository);
                        // no break
                    default:
                        $output = [];
                        foreach ($entities as $instance) {
                            $data = $instance->toArray();
                            array_push($output,
                                array_merge($data, [
                                    'catalog' => $instance->catalog,
                                    'covers'  => $this->getlistOfCovers($code)
                                ])
                            );
                        }
                }
                return $output;
            } else {
                return $factory->output($repository);
            }
        }

        return $repository;
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
     * @param Mixed   $value
     * @param String  $target
     * @param Bool    $target_is_enabled
     * @return Array
     *
     * @throws NotUnsignedIntegerException
     */
    public function autoCompleteSKUOfEnabled(?string $host_type, ?string $host_id, string $code, $value, $count = 10, $target = null, $target_is_enabled = null): array
    {
        if (
            !is_integer($count)
            || $count <= 0
        ) {
            throw new NotUnsignedIntegerException($count);
        }

        if (
            empty($host_type)
            || empty($host_id)
        ) {
            $instance = $this->instance;
        } else {
            $instance = $this->baseQueryForRepository($host_type, $host_id, $target, $target_is_enabled);
        }
        $records = $instance->with(['langs' => function ($query) use ($code) {
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
            $instance_lang = $record->findLangByKey('name');
            $list[] = ['id'   => $record->id,
                       'sku'  => $record->sku,
                       'name' => $instance_lang ?? ''];
        }

        return $list;
    }

    /**
     * @param Stock         $instance
     * @param Array|String  $code
     * @return Array
     */
    public function show($instance, $code): array
    {
        $data = [
            'id'       => $instance ? $instance->id : '',
            'basic'    => [],
            'covers'   => [],
            'images'   => [],
            'comments' => []
        ];

        if (empty($instance))
            return $data;

        $this->setEntity($instance);

        if (is_string($code)) {
            $data['basic'] = [
                'id'                => $instance->id,
                'host_type'         => $instance->host_type,
                'host_id'           => $instance->host_id,
                'product_id'        => $instance->product_id,
                'catalog_id'        => $instance->catalog_id,
                'type'              => $instance->type,
                'attribute_set'     => $instance->attribute_set,
                'sku'               => $instance->sku,
                'identifier'        => $instance->identifier,
                'cost'              => $instance->cost,
                'price_original'    => $instance->price_original,
                'price_discount'    => $instance->price_discount,
                'options'           => $instance->options,
                'covers'            => $instance->covers,
                'images'            => $instance->images,
                'videos'            => $instance->videos,
                'inventory'         => $instance->inventory,
                'quantity'          => $instance->quantity,
                'qty_per_order'     => $instance->qty_per_order,
                'fee'               => $instance->fee,
                'tax'               => $instance->tax,
                'tip'               => $instance->tip,
                'name'              => $instance->findLang($code, 'name'),
                'abstract'          => $instance->findLang($code, 'abstract'),
                'description'       => $instance->findLang($code, 'description'),
                'keywords'          => $instance->findLang($code, 'keywords'),
                'remarks'           => $instance->findLang($code, 'remarks'),
                'weight'            => $instance->weight,
                'binding_supported' => $instance->binding_supported,
                'recommendation'    => $instance->recommendation,
                'is_new'            => $instance->is_new,
                'is_featured'       => $instance->is_featured,
                'is_highlighted'    => $instance->is_highlighted,
                'is_sellable'       => $instance->is_sellable,
                'is_enabled'        => $instance->is_enabled,
                'updated_at'        => $instance->updated_at
            ];
            if (config('wk-mall-shelf.onoff.morph-category')) {
                $data['basic']['categories'] = [];
                foreach ($instance->categories as $category) {
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
            if (
                config('wk-mall-shelf.onoff.morph-tag')
                && is_iterable($instance->tags)
            ) {
                $data['basic']['tags'] = [];
                foreach ($instance->tags as $tag) {
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
                'id'          => empty($instance->product_id) ? '' : $instance->product->id,
                'serial'      => empty($instance->product_id) ? '' : $instance->product->serial,
                'price_base'  => empty($instance->product_id) ? '' : $instance->product->price_base,
                'name'        => empty($instance->product_id) ? '' : $instance->product->name,
                'description' => empty($instance->product_id) ? '' : $instance->product->description,
                'updated_at'  => empty($instance->product_id) ? '' : $instance->product->updated_at
            ];
            $data['basic']['catalog'] = [
                'id'          => empty($instance->catalog_id) ? '' : $instance->catalog->id,
                'serial'      => empty($instance->catalog_id) ? '' : $instance->catalog->serial,
                'color'       => empty($instance->catalog_id) ? '' : $instance->catalog->color,
                'size'        => empty($instance->catalog_id) ? '' : $instance->catalog->size,
                'material'    => empty($instance->catalog_id) ? '' : $instance->catalog->material,
                'taste'       => empty($instance->catalog_id) ? '' : $instance->catalog->taste,
                'weight'      => empty($instance->catalog_id) ? '' : $instance->catalog->weight,
                'length'      => empty($instance->catalog_id) ? '' : $instance->catalog->length,
                'width'       => empty($instance->catalog_id) ? '' : $instance->catalog->width,
                'height'      => empty($instance->catalog_id) ? '' : $instance->catalog->height,
                'name'        => empty($instance->catalog_id) ? '' : $instance->catalog->findLang($code, 'name'),
                'description' => empty($instance->catalog_id) ? '' : $instance->catalog->findLang($language, 'description'),
                'updated_at'  => empty($instance->catalog_id) ? '' : $instance->catalog->updated_at
            ];

        } elseif (is_array($code)) {
            foreach ($code as $language) {
                $data['basic'][$language] = [
                    'id'                => $instance->id,
                    'host_type'         => $instance->host_type,
                    'host_id'           => $instance->host_id,
                    'product_id'        => $instance->product_id,
                    'catalog_id'        => $instance->catalog_id,
                    'type'              => $instance->type,
                    'attribute_set'     => $instance->attribute_set,
                    'sku'               => $instance->sku,
                    'identifier'        => $instance->identifier,
                    'cost'              => $instance->cost,
                    'price_original'    => $instance->price_original,
                    'price_discount'    => $instance->price_discount,
                    'options'           => $instance->options,
                    'covers'            => $instance->covers,
                    'images'            => $instance->images,
                    'videos'            => $instance->videos,
                    'inventory'         => $instance->inventory,
                    'quantity'          => $instance->quantity,
                    'qty_per_order'     => $instance->qty_per_order,
                    'fee'               => $instance->fee,
                    'tax'               => $instance->tax,
                    'tip'               => $instance->tip,
                    'name'              => $instance->findLang($language, 'name'),
                    'abstract'          => $instance->findLang($language, 'abstract'),
                    'description'       => $instance->findLang($language, 'description'),
                    'keywords'          => $instance->findLang($language, 'keywords'),
                    'remarks'           => $instance->findLang($language, 'remarks'),
                    'weight'            => $instance->weight,
                    'binding_supported' => $instance->binding_supported,
                    'recommendation'    => $instance->recommendation,
                    'is_new'            => $instance->is_new,
                    'is_featured'       => $instance->is_featured,
                    'is_highlighted'    => $instance->is_highlighted,
                    'is_sellable'       => $instance->is_sellable,
                    'is_enabled'        => $instance->is_enabled,
                    'updated_at'        => $instance->updated_at
                ];
                if (config('wk-mall-shelf.onoff.morph-category')) {
                    $data['basic'][$language]['categories'] = [];
                    foreach ($instance->categories as $category) {
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
                if (
                    config('wk-mall-shelf.onoff.morph-tag')
                    && is_iterable($instance->tags)
                ) {
                    $data['basic'][$language]['tags'] = [];
                    foreach ($instance->tags as $tag) {
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
                    'id'          => empty($instance->product_id) ? '' : $instance->product->id,
                    'serial'      => empty($instance->product_id) ? '' : $instance->product->serial,
                    'price_base'  => empty($instance->product_id) ? '' : $instance->product->price_base,
                    'name'        => empty($instance->product_id) ? '' : $instance->product->name,
                    'description' => empty($instance->product_id) ? '' : $instance->product->description,
                    'updated_at'  => empty($instance->product_id) ? '' : $instance->product->updated_at
                ];
                $data['basic'][$language]['catalog'] = [
                    'id'          => empty($instance->catalog_id) ? '' : $instance->catalog->id,
                    'serial'      => empty($instance->catalog_id) ? '' : $instance->catalog->serial,
                    'color'       => empty($instance->catalog_id) ? '' : $instance->catalog->color,
                    'size'        => empty($instance->catalog_id) ? '' : $instance->catalog->size,
                    'material'    => empty($instance->catalog_id) ? '' : $instance->catalog->material,
                    'taste'       => empty($instance->catalog_id) ? '' : $instance->catalog->taste,
                    'weight'      => empty($instance->catalog_id) ? '' : $instance->catalog->weight,
                    'length'      => empty($instance->catalog_id) ? '' : $instance->catalog->length,
                    'width'       => empty($instance->catalog_id) ? '' : $instance->catalog->width,
                    'height'      => empty($instance->catalog_id) ? '' : $instance->catalog->height,
                    'name'        => empty($instance->catalog_id) ? '' : $instance->catalog->findLang($language, 'name'),
                    'description' => empty($instance->catalog_id) ? '' : $instance->catalog->findLang($language, 'description'),
                    'updated_at'  => empty($instance->catalog_id) ? '' : $instance->catalog->updated_at
                ];
            }
        }
        $data['covers'] = $this->getlistOfCovers($code);
        $data['images'] = $this->getlistOfImages($code, true);

        if (config('wk-mall-shelf.onoff.morph-comment'))
            $data['comments'] = $this->getlistOfComments($instance);

        return $data;
    }

    /**
     * @param Stock   $instance
     * @param String  $code
     * @return Array
     */
    public function showForFrontend($instance, string $code): array
    {
        $data = [
            'stock'    => [],
            'product'  => [],
            'catalog'  => [],
            'comments' => []
        ];

        if (
            empty($instance)
            || !$instance->is_enabled
            || (
                $instance->product
                && !$instance->product->is_enabled
            )
        ) {
            return $data;
        }

        $this->setEntity($instance);

        if (is_string($code)) {
            $data['stock'] = [
                'id'                => $instance->id,
                'type'              => $instance->type,
                'attribute_set'     => $instance->attribute_set,
                'sku'               => $instance->sku,
                'identifier'        => $instance->identifier,
                'price_original'    => $instance->price_original,
                'price_discount'    => $instance->price_discount,
                'options'           => $instance->options,
                'covers'            => $instance->covers,
                'images'            => $instance->images,
                'videos'            => $instance->videos,
                'inventory'         => $instance->inventory,
                'quantity'          => $instance->quantity,
                'qty_per_order'     => $instance->qty_per_order,
                'fee'               => $instance->fee,
                'tax'               => $instance->tax,
                'tip'               => $instance->tip,
                'name'              => $instance->findLang($code, 'name'),
                'abstract'          => $instance->findLang($code, 'abstract'),
                'description'       => $instance->findLang($code, 'description'),
                'keywords'          => $instance->findLang($code, 'keywords'),
                'weight'            => $instance->weight,
                'binding_supported' => $instance->binding_supported,
                'recommendation'    => $instance->recommendation,
                'is_new'            => $instance->is_new,
                'is_featured'       => $instance->is_featured,
                'is_highlighted'    => $instance->is_highlighted,
                'is_sellable'       => $instance->is_sellable,
                'updated_at'        => $instance->updated_at,
                'covers'            => $this->getlistOfCovers($code, true, $instance, true),
                'images'            => $this->getlistOfImages($code, true, true, $instance, true)
            ];
            if (config('wk-mall-shelf.onoff.morph-category')) {
                $data['stock']['categories'] = [];
                foreach ($instance->categories as $category) {
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
            if (
                config('wk-mall-shelf.onoff.morph-tag')
                && is_iterable($instance->tags)
            ) {
                $data['stock']['tags'] = [];
                foreach ($instance->tags as $tag) {
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
                'name'        => empty($instance->product_id) ? '' : $instance->product->findLang($code, 'name'),
                'description' => empty($instance->product_id) ? '' : $instance->product->findLang($code, 'description'),
                'covers'      => empty($instance->product_id) ? '' : $this->getlistOfCovers($code, true, $instance->product, true),
                'images'      => empty($instance->product_id) ? '' : $this->getlistOfImages($code, true, true, $instance->product, true)
            ];
            $data['catalog'] = [
                'color'       => empty($instance->catalog_id) ? '' : $instance->catalog->color,
                'size'        => empty($instance->catalog_id) ? '' : $instance->catalog->size,
                'material'    => empty($instance->catalog_id) ? '' : $instance->catalog->material,
                'taste'       => empty($instance->catalog_id) ? '' : $instance->catalog->taste,
                'weight'      => empty($instance->catalog_id) ? '' : $instance->catalog->weight,
                'length'      => empty($instance->catalog_id) ? '' : $instance->catalog->length,
                'width'       => empty($instance->catalog_id) ? '' : $instance->catalog->width,
                'height'      => empty($instance->catalog_id) ? '' : $instance->catalog->height,
                'name'        => empty($instance->catalog_id) ? '' : $instance->catalog->findLang($code, 'name'),
                'description' => empty($instance->catalog_id) ? '' : $instance->catalog->findLang($code, 'description'),
                'covers'      => empty($instance->catalog_id) ? [] : $this->getlistOfCovers($code, true, $instance->catalog, true),
                'images'      => empty($instance->catalog_id) ? [] : $this->getlistOfImages($code, true, true, $instance->catalog, true)
            ];

        } elseif (is_array($code)) {
            foreach ($code as $language) {
                $data['stock'][$language] = [
                    'id'                => $instance->id,
                    'type'              => $instance->type,
                    'attribute_set'     => $instance->attribute_set,
                    'sku'               => $instance->sku,
                    'identifier'        => $instance->identifier,
                    'price_original'    => $instance->price_original,
                    'price_discount'    => $instance->price_discount,
                    'options'           => $instance->options,
                    'covers'            => $instance->covers,
                    'images'            => $instance->images,
                    'videos'            => $instance->videos,
                    'inventory'         => $instance->inventory,
                    'quantity'          => $instance->quantity,
                    'qty_per_order'     => $instance->qty_per_order,
                    'fee'               => $instance->fee,
                    'tax'               => $instance->tax,
                    'tip'               => $instance->tip,
                    'name'              => $instance->findLang($language, 'name'),
                    'abstract'          => $instance->findLang($language, 'abstract'),
                    'description'       => $instance->findLang($language, 'description'),
                    'keywords'          => $instance->findLang($language, 'keywords'),
                    'weight'            => $instance->weight,
                    'binding_supported' => $instance->binding_supported,
                    'recommendation'    => $instance->recommendation,
                    'is_new'            => $instance->is_new,
                    'is_featured'       => $instance->is_featured,
                    'is_highlighted'    => $instance->is_highlighted,
                    'is_sellable'       => $instance->is_sellable,
                    'updated_at'        => $instance->updated_at,
                    'covers'            => $this->getlistOfCovers($language, true, $instance, true),
                    'images'            => $this->getlistOfImages($language, true, true, $instance, true)
                ];
                if (config('wk-mall-shelf.onoff.morph-category')) {
                    $data['stock'][$language]['categories'] = [];
                    foreach ($instance->categories as $category) {
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
                if (
                    config('wk-mall-shelf.onoff.morph-tag')
                    && is_iterable($instance->tags)
                ) {
                    $data['stock'][$language]['tags'] = [];
                    foreach ($instance->tags as $tag) {
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
                    'name'        => empty($instance->product_id) ? '' : $instance->product->findLang($language, 'name'),
                    'description' => empty($instance->product_id) ? '' : $instance->product->findLang($language, 'description'),
                    'covers'      => empty($instance->product_id) ? '' : $this->getlistOfCovers($language, true, $instance->product, true),
                    'images'      => empty($instance->product_id) ? '' : $this->getlistOfImages($language, true, true, $instance->product, true)
                ];
                $data['catalog'][$language] = [
                    'color'       => empty($instance->catalog_id) ? '' : $instance->catalog->color,
                    'size'        => empty($instance->catalog_id) ? '' : $instance->catalog->size,
                    'material'    => empty($instance->catalog_id) ? '' : $instance->catalog->material,
                    'taste'       => empty($instance->catalog_id) ? '' : $instance->catalog->taste,
                    'weight'      => empty($instance->catalog_id) ? '' : $instance->catalog->weight,
                    'length'      => empty($instance->catalog_id) ? '' : $instance->catalog->length,
                    'width'       => empty($instance->catalog_id) ? '' : $instance->catalog->width,
                    'height'      => empty($instance->catalog_id) ? '' : $instance->catalog->height,
                    'name'        => empty($instance->catalog_id) ? '' : $instance->catalog->findLang($language, 'name'),
                    'description' => empty($instance->catalog_id) ? '' : $instance->catalog->findLang($language, 'description'),
                    'covers'      => empty($instance->catalog_id) ? [] : $this->getlistOfCovers($language, true, $instance->catalog, true),
                    'images'      => empty($instance->catalog_id) ? [] : $this->getlistOfImages($language, true, true, $instance->catalog, true)
                ];
            }
        }

        if (config('wk-mall-shelf.onoff.morph-comment'))
            $data['comments'] = $this->getlistOfComments($instance);

        return $data;
    }
}
