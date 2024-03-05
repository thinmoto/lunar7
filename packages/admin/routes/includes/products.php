<?php

use Illuminate\Support\Facades\Route;
use Lunar\Hub\Http\Controllers\ProductsController;
use Lunar\Hub\Http\Livewire\Pages\Products\ProductCreate;
use Lunar\Hub\Http\Livewire\Pages\Products\ProductShow;
use Lunar\Hub\Http\Livewire\Pages\Products\ProductsIndex;
use Lunar\Hub\Http\Livewire\Pages\Products\Variants\VariantShow;
use Lunar\Models\Product;

/**
 * Channel routes.
 */
Route::group([
    'middleware' => 'can:catalogue:manage-products',
], function () {
    Route::get('/', ProductsIndex::class)->name('hub.products.index');
    Route::get('create', ProductCreate::class)->name('hub.products.create');

    Route::group([
        'prefix' => '{product}',
    ], function () {
        Route::get('/', ProductShow::class)->name('hub.products.show');
        Route::get('/clone', [ProductsController::class, 'clone'])->name('hub.products.clone');

        Route::group([
            'prefix' => 'variants',
        ], function () {
            Route::get('{variant}', VariantShow::class)->name('hub.products.variants.show');
        });
    });
});