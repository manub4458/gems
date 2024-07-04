<section class="tp-blog-area pt-110 pb-120">
    <div class="container">
        {!! Theme::partial('section-title', ['shortcode' => $shortcode, 'class' => 'text-center mb-40']) !!}

        <div class="row">
            @foreach($posts as $post)
                <div class="col-xl-4 col-lg-4 col-md-6">
                    <div class="tp-blog-item-2 mb-40">
                        <div class="tp-blog-thumb-2 p-relative fix">
                            <a href="{{ $post->url }}">
                                {{ RvMedia::image($post->image, $post->name) }}
                            </a>
                            <div class="tp-blog-meta-date-2">
                                <span>{{ Theme::formatDate($post->created_at) }}</span>
                            </div>
                        </div>
                        <div class="tp-blog-content-2 has-thumbnail">
                            <div class="tp-blog-meta-2 me-1">
                                <span>
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M12.636 8.14182L8.14808 12.6297C8.03182 12.7461 7.89375 12.8384 7.74178 12.9014C7.58981 12.9644 7.42691 12.9969 7.26239 12.9969C7.09788 12.9969 6.93498 12.9644 6.78301 12.9014C6.63104 12.8384 6.49297 12.7461 6.37671 12.6297L1 7.25926V1H7.25926L12.636 6.37671C12.8691 6.61126 13 6.92854 13 7.25926C13 7.58998 12.8691 7.90727 12.636 8.14182V8.14182Z"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                        <path d="M4.12964 4.12988H4.13694" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>

                                @if($post->tags->isNotEmpty())
                                    @foreach($post->tags as $tag)
                                        <a href="{{ $tag->url }}">{{ $tag->name }}@if(!$loop->last), @endif</a>
                                    @endforeach
                                @endif
                            </div>
                            <h3 class="tp-blog-title-2">
                                <a href="{{ $post->url }}">{!! BaseHelper::clean($post->name) !!}</a>
                            </h3>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if(($buttonLabel = $shortcode->button_label) && ($buttonUrl = $shortcode->button_url ?: get_blog_page_url()))
            <div class="row">
                <div class="col-xl-12">
                    <div class="tp-blog-more-2 mt-10 text-center">
                        <a href="{{ $buttonUrl }}" class="tp-btn tp-btn-border tp-btn-border-sm">
                            {!! BaseHelper::clean($buttonLabel) !!}
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
