<template>
    <section class="brand-section" v-if="countBrand > 0">
        <div class="container">
            <div  class="title justify-content-between">
                <h1>{{lang.popular_brand}}</h1>
                <router-link :to="{ name : 'brands' }">{{lang.browse_all_brands}} <span
                    class="icon mdi mdi-name mdi-arrow-right"></span></router-link>
            </div>
            <div class="category-content">
                <VueSlickCarousel v-bind="slick_settings" :rtl="settings.text_direction == 'rtl'">
                    <div v-if="countBrand > 0" v-for="(brand,index) in brands" :key="index">
                        <div class="brand margin_right_18">
                            <div class="brand_image">
                                <a :href="getUrl('brand/'+brand.slug)" @click.prevent="routerNavigator('product.by.brand',brand.slug)">
                                    <img v-lazy="brand.image_130x93"
                                         :alt="brand.title" class="img-fluid">
                                </a>
                            </div>
                            <span class="brand_title">{{ brand.title }}</span>
                        </div>
                    </div>
                </VueSlickCarousel>
            </div>
        </div>
    </section>

    <section class="categories-section" v-else-if="shimmer">
        <div class="container">
            <div class="category-content">
                <VueSlickCarousel v-bind="slick_settings" :rtl="settings.text_direction == 'rtl'">
                    <div v-for="(attr,i) in 6" :key="'static'+i">
                        <a href="javascript:void(0)" class="p-0 border-0"><shimmer></shimmer></a>
                    </div>
                </VueSlickCarousel>
            </div>
        </div>
    </section>
</template>

<script>
import shimmer from "../partials/shimmer";
import VueSlickCarousel from 'vue-slick-carousel'

export default {
    name: "brand",
    components: {shimmer,VueSlickCarousel},
    props: ['brands'],
    data:()=>({
        slick_settings : {
            "dots": false,
            "infinite": true,
            "arrows": false,
            "autoplay": false,
            "slidesToShow": 6,
            "slidesToScroll": 6,
            "responsive": [
                {
                    "breakpoint": 1024,
                    "settings": {
                        "slidesToShow": 5,
                        "slidesToScroll": 5,
                    }
                },
                {
                    "breakpoint": 768,
                    "settings": {
                        "slidesToShow": 3,
                        "slidesToScroll": 3,
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

                    "breakpoint": 320,
                    "settings": {
                        "slidesToShow": 2,
                        "slidesToScroll": 2
                    }
                }
            ]
        }
    }),
    watch : {
        homeResponse()
        {
            this.$store.commit('setShimmer',0);
        }
    },
    computed : {
        countBrand()
        {
            return this.lengthCounter(this.brands);
        }
    }
}
</script>
