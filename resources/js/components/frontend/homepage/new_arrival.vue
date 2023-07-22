<template>
    <section class="products-section" v-if="new_arrival.length > 0">
        <div class="container">
            <div class="title justify-content-between">
                <h1>{{lang.new_arrival}}</h1>
                <a href="javascript:void(0)" @click="navigator">{{lang.more_products}} <span
                    class="icon mdi mdi-name mdi-arrow-right"></span></a>
            </div>
            <productCarouselList :products="products" :grid_class="'grid-2'" :type="'flash'"></productCarouselList>
        </div><!-- /.container -->
    </section><!-- /.products-section -->
</template>

<script>
import productCarouselList from "../pages/product-carousel-list";

export default {
    name: "new_arrival",
    components: {
        productCarouselList
    },
    props: ['new_arrival'],

    computed: {
        products() {
            if (this.new_arrival && this.new_arrival.length == 0) {
                return ['id'];
            } else if (this.new_arrival && this.new_arrival.length > 0) {
                return this.new_arrival;
            } else {
                return [];
            }
        }
    },
    methods: {
        navigator() {
            window.scroll(0, 500);
            this.$store.commit('setActiveTab', 'product');

        }
    }
}
</script>
