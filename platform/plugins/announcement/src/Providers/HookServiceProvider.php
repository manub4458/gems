<?php

namespace ArchiElite\Announcement\Providers;

use ArchiElite\Announcement\AnnouncementHelper;
use Botble\Base\Facades\Html;
use Botble\Base\Supports\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! defined('THEME_FRONT_BODY')) {
            return;
        }

        add_filter(AnnouncementHelper::isThemeBuiltIn() ? 'announcement_display_html' : THEME_FRONT_BODY, function (?string $html): ?string {
            $html .= Html::style(asset('vendor/core/plugins/announcement/css/announcement.css'));

            if (AnnouncementHelper::isLazyLoadingEnabled()) {
                return $html . sprintf(
                    '<div data-bb-toggle="announcement-lazy-loading" data-url="%s"></div>',
                    route('public.ajax.announcements')
                );
            }

            return $html . AnnouncementHelper::render();
        }, 99);

        add_filter(THEME_FRONT_FOOTER, function (?string $footer): string {
            return $footer . Html::script(asset('vendor/core/plugins/announcement/js/announcement.js'));
        }, 99);
    }
}
