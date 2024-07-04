<?php

namespace Botble\Slug\Providers;

use Botble\Base\Facades\Form;
use Botble\Base\Supports\ServiceProvider;

class FormServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->booted(function () {
            Form::component('permalink', 'packages/slug::permalink', [
                'name',
                'value' => null,
                'id' => null,
                'prefix' => '',
                'preview' => false,
                'attributes' => [],
                'editable' => true,
                'model' => '',
            ]);
        });
    }
}
