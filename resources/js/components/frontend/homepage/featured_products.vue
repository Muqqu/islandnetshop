<template>
    <section class="products-section bg-white" v-if="lengthCounter(products)>0">
        <div class="container">
            <div  class="title justify-content-between">
                <h1>{{lang.featured_products}}</h1>
                <a href="javascript:void(0)" @click="navigator">{{lang.more_products}}<span class="icon mdi mdi-name mdi-arrow-right"></span></a>
            </div>
            <productCarousel :products="products" :number="12" :grid_class="'grid-6'"></productCarousel>
        </div><!-- /.container -->
    </section><!-- /.section -->
</template>

<script>
import productCarousel from "../pages/product-carousel";

export default {
    name: "featured_products",
    components : {
        productCarousel
    },
    props: ['featured_products'],

    computed : {
        countProduct()
        {
            return this.lengthCounter(this.featured_products);
        },
        products()
        {
            if (this.featured_products && this.featured_products.length == 0)
            {
                return ['id'];
            }
            else if (this.featured_products && this.featured_products.length > 0)
            {
                return this.featured_products;
            }
            else{
                return [];
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
