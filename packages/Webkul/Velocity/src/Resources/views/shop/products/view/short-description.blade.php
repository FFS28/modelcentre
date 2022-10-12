{!! view_render_event('bagisto.shop.products.view.short_description.before', ['product' => $product]) !!}
    @if($product->short_description != null)
        <accordian style="width: 90%;" :title="'{{ __('shop::app.products.short-description') }}'" :active="true">
            <div slot="header">
                <h3 class="no-margin display-inbl">
                    {{ __('velocity::app.products.short-description') }}
                </h3>

                <i class="rango-arrow"></i>
            </div>
            <div slot="body">
                <div class="full-short-description">
                    {!! $product->short_description !!}
                </div>
            </div>
        </accordian>
    @endif

{!! view_render_event('bagisto.shop.products.view.short_description.after', ['product' => $product]) !!}