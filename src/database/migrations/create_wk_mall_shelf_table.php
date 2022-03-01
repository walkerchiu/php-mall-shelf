<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateWkMallShelfTable extends Migration
{
    public function up()
    {
        Schema::create(config('wk-core.table.mall-shelf.products'), function (Blueprint $table) {
            $table->uuid('id');
            $table->nullableUuidMorphs('host');
            $table->string('type')->nullable();
            $table->string('attribute_set')->nullable();
            $table->string('serial')->nullable();
            $table->string('identifier')->nullable();
            $table->unsignedDecimal('cost', config('wk-mall-shelf.unsigned_decimal.precision'), config('wk-mall-shelf.unsigned_decimal.scale'))->nullable();
            $table->unsignedDecimal('price_base', config('wk-mall-shelf.unsigned_decimal.precision'), config('wk-mall-shelf.unsigned_decimal.scale'))->nullable();
            $table->json('covers')->nullable();
            $table->json('images')->nullable();
            $table->json('videos')->nullable();
            $table->boolean('is_enabled')->default(0);

            $table->timestampsTz();
            $table->softDeletes();

            $table->primary('id');
            $table->index('type');
            $table->index('attribute_set');
            $table->index('serial');
            $table->index('identifier');
            $table->index('is_enabled');
            $table->index(['host_type', 'host_id', 'is_enabled']);
        });
        if (!config('wk-mall-shelf.onoff.core-lang_core')) {
            Schema::create(config('wk-core.table.mall-shelf.products_lang'), function (Blueprint $table) {
                $table->uuid('id');
                $table->uuidMorphs('morph');
                $table->uuid('user_id')->nullable();
                $table->string('code');
                $table->string('key');
                $table->longText('value')->nullable();
                $table->boolean('is_current')->default(1);

                $table->timestampsTz();
                $table->softDeletes();

                $table->foreign('user_id')->references('id')
                    ->on(config('wk-core.table.user'))
                    ->onDelete('set null')
                    ->onUpdate('cascade');

                $table->primary('id');
            });
        }

        Schema::create(config('wk-core.table.mall-shelf.catalogs'), function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('product_id');
            $table->string('serial')->nullable();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->string('material')->nullable();
            $table->string('taste')->nullable();
            $table->unsignedDecimal('weight')->nullable();
            $table->unsignedDecimal('length')->nullable();
            $table->unsignedDecimal('width')->nullable();
            $table->unsignedDecimal('height')->nullable();
            $table->unsignedDecimal('cost', config('wk-mall-shelf.unsigned_decimal.precision'), config('wk-mall-shelf.unsigned_decimal.scale'))->nullable();
            $table->json('covers')->nullable();
            $table->json('images')->nullable();
            $table->json('videos')->nullable();
            $table->boolean('is_enabled')->default(0);

            $table->timestampsTz();
            $table->softDeletes();

            $table->foreign('product_id')->references('id')
                  ->on(config('wk-core.table.mall-shelf.products'))
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->primary('id');
            $table->index('serial');
            $table->index('color');
            $table->index('size');
            $table->index('material');
            $table->index('taste');
            $table->index('is_enabled');
        });
        if (!config('wk-mall-shelf.onoff.core-lang_core')) {
            Schema::create(config('wk-core.table.mall-shelf.catalogs_lang'), function (Blueprint $table) {
                $table->uuid('id');
                $table->uuidMorphs('morph');
                $table->uuid('user_id')->nullable();
                $table->string('code');
                $table->string('key');
                $table->longText('value')->nullable();
                $table->boolean('is_current')->default(1);

                $table->timestampsTz();
                $table->softDeletes();

                $table->foreign('user_id')->references('id')
                    ->on(config('wk-core.table.user'))
                    ->onDelete('set null')
                    ->onUpdate('cascade');

                $table->primary('id');
            });
        }

        Schema::create(config('wk-core.table.mall-shelf.stocks'), function (Blueprint $table) {
            $table->uuid('id');
            $table->nullableUuidMorphs('host');
            $table->string('type')->nullable();
            $table->string('attribute_set')->nullable();
            $table->string('sku')->nullable();
            $table->string('identifier');
            $table->uuid('product_id')->nullable();
            $table->uuid('catalog_id')->nullable();
            $table->unsignedDecimal('cost', config('wk-mall-shelf.unsigned_decimal.precision'), config('wk-mall-shelf.unsigned_decimal.scale'))->nullable();
            $table->unsignedDecimal('price_original', config('wk-mall-shelf.unsigned_decimal.precision'), config('wk-mall-shelf.unsigned_decimal.scale'))->nullable();
            $table->unsignedDecimal('price_discount', config('wk-mall-shelf.unsigned_decimal.precision'), config('wk-mall-shelf.unsigned_decimal.scale'))->nullable();
            $table->json('options')->nullable();
            $table->json('covers')->nullable();
            $table->json('images')->nullable();
            $table->json('videos')->nullable();
            $table->unsignedInteger('inventory')->nullable();
            $table->unsignedInteger('quantity')->nullable();
            $table->unsignedInteger('qty_per_order')->nullable();
            $table->unsignedDecimal('fee', config('wk-mall-shelf.unsigned_decimal.precision'), config('wk-mall-shelf.unsigned_decimal.scale'))->nullable();
            $table->unsignedDecimal('tax', config('wk-mall-shelf.unsigned_decimal.precision'), config('wk-mall-shelf.unsigned_decimal.scale'))->nullable();
            $table->unsignedDecimal('tip', config('wk-mall-shelf.unsigned_decimal.precision'), config('wk-mall-shelf.unsigned_decimal.scale'))->nullable();
            $table->unsignedBigInteger('weight')->nullable();
            $table->json('binding_supported')->nullable();
            $table->json('recommendation')->nullable();
            $table->boolean('is_new')->default(1);
            $table->boolean('is_featured')->default(0);
            $table->boolean('is_highlighted')->default(0);
            $table->boolean('is_sellable')->default(1);
            $table->boolean('is_enabled')->default(0);

            $table->timestampsTz();
            $table->softDeletes();

            $table->foreign('product_id')->references('id')
                  ->on(config('wk-core.table.mall-shelf.products'))
                  ->onDelete('set null')
                  ->onUpdate('cascade');
            $table->foreign('catalog_id')->references('id')
                  ->on(config('wk-core.table.mall-shelf.catalogs'))
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->primary('id');
            $table->index('type');
            $table->index('attribute_set');
            $table->index('sku');
            $table->index('identifier');
            $table->index('weight');
            $table->index('is_new');
            $table->index('is_featured');
            $table->index('is_highlighted');
            $table->index('is_sellable');
            $table->index('is_enabled');
            $table->index(['host_type', 'host_id', 'is_enabled']);
        });
        if (!config('wk-mall-shelf.onoff.core-lang_core')) {
            Schema::create(config('wk-core.table.mall-shelf.stocks_lang'), function (Blueprint $table) {
                $table->uuid('id');
                $table->uuidMorphs('morph');
                $table->uuid('user_id')->nullable();
                $table->string('code');
                $table->string('key');
                $table->longText('value')->nullable();
                $table->boolean('is_current')->default(1);

                $table->timestampsTz();
                $table->softDeletes();

                $table->foreign('user_id')->references('id')
                    ->on(config('wk-core.table.user'))
                    ->onDelete('set null')
                    ->onUpdate('cascade');

                $table->primary('id');
            });
        }

        Schema::create(config('wk-core.table.mall-shelf.relations'), function (Blueprint $table) {
            $table->uuid('id');
            $table->nullableUuidMorphs('host');
            $table->string('serial')->nullable();
            $table->json('relations');
            $table->boolean('is_enabled')->default(0);

            $table->timestampsTz();
            $table->softDeletes();

            $table->primary('id');
            $table->index('serial');
            $table->index('is_enabled');
        });
        if (!config('wk-mall-shelf.onoff.core-lang_core')) {
            Schema::create(config('wk-core.table.mall-shelf.relations_lang'), function (Blueprint $table) {
                $table->uuid('id');
                $table->uuidMorphs('morph');
                $table->uuid('user_id')->nullable();
                $table->string('code');
                $table->string('key');
                $table->longText('value')->nullable();
                $table->boolean('is_current')->default(1);

                $table->timestampsTz();
                $table->softDeletes();

                $table->foreign('user_id')->references('id')
                    ->on(config('wk-core.table.user'))
                    ->onDelete('set null')
                    ->onUpdate('cascade');

                $table->primary('id');
            });
        }
    }

    public function down() {
        Schema::dropIfExists(config('wk-core.table.mall-shelf.relations_lang'));
        Schema::dropIfExists(config('wk-core.table.mall-shelf.relations'));
        Schema::dropIfExists(config('wk-core.table.mall-shelf.stocks_lang'));
        Schema::dropIfExists(config('wk-core.table.mall-shelf.stocks'));
        Schema::dropIfExists(config('wk-core.table.mall-shelf.catalogs_lang'));
        Schema::dropIfExists(config('wk-core.table.mall-shelf.catalogs'));
        Schema::dropIfExists(config('wk-core.table.mall-shelf.products_lang'));
        Schema::dropIfExists(config('wk-core.table.mall-shelf.products'));
    }
}
