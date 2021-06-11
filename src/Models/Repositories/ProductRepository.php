<?php

namespace WalkerChiu\MallShelf\Models\Repositories;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Forms\FormHasHostTrait;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryHasHostTrait;
use WalkerChiu\MorphComment\Models\Repositories\CommentRepositoryTrait;
use WalkerChiu\MorphImage\Models\Repositories\ImageRepositoryTrait;

class ProductRepository extends Repository
{
    use FormHasHostTrait;
    use RepositoryHasHostTrait;
    use CommentRepositoryTrait;
    use ImageRepositoryTrait;

    protected $entity;

    public function __construct()
    {
        $this->entity = App::make(config('wk-core.class.mall-shelf.product'));
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
     * @param Boolean $toArray
     * @return Array|Collection
     */
    public function list($host_type, $host_id, String $code, Array $data, $page = null, $nums = null, $is_enabled = null, $target = null, $target_is_enabled = null, $toArray = true)
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
                                        ->unless(empty($data['serial']), function ($query) use ($data) {
                                            return $query->where('serial', $data['serial']);
                                        })
                                        ->unless(empty($data['identifier']), function ($query) use ($data) {
                                            return $query->where('identifier', $data['identifier']);
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
                                        ->unless(empty($data['price_base']), function ($query) use ($data) {
                                            return $query->where('price_base', $data['price_base']);
                                        })
                                        ->unless(empty($data['price_base_min']), function ($query) use ($data) {
                                            return $query->where('price_base', '>=', $data['price_base_min']);
                                        })
                                        ->unless(empty($data['price_base_max']), function ($query) use ($data) {
                                            return $query->where('price_base', '<=', $data['price_base_max']);
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
                                        });
                            })
                            ->orderBy('updated_at', 'DESC')
                            ->get()
                            ->when(is_integer($page) && is_integer($nums), function ($query) use ($page, $nums) {
                                return $query->forPage($page, $nums);
                            });
        if ($toArray) {
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
                        'covers'      => $this->getlistOfCovers($code)
                    ])
                );
            }

            return $list;
        } else {
            return $records;
        }
    }

    /**
     * @param Product $entity
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
                'host_type'     => $entity->host_type,
                'host_id'       => $entity->host_id,
                'type'          => $entity->type,
                'attribute_set' => $entity->attribute_set,
                'serial'        => $entity->serial,
                'identifier'    => $entity->identifier,
                'cost'          => $entity->cost,
                'price_base'    => $entity->price_base,
                'covers'        => $entity->covers,
                'images'        => $entity->images,
                'videos'        => $entity->videos,
                'name'          => $entity->findLang($code, 'name'),
                'abstract'      => $entity->findLang($code, 'abstract'),
                'description'   => $entity->findLang($code, 'description'),
                'keywords'      => $entity->findLang($code, 'keywords'),
                'remarks'       => $entity->findLang($code, 'remarks'),
                'is_enabled'    => $entity->is_enabled,
                'updated_at'    => $entity->updated_at
            ];

        } elseif (is_array($code)) {
            foreach ($code as $language) {
                $data['basic'][$language] = [
                    'host_type'     => $entity->host_type,
                    'host_id'       => $entity->host_id,
                    'type'          => $entity->type,
                    'attribute_set' => $entity->attribute_set,
                    'serial'        => $entity->serial,
                    'identifier'    => $entity->identifier,
                    'cost'          => $entity->cost,
                    'price_base'    => $entity->price_base,
                    'covers'        => $entity->covers,
                    'images'        => $entity->images,
                    'videos'        => $entity->videos,
                    'name'          => $entity->findLang($language, 'name'),
                    'abstract'      => $entity->findLang($language, 'abstract'),
                    'description'   => $entity->findLang($language, 'description'),
                    'keywords'      => $entity->findLang($language, 'keywords'),
                    'remarks'       => $entity->findLang($language, 'remarks'),
                    'is_enabled'    => $entity->is_enabled,
                    'updated_at'    => $entity->updated_at
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
