<?php

use App\AdminApi\Middlewares\VerifyAdminActivationMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:admin-api',
    VerifyAdminActivationMiddleware::class
])->group(function () {
    Route::prefix('acquirer-bin')->group(function () {
        Route::get('/', 'Admin@getAcquirerBins');
        Route::post('/', 'Admin@createAcquirerBin');
        Route::get('/{acquirer_bin_id}', 'Admin@getAcquirerBin');
        Route::put('/{acquirer_bin_id}', 'Admin@updateAcquirerBin');
        Route::delete('/{acquirer_bin_id}', 'Admin@deleteAcquirerBin');
    });

    Route::prefix('accumulators')->group(function () {
        Route::get('/', 'Admin@getAccumulators');
        Route::post('/', 'Admin@createAccumulator');
        Route::get('/{accumulator_id}', 'Admin@getAccumulator')->whereNumber('accumulator_id');
        Route::put('/{accumulator_id}', 'Admin@updateAccumulator')->whereNumber('accumulator_id');
        Route::delete('/{accumulator_id}', 'Admin@deleteAccumulator')->whereNumber('accumulator_id');

        Route::get('/types', 'Admin@getAccumulatorTypes');
        Route::get('/value-types', 'Admin@getAccumulatorValueTypes');
    });
});
