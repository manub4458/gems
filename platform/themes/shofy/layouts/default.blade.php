@extends(Theme::getThemeNamespace('layouts.base'))

@section('content')
    {!! apply_filters('theme_front_header_content', null) !!}

    <main>
        {!! Theme::breadcrumb()->render(Theme::getThemeNamespace('partials.breadcrumbs')) !!}

        <section class="tp-page-area pb-80 pt-50">
            <div class="container">
                {!! Theme::content() !!}
            </div>
        </section>
    </main>

    {!! apply_filters('theme_front_footer_content', null) !!}
@endsection
