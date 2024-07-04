@extends(Theme::getThemeNamespace('layouts.base'))

@section('content')
    <main>
        {!! Theme::content() !!}
    </main>
@endsection
