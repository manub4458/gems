<?php

use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\FieldOptions\UiSelectorFieldOption;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\UiSelectorField;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Support\Services\Cache\Cache;
use Botble\Theme\Facades\Theme;
use Botble\Widget\AbstractWidget;
use Botble\Widget\Forms\WidgetForm;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ProductCategoriesWidget extends AbstractWidget
{
    public function __construct()
    {
        parent::__construct([
            'name' => __('Product Categories'),
            'description' => __('List of product categories'),
            'categories' => [],
            'style' => 'simple-text',
            'display_children' => false,
            'centered_text' => false,
        ]);
    }

    protected function data(): array|Collection
    {
        $categoryIds = $this->getConfig('categories');

        if (empty($categoryIds)) {
            return [
                'categories' => collect(),
            ];
        }

        $cache = new Cache(app('cache'), ProductCategory::class);

        $cacheKey = 'ecommerce_categories_for_widgets_' . md5($cache->generateCacheKeyFromInput() . serialize($this->getConfig()));

        if ($cache->has($cacheKey)) {
            $categories = $cache->get($cacheKey);
        } else {
            $with = ['slugable'];

            if ($this->getConfig('display_children')) {
                $with[] = 'activeChildren';
            }

            $categories = ProductCategory::query()
                ->wherePublished()
                ->whereIn('id', $categoryIds)
                ->withCount('products')
                ->with($with)
                ->orderBy('order')
                ->get();

            $cache->put($cacheKey, $categories, Carbon::now()->addHours(2));
        }

        $style = in_array($this->getConfig('style'), ['simple-text', 'grid', 'slider']) ? $this->getConfig('style') : 'simple-text';

        return compact('categories', 'style');
    }

    protected function settingForm(): WidgetForm|string|null
    {
        return WidgetForm::createFromArray($config = $this->getConfig())
            ->add(
                'style',
                UiSelectorField::class,
                UiSelectorFieldOption::make()
                    ->choices([
                        'simple-text' => [
                            'label' => __('Simple Text'),
                            'image' => Theme::asset()->url('images/ecommerce-categories/simple-text.png'),
                        ],
                        'grid' => [
                            'label' => __('Grid'),
                            'image' => Theme::asset()->url('images/ecommerce-categories/grid.png'),
                        ],
                        'slider' => [
                            'label' => __('Slider'),
                            'image' => Theme::asset()->url('images/shortcodes/ecommerce-categories/slider.png'),
                        ],
                    ])
                    ->collapsible('style')
                    ->toArray(),
            )
            ->add(
                'name',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Name'))
                    ->toArray(),
            )
            ->add(
                'categories',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content(view('plugins/ecommerce::widgets.partials.select-product-categories', compact('config')) . '<br>')
                    ->toArray()
            )
            ->add(
                'display_children',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(__('Display children categories'))
                    ->collapseTrigger('style', 'grid', $config['style'] === 'grid')
                    ->toArray(),
            )
            ->add(
                'centered_text',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(__('Centered text'))
                    ->collapseTrigger('style', 'grid', $config['style'] === 'grid')
                    ->toArray(),
            );
    }

    protected function requiredPlugins(): array
    {
        return ['ecommerce'];
    }
}
