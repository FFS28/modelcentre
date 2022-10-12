{!! view_render_event('bagisto.shop.products.view.stock.before', ['product' => $product]) !!}

<div class="col-6 availability">
    {{-- @if ( $product->haveSufficientQuantity(1) === true )
        <span>IN STOCK {{ $product->totalQuantity() }}1 AVAILABLE</span>
    @endif
    <label
        class="{{! $product->haveSufficientQuantity(1) ? '' : 'active' }} disable-box-shadow">
            @if ( $product->haveSufficientQuantity(1) === true )
                {{ __('shop::app.products.in-stock') }}
            @elseif ( $product->haveSufficientQuantity(1) > 0 )
                {{ __('shop::app.products.available-for-order') }}
            @else
                {{ __('shop::app.products.out-of-stock') }}
            @endif
    </label>
    <br><br> --}}
    <span>SKU#: {{ $product->sku }}</span>
</div>

{!! view_render_event('bagisto.shop.products.view.stock.after', ['product' => $product]) !!}