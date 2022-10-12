@php
    $count = 10;
    $direction = core()->getCurrentLocale()->direction == 'rtl' ? 'rtl' : 'ltr';
@endphp
<category-slider
    localeDirection="{{ $direction }}"
    count="{{ (int) $count }}"
    root='{{env("APP_URL")}}'
>
</category-slider>