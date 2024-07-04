<?php

use Botble\Base\Facades\AdminHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\RequestLog\Http\Controllers'], function () {
    AdminHelper::registerRoutes(function () {
        Route::group(['prefix' => 'request-logs', 'as' => 'request-log.'], function () {
            Route::resource('', 'RequestLogController')
                ->only(['index', 'destroy'])->parameters(['' => 'request-log']);

            Route::get('widgets/request-errors', [
                'as' => 'widget.request-errors',
                'uses' => 'RequestLogController@getWidgetRequestErrors',
                'permission' => 'request-log.index',
            ]);

            Route::delete('items/empty', [
                'as' => 'empty',
                'uses' => 'RequestLogController@deleteAll',
                'permission' => 'request-log.destroy',
            ]);
        });
    });
});
