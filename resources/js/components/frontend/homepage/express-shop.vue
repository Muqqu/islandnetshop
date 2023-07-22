<template>
    <section class="sg-seller-product" v-if="lengthCounter(countShop) > 0">
        <div class="container">
            <div class="title">
                <h1>{{ lang.brand_shops }}</h1>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="sg-category-content">
                        <ul class="products grid-4">
                            <li v-for="(shop, i) in express_shop">
                                <div class="sg-product">
                                    <div class="product-thumb">
                                        <a
                                            :href="getUrl('shop/' + shop.slug)"
                                            @click.prevent="
                                                routerNavigator(
                                                    'shop',
                                                    shop.slug
                                                )
                                            "
                                        ><img
                                            v-lazy="shop.image_297x203"
                                            alt="Image"
                                            class="img-fluid"
                                        /></a>
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
                                                <img
                                                    v-lazy="shop.image_82x82"
                                                    :alt="shop.image_82x82"
                                                    class="img-fluid"
                                                />
                                            </div>
                                            <h3>
                                                <a
                                                    :href="
                                                        getUrl(
                                                            'shop/' + shop.slug
                                                        )
                                                    "
                                                    @click.prevent="
                                                        routerNavigator(
                                                            'shop',
                                                            shop.slug
                                                        )
                                                    "
                                                >{{ shop.shop_name }}</a
                                                >
                                            </h3>
                                            <div class="sg-rating">
                                                <span class="pe-2 fw-bold">{{
                                                        shop.rating_count
                                                    }}</span>
                                                <star-rating
                                                    class="pb-1 seller_min_margin"
                                                    v-model:rating_count="
                                                        shop.rating_count
                                                    "
                                                    :read-only="true"
                                                    :star-size="12"
                                                    :round-start-rating="false"
                                                ></star-rating>
                                                <span
                                                >({{ shop.reviews_count }}
                                                    {{ lang.ratings }})</span
                                                >
                                            </div>
                                        </div>
                                        <div class="product-info-bottom">
                                            <ul class="global-list">
                                                <li>
                                                    {{ lang.products }}:
                                                    {{ shop.total_products }}
                                                </li>
                                                <li>
                                                    {{ lang.joined }}:
                                                    {{ shop.join_date }}
                                                </li>
                                            </ul>
                                            <a
                                                :href="
                                                    getUrl('shop/' + shop.slug)
                                                "
                                                @click.prevent="
                                                    routerNavigator(
                                                        'shop',
                                                        shop.slug
                                                    )
                                                "
                                                class="store-btn"
                                            >{{ lang.visit_store }}
                                                <span
                                                    data-v-e4caeaf8=""
                                                    class="icon mdi mdi-name mdi-arrow-right"
                                                ></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.sg-product -->
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container -->
    </section>
    <!-- /.sg-brand-shop -->
    <section class="sg-brand-shop pt-0" v-else-if="shimmer">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6" v-for="(shop, i) in 4" :key="i">
                    <div class="brand-list">
                        <div class="brand-shop">
                            <shimmer class="pa-0" :height="300"></shimmer>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import shimmer from "../partials/shimmer";
import seller from "../partials/seller";
import StarRating from "vue-star-rating";

export default {
    name: "express_shop",
    components: {shimmer, seller, StarRating},
    props: ["express_shop"],
    data() {
        return {
            checkListing: true,
            slick_settings: {
                dots: false,
                edgeFriction: 0.35,
                infinite: true,
                arrows: false,
                autoplay: false,
                slidesToShow: 4,
                slidesToScroll: 4,
                responsive: [
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 3,
                            initialSlide: 1,
                        },
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2,
                            initialSlide: 1,
                        },
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2,
                        },
                    },
                    {
                        breakpoint: 320,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                        },
                    },
                ],
            },
        };
    },
    watch: {
        homeResponse() {
            this.$store.commit("setShimmer", 0);
        },
    },
    computed: {
        userShop() {
            return this.$store.getters.getShopFollwer;
        },
        countShop() {
            if (this.express_shop && this.express_shop.length > 0) {
                return this.express_shop;
            } else {
                return [];
            }
        },
    },
    methods: {
    },
};
</script>
