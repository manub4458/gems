@extends(Theme::getThemeNamespace('layouts.base'))

@section('content')
    {!! apply_filters('theme_front_header_content', null) !!}

    <main>
        {!! Theme::breadcrumb()->render(Theme::getThemeNamespace('partials.breadcrumbs')) !!}

        {!! Theme::content() !!}
    </main>

    {!! apply_filters('theme_front_footer_content', null) !!}
@endsection
