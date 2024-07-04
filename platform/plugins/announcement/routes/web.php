<?php

use ArchiElite\Announcement\Http\Controllers\AnnouncementController;
use ArchiElite\Announcement\Http\Controllers\PublicController;
use ArchiElite\Announcement\Http\Controllers\Settings\AnnouncementSettingController;
use Botble\Base\Facades\AdminHelper;
use Botble\Base\Http\Middleware\RequiresJsonRequestMiddleware;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Facades\Route;

Theme::registerRoutes(function () {
    Route::get('ajax/announcements', [PublicController::class, 'ajaxGetAnnouncements'])
        ->middleware(RequiresJsonRequestMiddleware::class)
        ->name('public.ajax.announcements');
});

AdminHelper::registerRoutes(function () {
    Route::prefix('announcements')
        ->name('announcements.')
        ->group(function () {
            Route::resource('/', AnnouncementController::class)->parameters(['' => 'announcement']);

            Route::group(['prefix' => 'settings', 'permission' => 'announcements.settings'], function () {
                Route::get('/', [AnnouncementSettingController::class, 'edit'])->name('settings');
                Route::put('/', [AnnouncementSettingController::class, 'update'])->name('settings.update');
            });
        });
});
