@php
    $count = core()->getConfigData('catalog.products.homepage.no_of_featured_product_homepage');
    $count = $count ? $count : 10;
    $direction = core()->getCurrentLocale()->direction == 'rtl' ? 'rtl' : 'ltr';
@endphp

<category-slider
    locale-direction="{{ $direction }}"
    count="{{ (int) $count }}"
    root="{{env('APP_URL')}}"
>

</category-slider>