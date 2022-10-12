<template>
    <div class="container-fluid">
        <shimmer-component v-if="isLoading"></shimmer-component>

        <template v-else-if="productCollections.length > 0">
            <card-list-header
                :heading="isCategory ? categoryDetails.name : productTitle"
                :view-all="isCategory ? `${this.baseUrl}/${categoryDetails.url_path}` : ''">
            </card-list-header>

            <div class="row" :class="localeDirection">
                <div
                    class="col-md-12 no-padding carousel-products"
                    :class="showRecentlyViewed === 'true' ? 'with-recent-viewed col-lg-9' : 'without-recent-viewed col-lg-12'">
                    <carousel-component
                        :slides-per-page="slidesPerPage"
                        navigationEnabled="hide"
                        paginationEnabled=""
                        :id="isCategory ? `${categoryDetails.name}-carousel` : productId"
                        :locale-direction="localeDirection"
                        :slides-count="productCollections.length"
                        v-if="count != 0">

                        <slide
                            :key="index"
                            :slot="`slide-${index}`"
                            v-for="(product, index) in productCollections">
                            <product-card
                                :product-type="productType"
                                :list="list"
                                :product="product">
                            </product-card>
                        </slide>
                    </carousel-component>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        props: {
            count: {
                type: String,
                default: '10'
            },
            productId: {
                type: String,
                default: ''
            },
            productType: {
                type: String,
                default: '',
            },
            productTitle: String,
            productRoute: String,
            localeDirection: String,
            showRecentlyViewed: {
                type: String,
                default: 'false'
            },
            recentlyViewedTitle: String,
            noDataText: String,
        },

        data: function () {
            return {
                list: false,
                isLoading: true,
                isCategory: false,
                productCollections: [],
                slidesPerPage: 4,
                windowWidth: window.innerWidth,
            }
        },

        mounted: function () {
            this.$nextTick(() => {
                window.addEventListener('resize', this.onResize);
            });

            this.getProducts();
            this.setWindowWidth();
            this.setSlidesPerPage(this.windowWidth);
        },

        watch: {
            /* checking the window width */
            windowWidth(newWidth, oldWidth) {
                this.setSlidesPerPage(newWidth);
            }
        },

        methods: {
            /* fetch product collections */
            getProducts: function () {
                this.$http.get(this.productRoute)
                .then(response => {
                    let count = this.count;

                    if (response.data.status && count != 0) {
                        if (response.data.categoryProducts !== undefined) {
                            this.isCategory = true;
                            this.categoryDetails = response.data.categoryDetails;
                            this.productCollections = response.data.categoryProducts;
                        } else {
                            this.productCollections = response.data.products;
                        }
                    } else {
                        this.productCollections = 0;
                    }

                    this.isLoading = false;
                })
                .catch(error => {
                    this.isLoading = false;
                    console.log(this.__('error.something_went_wrong'));
                })
            },

            /* waiting for element */
            waitForElement: function (selector, callback) {
                if (jQuery(selector).length) {
                    callback();
                } else {
                    setTimeout(() => {
                        this.waitForElement(selector, callback);
                    }, 100);
                }
            },

            /* setting window width */
            setWindowWidth: function () {
                let windowClass = this.getWindowClass();

                this.waitForElement(windowClass, () => {
                    this.windowWidth = $(windowClass).width();
                });
            },

            /* get window class */
            getWindowClass: function () {
                return this.showRecentlyViewed === 'true'
                    ? '.with-recent-viewed'
                    : '.without-recent-viewed';
            },

            /* on resize set window width */
            onResize: function () {
                this.windowWidth = $(this.getWindowClass()).width();
            },

            /* setting slides on the basis of window width */
            setSlidesPerPage: function (width) {
                if (width >= 768) {
                    this.slidesPerPage = 4;
                } else {
                    this.slidesPerPage = 1;
                }
            }
        },

        /* removing event */
        beforeDestroy: function () {
            window.removeEventListener('resize', this.onResize);
        },
    }
</script>