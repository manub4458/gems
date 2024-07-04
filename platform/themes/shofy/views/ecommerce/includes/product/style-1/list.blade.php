<div class="tp-product-list-item d-md-flex">
    <div class="tp-product-list-thumb p-relative fix">
        <a href="{{ $product->url }}">
            {{ RvMedia::image($product->image, $product->name, 'medium', useDefaultImage: true) }}
        </a>

        <div class="tp-product-action-2 tp-product-action-blackStyle">
            <div class="tp-product-action-item-2 d-flex flex-column">
                <button
                    type="button"
                    class="tp-product-action-btn-2 tp-product-quick-view-btn"
                    title="Quick view"
                    data-bs-toggle="modal"
                    data-bs-target="#product-quick-view-modal"
                    data-url="{{ route('public.ajax.quick-view', $product) }}"
                >
                    <svg width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M8.99948 5.06828C7.80247 5.06828 6.82956 6.04044 6.82956 7.23542C6.82956 8.42951 7.80247 9.40077 8.99948 9.40077C10.1965 9.40077 11.1703 8.42951 11.1703 7.23542C11.1703 6.04044 10.1965 5.06828 8.99948 5.06828ZM8.99942 10.7482C7.0581 10.7482 5.47949 9.17221 5.47949 7.23508C5.47949 5.29705 7.0581 3.72021 8.99942 3.72021C10.9407 3.72021 12.5202 5.29705 12.5202 7.23508C12.5202 9.17221 10.9407 10.7482 8.99942 10.7482Z"
                            fill="currentColor"
                        />
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M1.41273 7.2346C3.08674 10.9265 5.90646 13.1215 8.99978 13.1224C12.0931 13.1215 14.9128 10.9265 16.5868 7.2346C14.9128 3.54363 12.0931 1.34863 8.99978 1.34773C5.90736 1.34863 3.08674 3.54363 1.41273 7.2346ZM9.00164 14.4703H8.99804H8.99714C5.27471 14.4676 1.93209 11.8629 0.0546754 7.50073C-0.0182251 7.33091 -0.0182251 7.13864 0.0546754 6.96883C1.93209 2.60759 5.27561 0.00288103 8.99714 0.000185582C8.99894 -0.000712902 8.99894 -0.000712902 8.99984 0.000185582C9.00164 -0.000712902 9.00164 -0.000712902 9.00254 0.000185582C12.725 0.00288103 16.0676 2.60759 17.945 6.96883C18.0188 7.13864 18.0188 7.33091 17.945 7.50073C16.0685 11.8629 12.725 14.4676 9.00254 14.4703H9.00164Z"
                            fill="currentColor"
                        />
                    </svg>
                    <span class="tp-product-tooltip tp-product-tooltip-right">{{ __('Quick View') }}</span>
                </button>
                @if (EcommerceHelper::isWishlistEnabled())
                    <button
                        type="button"
                        @class(['tp-product-action-btn-2 tp-product-add-to-wishlist-btn', 'active' => $isInWishlist = is_product_in_wishlist($product->original_product->id)])
                        data-bb-toggle="add-to-wishlist" title="Add to wishlist"
                        data-url="{{ route('public.wishlist.add', $product) }}"
                        data-add-text="{{ __('Add To Wishlist') }}"
                        data-remove-text="{{ __('Remove From Wishlist') }}"
                    >
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M1.60355 7.98635C2.83622 11.8048 7.7062 14.8923 9.0004 15.6565C10.299 14.8844 15.2042 11.7628 16.3973 7.98985C17.1806 5.55102 16.4535 2.46177 13.5644 1.53473C12.1647 1.08741 10.532 1.35966 9.40484 2.22804C9.16921 2.40837 8.84214 2.41187 8.60476 2.23329C7.41078 1.33952 5.85105 1.07778 4.42936 1.53473C1.54465 2.4609 0.820172 5.55014 1.60355 7.98635ZM9.00138 17.0711C8.89236 17.0711 8.78421 17.0448 8.68574 16.9914C8.41055 16.8417 1.92808 13.2841 0.348132 8.3872C0.347252 8.3872 0.347252 8.38633 0.347252 8.38633C-0.644504 5.30321 0.459792 1.42874 4.02502 0.284605C5.69904 -0.254635 7.52342 -0.0174044 8.99874 0.909632C10.4283 0.00973263 12.3275 -0.238878 13.9681 0.284605C17.5368 1.43049 18.6446 5.30408 17.6538 8.38633C16.1248 13.2272 9.59485 16.8382 9.3179 16.9896C9.21943 17.0439 9.1104 17.0711 9.00138 17.0711Z"
                                fill="currentColor"
                            />
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M14.203 6.67473C13.8627 6.67473 13.5743 6.41474 13.5462 6.07159C13.4882 5.35202 13.0046 4.7445 12.3162 4.52302C11.9689 4.41097 11.779 4.04068 11.8906 3.69666C12.0041 3.35175 12.3724 3.16442 12.7206 3.27297C13.919 3.65901 14.7586 4.71561 14.8615 5.96479C14.8905 6.32632 14.6206 6.64322 14.2575 6.6721C14.239 6.67385 14.2214 6.67473 14.203 6.67473Z"
                                fill="currentColor"
                            />
                        </svg>
                        <span class="tp-product-tooltip tp-product-tooltip-right">
                                @if ($isInWishlist)
                                {{ __('Remove From Wishlist') }}
                            @else
                                {{ __('Add To Wishlist') }}
                            @endif
                            </span>
                    </button>
                @endif
                @if (EcommerceHelper::isCompareEnabled())
                    <button
                        type="button"
                        @class(['tp-product-action-btn-2 tp-product-add-to-compare-btn', 'active' => $isInCompare = is_product_in_compare($product->original_product->id)])
                        data-bb-toggle="add-to-compare" title="Add to compare"
                        data-url="{{ route('public.compare.add', $product) }}"
                        data-remove-url="{{ route('public.compare.remove', $product) }}"
                        data-add-text="{{ __('Add To Compare') }}"
                        data-remove-text="{{ __('Remove From Compare') }}"
                    >
                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.4144 6.16828L14 3.58412L11.4144 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M1.48883 3.58374L14 3.58374" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M4.07446 8.32153L1.48884 10.9057L4.07446 13.4898" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M14 10.9058H1.48883" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span class="tp-product-tooltip tp-product-tooltip-right">
                            @if ($isInCompare)
                                {{ __('Remove From Compare') }}
                            @else
                                {{ __('Add To Compare') }}
                            @endif
                        </span>
                    </button>
                @endif
            </div>
        </div>
    </div>
    <div class="tp-product-list-content">
        <div class="tp-product-content-2 pt-15">
            {!! apply_filters('ecommerce_before_product_item_content_renderer', null, $product) !!}

            @if (is_plugin_active('marketplace') && $product->store)
                <div class="tp-product-tag-2">
                    <a href="{{ $product->store->url }}">{{ $product->store->name }}</a>
                </div>
            @endif
            <h3 class="tp-product-title-2">
                <a href="{{ $product->url }}" title="{{ $product->name }}">
                    {!! BaseHelper::clean($product->name) !!}
                </a>
            </h3>

            @if(EcommerceHelper::isReviewEnabled())
                @include(Theme::getThemeNamespace('views.ecommerce.includes.product.style-1.rating'))
            @endif

            @include(Theme::getThemeNamespace('views.ecommerce.includes.product.style-1.price'))

            <p class="line-clamp-2">{{ strip_tags($product->description) }}</p>

            @if (EcommerceHelper::isCartEnabled())
                <div class="tp-product-list-add-to-cart">
                    <button
                        class="tp-product-list-add-to-cart-btn"
                        data-bb-toggle="add-to-cart"
                        data-url="{{ route('public.cart.add-to-cart') }}"
                        data-id="{{ $product->original_product->id }}"
                        {!! EcommerceHelper::jsAttributes('add-to-cart', $product) !!}
                    >
                        {{ __('Add To Cart') }}
                    </button>
                </div>
            @endif


            {!! apply_filters('ecommerce_after_product_item_content_renderer', null, $product) !!}
        </div>
    </div>
</div>
