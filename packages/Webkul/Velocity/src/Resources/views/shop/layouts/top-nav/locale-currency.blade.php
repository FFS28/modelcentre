@php
$searchQuery = request()->input();

if ($searchQuery && !empty($searchQuery)) {
    $searchQuery = implode(
        '&',
        array_map(
            function ($v, $k) {
                if (is_array($v)) {
                    if (is_array($v)) {
                        $key = array_keys($v)[0];

                        return $k . "[$key]=" . implode('&' . $k . '[]=', $v);
                    } else {
                        return $k . '[]=' . implode('&' . $k . '[]=', $v);
                    }
                } else {
                    return $k . '=' . $v;
                }
            },
            $searchQuery,
            array_keys($searchQuery),
        ),
    );
} else {
    $searchQuery = false;
}
@endphp

{!! view_render_event('bagisto.shop.layout.header.locale.before') !!}
<div class="d-inline-block" style="position: relative; width: 100%; text-align: center; padding-top: 5px">
    <div style="display: inline-block;">
        <h6 style="font-weight: bold; text-align:center; margin: auto;font-family: 'Open Sans';font-style: normal;font-weight: 600;font-size: 12px;line-height: 16px;color: #101D61;">Get FREE UK postage when you spend £50 or more on our website</h6>
    </div>
    <div style="display: inline-block; position: absolute; right: 0;color: #101D61;">
        <div style="display: inline-block; margin: 0 5px">Help</div>
        <div style="display: inline-block; margin: 0 5px">GBP</div>
        <div class="locale-icon">
            <img src="{{ core()->getCurrentLocale()->image_url }}" alt="" width="20" height="20" />
        </div>
    </div>
</div>

{!! view_render_event('bagisto.shop.layout.header.locale.after') !!}
<!--
{!! view_render_event('bagisto.shop.layout.header.currency-item.before') !!}

    @if (core()->getCurrentChannel()->currencies->count() > 1)
        <div class="d-inline-block">
            <div class="dropdown">
            <span class="currency-icon">
                {{ core()->getCurrentCurrency()->symbol }}
            </span>

               <select
                    class="btn btn-link dropdown-toggle control locale-switcher styled-select"
                    onchange="window.location.href = this.value" aria-label="Locale">
                    @foreach (core()->getCurrentChannel()->currencies as $currency)
@if (isset($searchQuery) && $searchQuery)
<option value="?{{ $searchQuery }}&currency={{ $currency->code }}" {{ $currency->code == core()->getCurrentCurrencyCode() ? 'selected' : '' }}>{{ $currency->code }}</option>
@else
<option value="?currency={{ $currency->code }}" {{ $currency->code == core()->getCurrentCurrencyCode() ? 'selected' : '' }}>{{ $currency->code }}</option>
@endif
@endforeach

                </select>

                <div class="select-icon-container">
                    <span class="select-icon rango-arrow-down"></span>
                </div>
            </div>
        </div>
    @endif

{!! view_render_event('bagisto.shop.layout.header.currency-item.after') !!}

-->
