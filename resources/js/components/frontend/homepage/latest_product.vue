<template>
    <section class="products-section bg-white" v-if="lengthCounter(products)>0">
        <div class="container">
            <div class="title justify-content-between">
                <h1> {{ lang.latest_products }}</h1>
                <a :href="getUrl('product')" @click.prevent="routerNavigator('all.products')">{{lang.more_products }}<span
                    class="icon mdi mdi-name mdi-arrow-right"></span></a>
            </div>
            <productCarousel :products="products" :number="12" :grid_class="'grid-6'"></productCarousel>
        </div><!-- /.container -->
    </section><!-- /.section -->
    <section class="products-section bg-white" v-else-if="shimmer">
        <div class="container" >
            <ul class="products grid-6" >
                <li v-for="(product,index) in 6" :key="index">
                    <div class="sg-product" >
                        <a href="javascript:void(0)">
                            <shimmer :height="290"></shimmer>
                        </a>
                    </div><!-- /.sg-product -->
                </li>
            </ul>
        </div><!-- /.container -->
    </section><!-- /.section -->
</template>
<script>
import productCarousel from "../pages/product-carousel";
import shimmer from "../partials/shimmer";

export default {
    name: "latest_products",
    components : {
        productCarousel,shimmer
    },
    props: ['latest_products'],
    watch : {
        homeResponse()
        {
            this.$store.commit('setShimmer',0);
        }
    },
    computed : {
        products()
        {
            if (this.latest_products && this.latest_products.length > 0)
            {
                return this.latest_products;
            }
            else{
                return [];
            }
        },
    },
    methods:{
        navigator()
        {
            this.$router.push({ name : 'product' });
        }
    }
}
</script>
