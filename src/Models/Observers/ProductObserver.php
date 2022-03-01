<?php

namespace WalkerChiu\MallShelf\Models\Observers;

class ProductObserver
{
    /**
     * Handle the entity "retrieved" event.
     *
     * @param Entity  $entity
     * @return void
     */
    public function retrieved($entity)
    {
        //
    }

    /**
     * Handle the entity "creating" event.
     *
     * @param Entity  $entity
     * @return void
     */
    public function creating($entity)
    {
        //
    }

    /**
     * Handle the entity "created" event.
     *
     * @param Entity  $entity
     * @return void
     */
    public function created($entity)
    {
        //
    }

    /**
     * Handle the entity "updating" event.
     *
     * @param Entity  $entity
     * @return void
     */
    public function updating($entity)
    {
        //
    }

    /**
     * Handle the entity "updated" event.
     *
     * @param Entity  $entity
     * @return void
     */
    public function updated($entity)
    {
        if (!$entity->is_enabled) {
            config('wk-core.class.mall-shelf.stock')
                ::where('product_id', $entity->id)
                ->update(['is_enabled' => 0]);
        }
    }

    /**
     * Handle the entity "saving" event.
     *
     * @param Entity  $entity
     * @return void
     */
    public function saving($entity)
    {
        if (
            !is_null($entity->identifier)
            && config('wk-core.class.mall-shelf.product')
                ::where('id', '<>', $entity->id)
                ->where('host_type', $entity->host_type)
                ->where('host_id', $entity->host_id)
                ->where('identifier', $entity->identifier)
                ->exists()
        )
            return false;
    }

    /**
     * Handle the entity "saved" event.
     *
     * @param Entity  $entity
     * @return void
     */
    public function saved($entity)
    {
        //
    }

    /**
     * Handle the entity "deleting" event.
     *
     * @param Entity  $entity
     * @return void
     */
    public function deleting($entity)
    {
        if (!$entity->stocks->isEmpty())
            return false;
    }

    /**
     * Handle the entity "deleted" event.
     *
     * Its Lang will be automatically removed by database.
     * 
     * @param Entity  $entity
     * @return void
     */
    public function deleted($entity)
    {
        if ($entity->isForceDeleting()) {
            $entity->langs()->withTrashed()
                            ->forceDelete();

            $records = $entity->catalogs()->withTrashed()->get();
            foreach ($records as $recoed) {
                $recoed->forceDelete();
            }

            if (
                config('wk-mall-shelf.onoff.morph-image')
                && !empty(config('wk-core.class.morph-image.image'))
            ) {
                $records = $entity->images()->withTrashed()->get();
                foreach ($records as $recoed) {
                    $recoed->forceDelete();
                }
                foreach ($entity->stocks()->withTrashed() as $stock) {
                    $records = $stock->images()->withTrashed()->get();
                    foreach ($records as $recoed) {
                        $recoed->forceDelete();
                    }
                    $stock->forceDelete();
                }
            }
            if (
                config('wk-mall-shelf.onoff.morph-category')
                && !empty(config('wk-core.class.morph-category.category'))
            ) {
                $entity->categories()->detach();
            }
            if (
                config('wk-mall-shelf.onoff.morph-tag')
                && !empty(config('wk-core.class.morph-tag.tag'))
                && is_iterable($entity->tags())
            ) {
                $entity->tags()->detach();
            }
            if (
                config('wk-mall-shelf.onoff.morph-comment')
                && !empty(config('wk-core.class.morph-comment.comment'))
            ) {
                $records = $entity->comments()->withTrashed()->get();
                foreach ($records as $recoed) {
                    $recoed->forceDelete();
                }
            }
        }

        if (!config('wk-mall-shelf.soft_delete')) {
            $entity->forceDelete();
        }
    }

    /**
     * Handle the entity "restoring" event.
     *
     * @param Entity  $entity
     * @return void
     */
    public function restoring($entity)
    {
        if (
            !is_null($entity->identifier)
            && config('wk-core.class.mall-shelf.product')
                ::where('id', '<>', $entity->id)
                ->where('host_type', $entity->host_type)
                ->where('host_id', $entity->host_id)
                ->where('identifier', $entity->identifier)
                ->exists()
        )
            return false;
    }

    /**
     * Handle the entity "restored" event.
     *
     * @param Entity  $entity
     * @return void
     */
    public function restored($entity)
    {
        //
    }
}
