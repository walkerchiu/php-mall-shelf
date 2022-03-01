<?php

namespace WalkerChiu\MallShelf\Models\Entities;

use WalkerChiu\Core\Models\Entities\UuidEntity;
use WalkerChiu\Core\Models\Entities\LangTrait;
use WalkerChiu\MorphImage\Models\Entities\ImageTrait;
use WalkerChiu\MorphTag\Models\Entities\TagTrait;

class Product extends UuidEntity
{
    use LangTrait;
    use ImageTrait;
    use TagTrait;



    /**
     * Create a new instance.
     *
     * @param Array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->table = config('wk-core.table.mall-shelf.products');

        $this->fillable = array_merge($this->fillable, [
            'host_type', 'host_id',
            'type',
            'attribute_set',
            'serial',
            'identifier',
            'cost', 'price_base',
            'covers', 'images', 'videos'
        ]);

        $this->casts = array_merge($this->casts, [
            'covers' => 'json',
            'images' => 'json',
            'videos' => 'json'
        ]);

        parent::__construct($attributes);
    }

    /**
     * Get it's lang entity.
     *
     * @return Lang
     */
    public function lang()
    {
        if (
            config('wk-core.onoff.core-lang_core')
            || config('wk-mall-shelf.onoff.core-lang_core')
        ) {
            return config('wk-core.class.core.langCore');
        } else {
            return config('wk-core.class.mall-shelf.productLang');
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function langs()
    {
        if (
            config('wk-core.onoff.core-lang_core')
            || config('wk-mall-shelf.onoff.core-lang_core')
        ) {
            return $this->langsCore();
        } else {
            return $this->hasMany(config('wk-core.class.mall-shelf.productLang'), 'morph_id', 'id');
        }
    }

    /**
     * Get the owning host model.
     */
    public function host()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function catalogs()
    {
        return $this->hasMany(config('wk-core.class.mall-shelf.catalog'), 'product_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stocks()
    {
        return $this->hasMany(config('wk-core.class.mall-shelf.stock'), 'product_id', 'id');
    }

    /**
     * Get all of the comments for the product.
     *
     * @param Int  $user_id
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments($user_id = null)
    {
        return $this->morphMany(config('wk-core.class.morph-comment.comment'), 'morph')
                    ->when($user_id, function ($query, $user_id) {
                                return $query->where('user_id', $user_id);
                            });
    }

    /**
     * Get all of the categories for the product.
     *
     * @param String  $type
     * @param Bool    $is_enabled
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function categories($type = null, $is_enabled = null)
    {
        $table = config('wk-core.table.morph-category.categories_morphs');
        return $this->morphToMany(config('wk-core.class.morph-category.category'), 'morph', $table)
                    ->when(is_null($type), function ($query) {
                          return $query->whereNull('type');
                      }, function ($query) use ($type) {
                          return $query->where('type', $type);
                      })
                    ->unless( is_null($is_enabled), function ($query) use ($is_enabled) {
                        return $query->where('is_enabled', $is_enabled);
                    });
    }
}
