{!! view_render_event('bagisto.shop.products.price.before', ['product' => $product]) !!}

@php
    $show = $product->getTypeInstance()->getPriceHtml();
@endphp
<div class="product-price">
    {!! $show !!}
</div>

{!! view_render_event('bagisto.shop.products.price.after', ['product' => $product]) !!}