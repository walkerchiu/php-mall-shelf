<?php

namespace WalkerChiu\MallShelf\Models\Repositories;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Forms\FormTrait;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryTrait;
use WalkerChiu\MorphComment\Models\Repositories\CommentRepositoryTrait;
use WalkerChiu\MorphImage\Models\Repositories\ImageRepositoryTrait;

class CatalogRepository extends Repository
{
    use FormTrait;
    use RepositoryTrait;
    use CommentRepositoryTrait;
    use ImageRepositoryTrait;

    protected $entity;

    public function __construct()
    {
        $this->entity = App::make(config('wk-core.class.mall-shelf.catalog'));
    }

    /**
     * @param String  $code
     * @param Array   $data
     * @param Int     $page
     * @param Int     $nums per page
     * @param Boolean $is_enabled
     * @param String  $target
     * @param Boolean $target_is_enabled
     * @return Array
     */
    public function list(String $code, Array $data, $page = null, $nums = null, $is_enabled = null, $target = null, $target_is_enabled = null)
    {
        $this->assertForPagination($page, $nums);

        $entity = $this->entity;
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
                                            return $query->whereHas('langs', function($query) use ($data) {
                                                $query->ofCurrent()
                                                      ->where('key', 'name')
                                                      ->where('value', 'LIKE', "%".$data['name']."%");
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
                                        });
                            })
                            ->orderBy('updated_at', 'DESC')
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
                    'description' => $record->findLangByKey('description'),
                    'keywords'    => $record->findLangByKey('keywords'),
                    'covers'      => $this->getlistOfCovers($code)
                ])
            );
        }

        return $list;
    }

    /**
     * @param Catalog $entity
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
                'product_id'  => $entity->product_id,
                'serial'      => $entity->serial,
                'color'       => $entity->color,
                'size'        => $entity->size,
                'material'    => $entity->material,
                'taste'       => $entity->taste,
                'weight'      => $entity->weight,
                'length'      => $entity->length,
                'width'       => $entity->width,
                'height'      => $entity->height,
                'cost'        => $entity->cost,
                'covers'      => $entity->covers,
                'images'      => $entity->images,
                'videos'      => $entity->videos,
                'name'        => $entity->findLang($code, 'name'),
                'description' => $entity->findLang($code, 'description'),
                'keywords'    => $entity->findLang($code, 'keywords'),
                'remarks'     => $entity->findLang($code, 'remarks'),
                'is_enabled'  => $entity->is_enabled,
                'updated_at'  => $entity->updated_at
            ];

        } elseif (is_array($code)) {
            foreach ($code as $language) {
                $data['basic'][$language] = [
                    'product_id'  => $entity->product_id,
                    'serial'      => $entity->serial,
                    'color'       => $entity->color,
                    'size'        => $entity->size,
                    'material'    => $entity->material,
                    'taste'       => $entity->taste,
                    'weight'      => $entity->weight,
                    'length'      => $entity->length,
                    'width'       => $entity->width,
                    'height'      => $entity->height,
                    'cost'        => $entity->cost,
                    'covers'      => $entity->covers,
                    'images'      => $entity->images,
                    'videos'      => $entity->videos,
                    'name'        => $entity->findLang($language, 'name'),
                    'description' => $entity->findLang($language, 'description'),
                    'keywords'    => $entity->findLang($language, 'keywords'),
                    'remarks'     => $entity->findLang($language, 'remarks'),
                    'is_enabled'  => $entity->is_enabled,
                    'updated_at'  => $entity->updated_at
                ];
            }
        }
        $data['covers'] = $this->getlistOfCovers($code);
        $data['images'] = $this->getlistOfImages($code, true);

        if (config('wk-mall-shelf.onoff.morph-comment'))
            $data['comments'] = $this->getlistOfComments($entity);

        return $data;
    }
}
