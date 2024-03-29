<?php

namespace WalkerChiu\MallShelf\Models\Entities;

use WalkerChiu\Core\Models\Entities\UuidEntity;
use WalkerChiu\Core\Models\Entities\LangTrait;

class Relation extends UuidEntity
{
    use LangTrait;



    /**
     * Create a new instance.
     *
     * @param Array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->table = config('wk-core.table.mall-shelf.relations');

        $this->fillable = array_merge($this->fillable, [
            'host_type', 'host_id',
            'serial',
            'relations'
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
            return config('wk-core.class.mall-shelf.relationLang');
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
            return $this->langsCore($host_type, $host_id);
        } else {
            return $this->hasMany(config('wk-core.class.mall-shelf.relationLang'), 'morph_id', 'id');
        }
    }
}
