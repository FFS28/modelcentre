@inject ('productViewHelper', 'Webkul\Product\Helpers\View')

{!! view_render_event('bagisto.shop.products.view.attributes.before', ['product' => $product]) !!}
    @php
        $customAttributeValues = $productViewHelper->getAdditionalData($product);
    @endphp

    @if ($customAttributeValues)
        {{-- {{ echo $customAttributeValues; }} --}}

        @foreach ($customAttributeValues as $attribute)
            @if ($attribute['value'] != null)
                <accordian :active="false">
                    <div slot="header">
                        @if ($attribute['label'])
                            <h3 class="no-margin display-inbl">{{ $attribute['label'] }}</h3>
                        @else
                            <h3 class="no-margin display-inbl">{{ $attribute['admin_name'] }}</h3>
                        @endif
                        <i class="rango-arrow"></i>
                    </div>

                    <div slot="body">
                        <p>{{ $attribute['value'] }}</p>
                    </div>
                </accordian>
            @endif
        @endforeach
    @endif

{!! view_render_event('bagisto.shop.products.view.attributes.after', ['product' => $product]) !!}