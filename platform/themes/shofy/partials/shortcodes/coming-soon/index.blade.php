<section class="section container box-coming-soon pt-100 pb-100 overflow-hidden">
    @if($shortcode->image)
        <div class="row align-items-center">
            <div class="col-lg-5 mb-30">
        @endif
                @if ($countdownTime)
                    @include(Theme::getThemeNamespace('views.ecommerce.includes.product.countdown'), ['endDate' => $countdownTime])
                @endif

                @if($shortcode->title)
                    <h3 class="color-brand-2 wow animate__animated animate__fadeIn">
                        {!! BaseHelper::clean($shortcode->title) !!}
                    </h3>
                @endif

                {!! $form->renderForm() !!}

                <div class="mt-30 footer-info">
                    <ul class="list-wrap">
                        @if ($address = $shortcode->address)
                            <li>
                                <p><x-core::icon name="ti ti-map-pin" class="me-1" /> {!! BaseHelper::clean($address) !!}</p>
                            </li>
                        @endif

                        @if ($hotline = $shortcode->hotline)
                            <li>
                                <p>
                                    <x-core::icon name="ti ti-phone" class="me-1" /> <a href="tel:{{ $hotline }}" dir="ltr">{{ $hotline }}</a>
                                </p>
                            </li>
                        @endif

                        @if ($businessHours = $shortcode->business_hours)
                            <li>
                                <p><x-core::icon name="ti ti-clock" class="me-1" /> {!! BaseHelper::clean(nl2br($businessHours)) !!}</p>
                            </li>
                        @endif
                    </ul>
                </div>

                @if($shortcode->show_social_links ?? true)
                    @if($socialLinks = Theme::getSocialLinks())
                        <div class="tp-footer-social gap-2">
                            @foreach($socialLinks as $socialLink)
                                @continue(! $socialLink->getUrl() || ! $socialLink->getIconHtml())

                                <a {!! $socialLink->getAttributes() !!}>{{ $socialLink->getIconHtml() }}</a>
                            @endforeach
                        </div>
                    @endif
                @endif

                @if($shortcode->image)
            </div>
            <div class="col-lg-7 mb-30">
                {{ RvMedia::image($shortcode->image, $shortcode->title, attributes: ['class' => 'coming-soon-image']) }}
            </div>
            @endif
        </div>
</section>
