<?php

namespace WalkerChiu\MallShelf\Models\Repositories;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Forms\FormTrait;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryTrait;
use WalkerChiu\Core\Models\Services\PackagingFactory;
use WalkerChiu\MorphComment\Models\Repositories\CommentRepositoryTrait;
use WalkerChiu\MorphImage\Models\Repositories\ImageRepositoryTrait;

class CatalogRepository extends Repository
{
    use FormTrait;
    use RepositoryTrait;
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
        $this->instance = App::make(config('wk-core.class.mall-shelf.catalog'));
    }

    /**
     * @param String  $code
     * @param Array   $data
     * @param Bool    $is_enabled
     * @param String  $target
     * @param Bool    $target_is_enabled
     * @param Bool    $auto_packing
     * @return Array|Collection|Eloquent
     */
    public function list(string $code, array $data, $is_enabled = null, $target = null, $target_is_enabled = null, $auto_packing = false)
    {
        $instance = $this->instance;
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
                                            ->unless(empty($data['product_id']), function ($query) use ($data) {
                                                return $query->where('product_id', $data['product_id']);
                                            })
                                            ->unless(empty($data['serial']), function ($query) use ($data) {
                                                return $query->where('serial', $data['serial']);
                                            })
                                            ->unless(empty($data['color']), function ($query) use ($data) {
                                                return $query->where('color', $data['color']);
                                            })
                                            ->unless(empty($data['size']), function ($query) use ($data) {
                                                return $query->where('size', $data['size']);
                                            })
                                            ->unless(empty($data['material']), function ($query) use ($data) {
                                                return $query->where('material', $data['material']);
                                            })
                                            ->unless(empty($data['taste']), function ($query) use ($data) {
                                                return $query->where('taste', $data['taste']);
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
                                            ->unless(empty($data['length']), function ($query) use ($data) {
                                                return $query->where('length', $data['length']);
                                            })
                                            ->unless(empty($data['length_min']), function ($query) use ($data) {
                                                return $query->where('length', '>=', $data['length_min']);
                                            })
                                            ->unless(empty($data['length_max']), function ($query) use ($data) {
                                                return $query->where('length', '<=', $data['length_max']);
                                            })
                                            ->unless(empty($data['width']), function ($query) use ($data) {
                                                return $query->where('width', $data['width']);
                                            })
                                            ->unless(empty($data['width_min']), function ($query) use ($data) {
                                                return $query->where('width', '>=', $data['width_min']);
                                            })
                                            ->unless(empty($data['width_max']), function ($query) use ($data) {
                                                return $query->where('width', '<=', $data['width_max']);
                                            })
                                            ->unless(empty($data['height']), function ($query) use ($data) {
                                                return $query->where('height', $data['height']);
                                            })
                                            ->unless(empty($data['height_min']), function ($query) use ($data) {
                                                return $query->where('height', '>=', $data['height_min']);
                                            })
                                            ->unless(empty($data['height_max']), function ($query) use ($data) {
                                                return $query->where('height', '<=', $data['height_max']);
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
                                            ->unless(empty($data['name']), function ($query) use ($data) {
                                                return $query->whereHas('langs', function ($query) use ($data) {
                                                    $query->ofCurrent()
                                                          ->where('key', 'name')
                                                          ->where('value', 'LIKE', "%".$data['name']."%");
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
                                            });
                                })
                                ->orderBy('updated_at', 'DESC');

        if ($auto_packing) {
            $factory = new PackagingFactory(config('wk-mall-shelf.output_format'), config('wk-mall-shelf.pagination.pageName'), config('wk-mall-shelf.pagination.perPage'));
            $factory->setFieldsLang(['name', 'description', 'keywords']);

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
                                    'covers' => $this->getlistOfCovers($code)
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

    /**
     * @param Catalog       $instance
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
                'product_id'  => $instance->product_id,
                'serial'      => $instance->serial,
                'color'       => $instance->color,
                'size'        => $instance->size,
                'material'    => $instance->material,
                'taste'       => $instance->taste,
                'weight'      => $instance->weight,
                'length'      => $instance->length,
                'width'       => $instance->width,
                'height'      => $instance->height,
                'cost'        => $instance->cost,
                'covers'      => $instance->covers,
                'images'      => $instance->images,
                'videos'      => $instance->videos,
                'name'        => $instance->findLang($code, 'name'),
                'description' => $instance->findLang($code, 'description'),
                'keywords'    => $instance->findLang($code, 'keywords'),
                'remarks'     => $instance->findLang($code, 'remarks'),
                'is_enabled'  => $instance->is_enabled,
                'updated_at'  => $instance->updated_at
            ];

        } elseif (is_array($code)) {
            foreach ($code as $language) {
                $data['basic'][$language] = [
                    'product_id'  => $instance->product_id,
                    'serial'      => $instance->serial,
                    'color'       => $instance->color,
                    'size'        => $instance->size,
                    'material'    => $instance->material,
                    'taste'       => $instance->taste,
                    'weight'      => $instance->weight,
                    'length'      => $instance->length,
                    'width'       => $instance->width,
                    'height'      => $instance->height,
                    'cost'        => $instance->cost,
                    'covers'      => $instance->covers,
                    'images'      => $instance->images,
                    'videos'      => $instance->videos,
                    'name'        => $instance->findLang($language, 'name'),
                    'description' => $instance->findLang($language, 'description'),
                    'keywords'    => $instance->findLang($language, 'keywords'),
                    'remarks'     => $instance->findLang($language, 'remarks'),
                    'is_enabled'  => $instance->is_enabled,
                    'updated_at'  => $instance->updated_at
                ];
            }
        }
        $data['covers'] = $this->getlistOfCovers($code);
        $data['images'] = $this->getlistOfImages($code, true);

        if (config('wk-mall-shelf.onoff.morph-comment'))
            $data['comments'] = $this->getlistOfComments($instance);

        return $data;
    }
}
