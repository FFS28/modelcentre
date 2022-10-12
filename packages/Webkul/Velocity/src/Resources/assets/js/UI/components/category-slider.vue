<template>
    <div class="container-fluid">
        <template v-if="categories.length > 0">
            <div class="row" :class="localeDirection">
                <div
                    class="col-md-12 no-padding carousel-category without-recent-viewed col-lg-12">
                    <carousel-component
                        :slides-per-page="slidesPerPage"
                        navigationEnabled="hide"
                        paginationEnabled=""
                        :locale-direction="localeDirection"
                        :slides-count="categories.length"
                        v-if="count != 0">

                        <slide
                            :key="index"
                            :slot="`slide-${index}`"
                            v-for="(category, index) in categories">
                            <category-card
                                :img_url="category.image_url"
                                :name="category.name"
                                :link="rootCategoryUrl + category.url_path">
                            </category-card>
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
            localeDirection: String,
            root: String
        },

        data: function () {
            return {
                list: false,
                isCategory: false,
                productCollections: [],
                slidesPerPage: 4,
                windowWidth: window.innerWidth,
                categories: [],
                rootCategoryUrl: this.root
            }
        },

        mounted: function () {
            this.$nextTick(() => {
                window.addEventListener('resize', this.onResize);
            });
            this.setWindowWidth();
            this.setSlidesPerPage(this.windowWidth);

        },

        created () {
            fetch("/getAllCategories").then(res => res.json()).then(data => {
                this.categories = data[0].children;
                this.rootCategoryUrl = this.rootCategoryUrl + "/";
            }).catch(err => {console.log(err)})
        },

        watch: {
            /* checking the window width */
            windowWidth(newWidth, oldWidth) {
                this.setSlidesPerPage(newWidth);
            }
        },

        methods: {
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
                return '.with-recent-viewed';
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