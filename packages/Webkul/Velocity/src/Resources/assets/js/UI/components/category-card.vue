<template>
    <div class="card grid-card product-card-new featured-product-card">
        <div class="product-image">
            <a :title="name" :href="link">
                <img
                    style="border-radius: 8px !important; border-bottom-left-radius: 0px !important;    border-bottom-right-radius: 0px !important;"
                    class="featured-product-image lzy_img"
                    :src="img_url ? img_url : `${baseUrl}` + '/vendor/webkul/ui/assets/images/product/meduim-product-placeholder.webp'"
                />
            </a>
        </div>

        <div class="card-body" style ="text-align: center">
                <a
                    class="unset"
                    :title="name"
                    :href="link"
                >
                    <span class="fs16 featured-product-name">{{ name }}</span>
                </a>
        </div>
    </div>

</template>

<script type="text/javascript">
    export default {
        props: [
            'img_url',
            'name',
            'link',
        ],

        data: function () {
            return {
                'addToCart': 0,
                'addToCartHtml': '',
            }
        }
    }
</script>