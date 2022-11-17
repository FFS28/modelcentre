@extends('admin::layouts.content')

@section('page_title')
    {{ __('admin::app.catalog.products.edit-title') }}
@stop

@push('css')
    <style>
       @media only screen and (max-width: 728px){
            .content-container .content .page-header .page-title{
                width: 100%;
            }

            .content-container .content .page-header .page-title .control-group {
                margin-top: 20px!important;
                width: 100%!important;
                margin-left: 0!important;
            }

            .content-container .content .page-header .page-action {
                margin-top: 10px!important;
                float: left;
            }
       }
    </style>
@endpush

@section('content')
    <div class="content">
        @php
            $locale = core()->checkRequestedLocaleCodeInRequestedChannel();
            $channel = core()->getRequestedChannelCode();
            $channelLocales = core()->getAllLocalesByRequestedChannel()['locales'];
            $customizableOption = $product->getCustomizableOptions($product->id);
        @endphp

        {!! view_render_event('bagisto.admin.catalog.product.edit.before', ['product' => $product]) !!}

        <form method="POST" action="" @submit.prevent="onSubmit" enctype="multipart/form-data">

            <div class="page-header">

                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link"
                           onclick="window.location = '{{ route('admin.catalog.products.index') }}'"></i>

                        {{ __('admin::app.catalog.products.edit-title') }}
                    </h1>

                    <div class="control-group">
                        <select class="control" id="channel-switcher" name="channel">
                            @foreach (core()->getAllChannels() as $channelModel)

                                <option
                                    value="{{ $channelModel->code }}" {{ ($channelModel->code) == $channel ? 'selected' : '' }}>
                                    {{ core()->getChannelName($channelModel) }}
                                </option>

                            @endforeach
                        </select>
                    </div>

                    <div class="control-group">
                        <select class="control" id="locale-switcher" name="locale">
                            @foreach ($channelLocales as $localeModel)

                                <option
                                    value="{{ $localeModel->code }}" {{ ($localeModel->code) == $locale ? 'selected' : '' }}>
                                    {{ $localeModel->name }}
                                </option>

                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="page-action">
                    <button type="submit" class="btn btn-lg btn-primary">
                        {{ __('admin::app.catalog.products.save-btn-title') }}
                    </button>
                </div>
            </div>

            <div class="page-content">
                @csrf()

                <input name="_method" type="hidden" value="PUT">

                @foreach ($product->attribute_family->attribute_groups as $index => $attributeGroup)
                    <?php $customAttributes = $product->getEditableAttributes($attributeGroup); ?>

                    @if (count($customAttributes))

                        {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.' . $attributeGroup->name . '.before', ['product' => $product]) !!}

                        <accordian title="{{ __($attributeGroup->name) }}"
                                   :active="{{$index == 0 ? 'true' : 'false'}}">
                            <div slot="body">
                                {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.' . $attributeGroup->name . '.controls.before', ['product' => $product]) !!}

                                @foreach ($customAttributes as $attribute)

                                    <?php
                                        if ($attribute->code == 'guest_checkout' && ! core()->getConfigData('catalog.products.guest-checkout.allow-guest-checkout')) {
                                            continue;
                                        }

                                        $validations = [];

                                        if ($attribute->is_required) {
                                            array_push($validations, 'required');
                                        }

                                        if ($attribute->type == 'price') {
                                            array_push($validations, 'decimal');
                                        }

                                        if ($attribute->type == 'file') {
                                            $retVal = (core()->getConfigData('catalog.products.attribute.file_attribute_upload_size')) ? core()->getConfigData('catalog.products.attribute.file_attribute_upload_size') : '2048' ;
                                            array_push($validations, 'size:' . $retVal);
                                        }

                                        if ($attribute->type == 'image') {
                                            $retVal = (core()->getConfigData('catalog.products.attribute.image_attribute_upload_size')) ? core()->getConfigData('catalog.products.attribute.image_attribute_upload_size') : '2048' ;
                                            array_push($validations, 'size:' . $retVal . '|mimes:bmp,jpeg,jpg,png,webp');
                                        }

                                        array_push($validations, $attribute->validation);

                                        $validations = implode('|', array_filter($validations));
                                    ?>

                                    @if (view()->exists($typeView = 'admin::catalog.products.field-types.' . $attribute->type))

                                        <div class="control-group {{ $attribute->type }} {{ $attribute->enable_wysiwyg ? 'have-wysiwyg' : '' }}"
                                             @if ($attribute->type == 'multiselect') :class="[errors.has('{{ $attribute->code }}[]') ? 'has-error' : '']"
                                             @else :class="[errors.has('{{ $attribute->code }}') ? 'has-error' : '']" @endif>

                                            <label
                                                for="{{ $attribute->code }}" {{ $attribute->is_required ? 'class=required' : '' }}>
                                                {{ $attribute->admin_name }}

                                                @if ($attribute->type == 'price')
                                                    <span class="currency-code">({{ core()->currencySymbol(core()->getBaseCurrencyCode()) }})</span>
                                                @endif

                                                <?php
                                                $channel_locale = [];

                                                if ($attribute->value_per_channel) {
                                                    array_push($channel_locale, $channel);
                                                }

                                                if ($attribute->value_per_locale) {
                                                    array_push($channel_locale, $locale);
                                                }
                                                ?>

                                                @if (count($channel_locale))
                                                    <span class="locale">[{{ implode(' - ', $channel_locale) }}]</span>
                                                @endif
                                            </label>

                                            @include ($typeView)

                                            <span class="control-error"
                                                  @if ($attribute->type == 'multiselect') v-if="errors.has('{{ $attribute->code }}[]')"
                                                  @else  v-if="errors.has('{{ $attribute->code }}')"  @endif>
                                                @if ($attribute->type == 'multiselect')
                                                    @{{ errors.first('{!! $attribute->code !!}[]') }}
                                                @else
                                                    @{{ errors.first('{!! $attribute->code !!}') }}
                                                @endif
                                            </span>
                                        </div>

                                    @endif

                                @endforeach
                                @if ($attributeGroup->name == 'Price')

                                    @include ('admin::catalog.products.accordians.customer-group-price')

                                @endif

                                {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.' . $attributeGroup->name . '.controls.after', ['product' => $product]) !!}
                            </div>
                        </accordian>

                        {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.' . $attributeGroup->name . '.after', ['product' => $product]) !!}

                    @endif

                @endforeach
                <accordian title="{{ __('This is Customizable option') }}"
                    :active="{{$index == 0 ? 'true' : 'false'}}">
                        <div slot="body">
                            <div class="control-group" style="display: flex; gap: 20px;">
                                <label class="btn btn-lg btn-primary" style="display: table; width: auto;"  onclick="create_option_panel()">
                                    Create New Customizable option
                                </label>
                                <label class="btn btn-lg btn-primary" style="display: table; width: auto;"  onclick="create_sub_option_panel()">
                                    Create New Sub Customizable option
                                </label>
                            </div>
                            <div class="create-option-panel" id="create-option-panel">
                                <div class="control-group">
                                    <label>Option Title</label>
                                    <input type="text" class="control" aria-required="false" id="option_title">
                                </div>
                                <div class="control-group select ">
                                    <label for="option_type">Option Type</label>
                                    <select id="option_type" class="control" aria-required="false" onchange="showPricePanel()">
                                        <option value="radio">
                                            radio
                                        </option>
                                        <option value="field">
                                            field
                                        </option>
                                        <option value="area">
                                            area
                                        </option>
                                    </select>
                                </div>
                                <div class="control-group" id="price_panel" style="display: none">
                                    <label>price</label>
                                    <input type="number" class="control" aria-required="false" min="0" defaultValue="0" id="option_price">
                                </div>
                                <div class="control-group" style="display: flex; gap: 10px" >
                                    <label class="btn btn-lg btn-primary" style="display: table; width: auto;"  onclick="save_options()">
                                        save
                                    </label>
                                    <label class="btn btn-lg btn-primary" style="display: table; width: auto;"  onclick="hide_option_panel()">
                                        cancel
                                    </label>
                                </div>
                            </div>
                            <div class="create-sub-option-panel" id="create-sub-option-panel" style="display: none">
                                <div class="control-group select ">
                                    <label for="option_selector">Option Select</label>
                                    <select id="option_selector" class="control" aria-required="false">
                                        @foreach ($customizableOption as $option)
                                            <option value="{{$option['id']}}">
                                                {{$option['option']}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="control-group">
                                    <label>Sub Option Title</label>
                                    <input type="text" class="control" aria-required="false" id="sub_option_title">
                                </div>
                                <div class="control-group">
                                    <label>Sub Option Price</label>
                                    <input type="number" class="control" aria-required="false" min="0" defaultValue="0" id="sub_option_price">
                                </div>
                                <div class="control-group" style="display: flex; gap: 10px" >
                                    <label class="btn btn-lg btn-primary" style="display: table; width: auto;"  onclick="save_sub_options()">
                                        save
                                    </label>
                                    <label class="btn btn-lg btn-primary" style="display: table; width: auto;"  onclick="hide_sub_option_panel()">
                                        cancel
                                    </label>
                                </div>
                            </div>
                            <div id="option_content">
                            @foreach ($customizableOption as $option)
                                <div class="table" style="margin-bottom: 20px; margin-top: 20px;"  id="option_content_{{$option['id']}}">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th width="60%">{{$option['option']}}</th>
                                                <th width="15%">{{$option['type']}}</th>
                                                <th width="15%">{{$option['price']}}</th>
                                                <th width="10%">
                                                    <label class="btn btn-lg btn-primary" style="width: auto; padding: 5px;"  onclick="delete_option({{$option['id']}})">delete</label>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="4">
                                                    <table>
                                                        <tbody>
                                                            @foreach ($option['suboptions'] as $suboption)
                                                                <tr id="suboption_{{$suboption->id}}">
                                                                    <td width="60%">{{$suboption->type_title}}</td>
                                                                    <td width="15%"></td>
                                                                    <td width="15%">{{$suboption->type_price}}</td>
                                                                    <td width="10%">
                                                                        <label class="btn btn-lg btn-primary" style="width: auto; padding: 5px;"  onclick="delete_sub_option('{{$suboption->id}}')">delete</label>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>

                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                            </div>
                        </div>
                </accordian>

                {!! view_render_event(
                  'bagisto.admin.catalog.product.edit_form_accordian.additional_views.before',
                   ['product' => $product])
                !!}
                @foreach ($product->getTypeInstance()->getAdditionalViews() as $view)

                    @include ($view)

                @endforeach

                {!! view_render_event(
                  'bagisto.admin.catalog.product.edit_form_accordian.additional_views.after',
                   ['product' => $product])
                !!}
            </div>

        </form>

        {!! view_render_event('bagisto.admin.catalog.product.edit.after', ['product' => $product]) !!}
    </div>
@stop

@push('scripts')
    @include('admin::layouts.tinymce')

    <script>
        $(document).ready(function () {
            $('#channel-switcher, #locale-switcher').on('change', function (e) {
                $('#channel-switcher').val()

                if (event.target.id == 'channel-switcher') {
                    let locale = "{{ app('Webkul\Core\Repositories\ChannelRepository')->findOneByField('code', $channel)->locales->first()->code }}";

                    $('#locale-switcher').val(locale);
                }

                var query = '?channel=' + $('#channel-switcher').val() + '&locale=' + $('#locale-switcher').val();

                window.location.href = "{{ route('admin.catalog.products.edit', $product->id)  }}" + query;
            });

            tinyMCEHelper.initTinyMCE({
                selector: 'textarea.enable-wysiwyg, textarea.enable-wysiwyg',
                height: 200,
                width: "100%",
                plugins: 'image imagetools media wordcount save fullscreen code table lists link hr',
                toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor link hr | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent  | removeformat | code | table',
                image_advtab: true,
            });
        });

        function create_option_panel() {
            initialize();
            $("#create-option-panel").show();
            $("#create-sub-option-panel").hide();
        }

        function create_sub_option_panel() {
            initialize();
            $("#create-sub-option-panel").show();
            $("#create-option-panel").hide();
        }

        function hide_option_panel() {
            $("#create-option-panel").hide()
        }

        function hide_sub_option_panel() {
            $("#create-sub-option-panel").hide()
        }

        function showPricePanel() {
            $("#option_price").val(0);
            if($("#option_type").val() == "radio") {
                $("#price_panel").hide();
            }else {
                $("#price_panel").show();
            }
        }

        function initialize() {
            $("#option_title").val("");
            $("#option_type").val("");
            $("#option_price").val(0);
            $("#sub_option_title").val("");
            $("#sub_option_price").val(0);
        }

        function save_options() {
            let product_id = "{{$product->id}}";
            let title = $("#option_title").val();
            let type = $("#option_type").val();
            let price = type == "radio" ? null : $("#option_price").val();

            $.ajax({
                type: "post",
                url: "{{ route('admin.catalog.products.customcreate') }}",
                data: {
                    _token: "{{csrf_token()}}",
                    product_id: product_id,
                    title: title,
                    type: type,
                    price: price
                },
                success: function(data) {
                    $("#option_selector").append(`<option value="${data}">${title}</option>`);
                    $("#option_content").append(`<div class="table" style="margin-bottom: 20px; margin-top: 20px;"  id="option_content_${data}">
                        <table>
                            <thead>
                                <tr>
                                    <th width="60%">${title}</th>
                                    <th width="15%">${type}</th>
                                    <th width="15%">${price == null ? "" : price}</th>
                                    <th width="10%">
                                        <label class="btn btn-lg btn-primary" style="width: auto; padding: 5px;"  onclick="delete_option(${data})">delete</label>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4">
                                        <table>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>`)
                }
            })
        }

        function save_sub_options() {
            let option_id = $("#option_selector").val();
            let title = $("#sub_option_title").val();
            let price = $("#sub_option_price").val();

            $.ajax({
                type: "post",
                url: "{{ route('admin.catalog.products.subcustomcreate') }}",
                data: {
                    _token: "{{csrf_token()}}",
                    option_id: option_id,
                    title: title,
                    price: price
                },
                success: function(data) {
                    $("#option_content_" + option_id + " tbody tbody").append(`<tr id="suboption_${data}">
                        <td width="60%">${title}</td>
                        <td width="15%"></td>
                        <td width="15%">${price}</td>
                        <td width="10%">
                            <label class="btn btn-lg btn-primary" style="width: auto; padding: 5px;"  onclick="delete_sub_option(${data})">delete</label>
                        </td>
                    </tr>`)
                }
            })
        }

        function delete_sub_option(suboption_id) {
            $.ajax({
                type: "POST",
                url: "{{ route('admin.catalog.products.subcustomdelete') }}",
                data: {
                    _token: "{{csrf_token()}}",
                    suboption_id: suboption_id
                },
                success: function(data) {
                    $("#suboption_"+suboption_id).remove();
                }
            })
        }

        function delete_option(option_id) {
            $.ajax({
                type: "post",
                url: "{{ route('admin.catalog.products.customdelete') }}",
                data: {
                    _token: "{{csrf_token()}}",
                    option_id: option_id
                },
                success: function(data) {
                    $("#option_selector option[value=" + option_id + "]").remove();
                    $("#option_content_"+option_id).remove();
                }
            })
        }
    </script>
@endpush
