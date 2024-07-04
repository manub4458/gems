@php
    $hasSidebar = $hasSidebar ?? true;
@endphp

<div class="tp-blog-grid-item-wrapper">
    <div class="row tp-gx-30">
        @foreach ($posts as $post)
            <div @class(['col-md-6' => $hasSidebar, 'col-md-4' => ! $hasSidebar])>
                @include(Theme::getThemeNamespace('views.partials.post-grid-item'), compact('post'))
            </div>
        @endforeach
    </div>
</div>
