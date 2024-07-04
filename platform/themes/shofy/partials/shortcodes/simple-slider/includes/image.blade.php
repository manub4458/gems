@php
    $tabletImage = $slider->getMetaData('tablet_image', true) ?: $slider->image;
    $mobileImage = $slider->getMetaData('mobile_image', true) ?: $tabletImage;
@endphp

@if($slider->link)
    <a href="{{ url($slider->link) }}">
@endif
    <picture>
        <source
            srcset="{{ RvMedia::getImageUrl($slider->image, null, false, RvMedia::getDefaultImage()) }}"
            media="(min-width: 1200px)"
        />
        <source
            srcset="{{ RvMedia::getImageUrl($tabletImage, null, false, RvMedia::getDefaultImage()) }}"
            media="(min-width: 768px)"
        />
        <source
            srcset="{{ RvMedia::getImageUrl($mobileImage, null, false, RvMedia::getDefaultImage()) }}"
            media="(max-width: 767px)"
        />
        {{ RvMedia::image($slider->image, $slider->title, attributes: $sliderAttributes ?? ['loading' => 'eager']) }}
    </picture>
@if($slider->link)
    </a>
@endif

@php
    unset($sliderAttributes);
@endphp
