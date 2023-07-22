<template>
    <section class="products-section bg-white" v-if="lengthCounter(products)>0">
        <div class="container">
            <div v-if="lengthCounter(products)>0" class="title justify-content-between">
                <h1>{{ lang.best_related_products}}</h1>
                <a href="javascript:void(0)" @click="navigator">{{ lang.more_products}} <span
                    class="icon mdi mdi-name mdi-arrow-right"></span></a>
            </div>
            <productCarousel :products="products" :number="12" :grid_class="'grid-6'"></productCarousel>
        </div><!-- /.container -->
    </section><!-- /.section -->
</template>

<script>
import productCarousel from "../pages/product-carousel";

export default {
    name: "best_rated",
    components: {
        productCarousel
    },
    props: ['best_rated'],

    computed: {

        products() {
            if (this.best_rated && this.best_rated.length > 0) {
                return this.best_rated;
            } else {
                return [];
            }
        },
    },
    methods: {
        navigator() {
            window.scroll(0, 500);
            this.$store.commit('setActiveTab', 'product');

        }
    }

}
</script>
