<template>
    <section class="categories-section category-style-1 bg-color top" v-if="lengthCounter(countCategories)>0">
        <div class="container">
            <div class="title justify-content-center">
                <h1>{{ lang.top_categories_of_this_month}}</h1>
            </div>
            <div class="category-content" >
                <VueSlickCarousel v-bind="slick_settings" :rtl="settings.text_direction == 'rtl'">
                    <div class="global-list category-slider text-center" v-if="lengthCounter(countCategories)>0" v-for="(category,index) in top_categories" :key="index">
                        <router-link :to="{name : 'product.by.category',params : {id : category.id, slug : category.slug } }"><img v-lazy="category.top_image" :alt="category.title" class="img-fluid"><span class="text-ellipse-one">{{ category.title }}</span></router-link>
                    </div>
                </VueSlickCarousel>
            </div>
        </div><!-- /.container -->
    </section><!-- /.section -->
    <section class="categories-section category-style-1 bg-color" v-else-if="shimmer">
        <div class="container">
            <div class="category-content" >
                <ul class="global-list grid-8">
                    <li v-for="(cat,i) in 8" :key="i" >
                        <shimmer :height="80"></shimmer>
                    </li>
                </ul>
            </div>
        </div><!-- /.container -->
    </section><!-- /.section -->
</template>

<script>
import shimmer from "../partials/shimmer";
import VueSlickCarousel from 'vue-slick-carousel'

export default {
    name: "top_categories",
    props : ['top_categories'],
    components : {
        shimmer, VueSlickCarousel
    },
    data(){
        return {
            slick_settings : {
                "dots": false,
                "edgeFriction": 0.35,
                "infinite": true,
                "arrows": false,
                "autoplay": false,
                "slidesToShow": 6,
                "slidesToScroll": 6,
                "centerPadding": '50px',
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
        }
    },
    watch : {
        homeResponse()
        {
            this.$store.commit('setShimmer',0);
        }
    },
    computed : {
        countCategories()
        {
            if (this.top_categories && this.top_categories.length > 0)
            {
                return this.top_categories;
            }
            else{
                return [];
            }
        },
    },
}
</script>
