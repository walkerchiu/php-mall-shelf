<?php

namespace WalkerChiu\MallShelf\Models\Entities;

use WalkerChiu\Core\Models\Entities\UuidEntity;
use WalkerChiu\Core\Models\Entities\LangTrait;
use WalkerChiu\MorphImage\Models\Entities\ImageTrait;
use WalkerChiu\MorphTag\Models\Entities\TagTrait;

class Stock extends UuidEntity
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
        $this->table = config('wk-core.table.mall-shelf.stocks');

        $this->fillable = array_merge($this->fillable, [
            'host_type', 'host_id',
            'type',
            'attribute_set',
            'sku',
            'identifier',
            'product_id', 'catalog_id',
            'cost', 'price_original', 'price_discount', 'inventory', 'quantity', 'qty_per_order',
            'options', 'covers', 'images', 'videos',
            'fee', 'tax', 'tip',
            'weight',
            'binding_supported', 'recommendation',
            'is_new', 'is_featured', 'is_highlighted', 'is_sellable'
        ]);

        $this->casts = array_merge($this->casts, [
            'options'           => 'json',
            'covers'            => 'json',
            'images'            => 'json',
            'videos'            => 'json',
            'binding_supported' => 'json',
            'recommendation'    => 'json',
            'is_new'            => 'boolean',
            'is_featured'       => 'boolean',
            'is_highlighted'    => 'boolean',
            'is_sellable'       => 'boolean'
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
            return config('wk-core.class.mall-shelf.stockLang');
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
            return $this->hasMany(config('wk-core.class.mall-shelf.stockLang'), 'morph_id', 'id');
        }
    }

    /**
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfNew($query)
    {
        return $query->where('is_new', 1);
    }

    /**
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfNotNew($query)
    {
        return $query->where('is_new', 0);
    }

    /**
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfFeatured($query)
    {
        return $query->where('is_featured', 1);
    }

    /**
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfNotFeatured($query)
    {
        return $query->where('is_featured', 0);
    }

    /**
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfSellable($query)
    {
        return $query->where('is_sellable', 1);
    }

    /**
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfUnSellable($query)
    {
        return $query->where('is_sellable', 0);
    }

    /**
     * Get the owning host model.
     */
    public function host()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(config('wk-core.class.mall-shelf.product'), 'product_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function catalog()
    {
        return $this->belongsTo(config('wk-core.class.mall-shelf.catalog'), 'catalog_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wishlist()
    {
        return $this->hasMany(config('wk-core.class.mall-wishlist.item'), 'stock_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function itemsInCart()
    {
        return $this->hasMany(config('wk-core.class.mall-cart.item'), 'stock_id', 'id');
    }

    /**
     * Get all of the comments for the stock.
     *
     * @param String  $user_id
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
     * Get all of the categories for the stock.
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
