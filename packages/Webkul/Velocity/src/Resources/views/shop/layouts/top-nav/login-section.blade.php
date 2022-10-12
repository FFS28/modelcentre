{!! view_render_event('bagisto.shop.layout.header.account-item.before') !!}

<div id="account">
    @if(auth()->guard('customer')->user())
        <div class="d-inline-block welcome-content dropdown-toggle" style="margin-top: 17px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="white"
                class="bi bi-person-circle" viewBox="0 0 20 16">
                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                <path fill-rule="evenodd"
                    d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
            </svg>
            <span class="fs12 fw6 cart-text" style="color: white">
                {{ __('velocity::app.header.welcome-message', [
                        'customer_name' => auth()->guard('customer')->user()
                            ? auth()->guard('customer')->user()->first_name
                            : trans('velocity::app.header.guest')
                        ]
                    )
                }}
            </span>
            <span class="rango-arrow-down"></span>
        </div>
    @else
        <div class="d-inline-block welcome-content dropdown-toggle" style="margin-top: 17px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="white"
                class="bi bi-person-circle" viewBox="0 0 20 16">
                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                <path fill-rule="evenodd"
                    d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
            </svg>
            <span class="fs12 cart-text"><a href="{{ route('customer.session.index') }}" style="color: lightgrey">Sign In</a></span>
            <span class="fs12 cart-text" style="margin-left: 5px; margin-right: 5px; color: lightgrey">/</span>
            <span class="fs12 cart-text"><a href="{{ route('customer.register.index') }}" style="color: lightgrey">Register</a></span>
        </div>
    @endif
    @auth('customer')
        <div class="dropdown-list">
            <div class="dropdown-label">
                {{ auth()->guard('customer')->user()->first_name }}
            </div>

            <div class="dropdown-container">
                <ul type="none">
                    <li>
                        <a href="{{ route('customer.profile.index') }}" class="unset">{{ __('shop::app.header.profile') }}</a>
                    </li>

                    <li>
                        <a href="{{ route('customer.orders.index') }}" class="unset">{{ __('velocity::app.shop.general.orders') }}</a>
                    </li>

                    @php
                        $showCompare = core()->getConfigData('general.content.shop.compare_option') == "1" ? true : false;

                        $showWishlist = core()->getConfigData('general.content.shop.wishlist_option') == "1" ? true : false;
                    @endphp

                    @if ($showWishlist)
                        <li>
                            <a href="{{ route('customer.wishlist.index') }}" class="unset">{{ __('shop::app.header.wishlist') }}</a>
                        </li>
                    @endif

                    @if ($showCompare)
                        <li>
                            <a href="{{ route('velocity.customer.product.compare') }}" class="unset">{{ __('velocity::app.customer.compare.text') }}</a>
                        </li>
                    @endif

                    <li>
                        <form id="customerLogout" action="{{ route('customer.session.destroy') }}" method="POST">
                            @csrf

                            @method('DELETE')
                        </form>

                        <a
                            class="unset"
                            href="{{ route('customer.session.destroy') }}"
                            onclick="event.preventDefault(); document.getElementById('customerLogout').submit();">
                            {{ __('shop::app.header.logout') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    @endauth
</div>

{!! view_render_event('bagisto.shop.layout.header.account-item.after') !!}
