<template>
    <section class="products-section bg-white" v-if="lengthCounter(productsList)>0">
        <div class="container" v-if="lengthCounter(products)">
            <div class="title justify-content-between">
                <h1> {{ lang.best_selling_product }}</h1>
                <a :href="getUrl('best-selling/products')" @click.prevent="navigator">{{lang.more_products}} <span class="icon mdi mdi-name mdi-arrow-right"></span></a>
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
        </div>
    </section>
</template>
<script>
import productCarousel from "../pages/product-carousel";
import shimmer from "../partials/shimmer";


export default {
    name: "best_selling_product",
    components : {
        productCarousel,shimmer
    },
    props: ['best_selling_product'],
    watch : {
        homeResponse()
        {
            this.$store.commit('setShimmer',0);
        }
    },
    computed : {
        products()
        {
            if (this.best_selling_product && this.best_selling_product.length > 0)
            {
                return this.best_selling_product;
            }
            else{
                return [];
            }
        },
        productsList()
        {
            if (this.best_selling_product && this.best_selling_product.length > 0)
            {
                return this.best_selling_product;
            }
            else{
                return ['id'];
            }
        },
    },
    methods:{
        navigator()
        {
            if (this.$route.name == 'shop')
            {
                window.scroll(0, 500);
                this.$store.commit('setActiveTab','product');
            }
            else{
                this.$router.push({ name : 'product.by.selling' });
            }
        }
    }
}
</script>
