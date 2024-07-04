@php
    Theme::set('pageTitle', $page->name);

    if ($breadcrumbStyle = $page->getMetaData('breadcrumb_style', true)) {
        Theme::set('breadcrumbStyle', $breadcrumbStyle);
    }

    if ($breadcrumbBackground = $page->getMetaData('breadcrumb_background', true)) {
        Theme::set('breadcrumbBackground', $breadcrumbBackground);
    }

    Theme::set('isHomePage', BaseHelper::isHomePage($page->id));
@endphp

@if (BaseHelper::isHomepage($page->id))
    {!! apply_filters(PAGE_FILTER_FRONT_PAGE_CONTENT, BaseHelper::clean($page->content), $page) !!}
@else
    <div class="ck-content">
        {!! apply_filters(PAGE_FILTER_FRONT_PAGE_CONTENT, BaseHelper::clean($page->content), $page) !!}
    </div>
@endif
