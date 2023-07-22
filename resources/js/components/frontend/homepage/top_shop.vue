<template>
    <section class="sg-seller-product top-shop" v-if="lengthCounter(countShop)>0">
        <div class="container">
            <div class="title">
                <h1>{{ lang.top_shop }}</h1>
            </div>
            <VueSlickCarousel v-bind="slick_settings" :rtl="settings.text_direction == 'rtl'">
                <li v-for="(shop,i) in sellers">
                    <div class="sg-product">
                        <!-- <span class="mdi mdi-name mdi-heart-outline"></span> -->
                        <div class="product-thumb">
                            <a :href="getUrl('shop/'+shop.slug)" @click.prevent="routerNavigator('shop',shop.slug)"><img v-lazy="shop.image_297x203" alt="Image" class="img-fluid"></a>
                        </div>
                        <div class="favorite-icon" v-if="authUser">
                            <a href="javascript:void(0)" :class="{ 'disable_btn' : disabled }"
                               @click="removeFollowed(shop.id)"
                               v-if="checkFollowed(shop)"
                            ><span
                                class="mdi mdi-name mdi-heart"
                            ></span
                            ></a>
                            <a href="javascript:void(0)"
                               @click="follow(shop.id)" :class="{ 'disable_btn' : disabled }"
                               v-else
                            ><span
                                class="mdi mdi-name mdi-heart-outline"
                            ></span
                            ></a>
                        </div>

                        <div class="seller-product-grid-view">
                            <div class="product-info">
                                <div class="seller-logo">
                                    <img v-lazy="shop.image_82x82" :alt="shop.image_82x82" class="img-fluid">
                                </div>
                                <h3><a :href="getUrl('shop/'+shop.slug)" @click.prevent="routerNavigator('shop',shop.slug)">{{ shop.shop_name }}</a></h3>
                                <div class="sg-rating">
                                    <span class="pe-2 fw-bold">{{ shop.rating_count }}</span>
                                    <star-rating class="pb-1 seller_min_margin" v-model:rating_count="shop.rating_count" :read-only="true" :star-size="12"
                                                 :round-start-rating="false"></star-rating>
                                    <span>({{ shop.reviews_count }} {{ lang.ratings }})</span>
                                </div>
                            </div>
                            <div class="product-info-bottom">
                                <ul class="global-list">
                                    <li>{{ lang.products }}: {{ shop.total_products }}</li>
                                    <li>{{ lang.joined }}: {{ shop.join_date }}</li>
                                </ul>
                                <a :href="getUrl('shop/'+shop.slug)" @click.prevent="routerNavigator('shop',shop.slug)" class="store-btn">{{ lang.visit_store }} <span data-v-e4caeaf8="" class="icon mdi mdi-name mdi-arrow-right"></span>
                                </a>
                            </div>
                        </div>
                    </div><!-- /.sg-product -->
                </li>
            </VueSlickCarousel>
        </div><!-- /.container -->
    </section><!-- /.sg-store-section -->
    <section class="sg-store-section p-0" v-else-if="shimmer">
        <div class="container">
            <VueSlickCarousel v-bind="slick_settings" :rtl="settings.text_direction == 'rtl'">
                <div class="store div_margin" v-for="(seller,index) in 6" :key="index">
                    <shimmer class="pa-3" :height="300"></shimmer>
                </div>
            </VueSlickCarousel>
        </div>
    </section>
</template>

<script>

import shimmer from "../partials/shimmer";
import StarRating from 'vue-star-rating';
import VueSlickCarousel from 'vue-slick-carousel'

export default {
    name: "top_shop",
    components: {shimmer, StarRating, VueSlickCarousel},
    props: ['sellers'],
    data() {
        return {
            navigation_right: 'NEX <span class="mdi mdi-name mdi-arrow-right"></span>',
            navigation_left: '<span class=" mdi mdi-name mdi-arrow-left"></span> PRE',
            checkListing: true,
            slick_settings : {
                "dots": false,
                "edgeFriction": 0.35,
                "infinite": true,
                "arrows": false,
                "autoplay": false,
                "adaptiveHeight": true,
                "slidesToShow": 4,
                "slidesToScroll": 4,
                "responsive": [
                    {
                        "breakpoint": 1199,
                        "settings": {
                            "slidesToShow": 3,
                            "slidesToScroll": 3,
                        }
                    },
                    {
                        "breakpoint": 768,
                        "settings": {
                            "slidesToShow": 2,
                            "slidesToScroll": 2,
                        }
                    },
                    {

                        "breakpoint": 480,
                        "settings": {
                            "slidesToShow": 2,
                            "slidesToScroll": 2
                        }
                    },
                    {

                        "breakpoint": 575,
                        "settings": {
                            "slidesToShow": 2,
                            "slidesToScroll": 2
                        }
                    },
                    {

                        "breakpoint": 320,
                        "settings": {
                            "slidesToShow": 1,
                            "slidesToScroll": 1
                        }
                    }
                ]
            }

        }
    },
    computed: {
        userShop() {
            return this.$store.getters.getShopFollwer
        },
        countShop() {
            if (this.sellers && this.sellers.length > 0) {
                return this.sellers;
            } else {
                return [];
            }
        },
    },
    watch : {
        homeResponse()
        {
            this.$store.commit('setShimmer',0);
        }
    },
    methods: {
    },
}
</script>
