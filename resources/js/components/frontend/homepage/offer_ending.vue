<template>
    <div>
        <section class="products-section category-products bg-white" v-if="lengthCounter(products)>0">
            <div class="container">
                <div v-if="offer_ending_products && offer_ending_products.length > 0" class="title justify-content-between">
                    <h1>{{lang.offer_ending_soon}}</h1>
                    <a href="javascript:void(0)" @click="navigator">{{lang.more_products}} <span class="icon mdi mdi-name mdi-arrow-right"></span></a>
                </div>
                <div class="row">
                    <div class="col-md-4" v-if="offer_ending_banner && $route.name == 'home' && window.width >= 1200">
                        <div class="products">
                            <div class="category-product">
                                <div>
                                    <a v-if="urlCheck(offer_ending_banner_url)" :href="offer_ending_banner_url"><img
                                        v-lazy="offer_ending_banner"  :alt="offer_ending_banner" class="img-fluid"></a>
                                    <router-link v-if="offer_ending_banner_url" v-else :to="offer_ending_banner_url">
                                        <img v-lazy="offer_ending_banner" :alt="offer_ending_banner" class="img-fluid">
                                    </router-link>
                                </div>
                                <div class="thumb" v-if="offer_ending_banner">
                                    <a href="javascript:void(0)"><img
                                       v-lazy="offer_ending_banner" :alt="offer_ending_banner" class="img-fluid"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div :class="[window.width <1200 ? 'col-lg-12' : 'col-lg-8']">
                        <product :products="products" :grid_class="window.width <1200 ? 'grid-4 grid-show' : 'grid-4'"></product>
                    </div>
                </div><!-- /.row -->
            </div><!-- /.container -->
        </section>
    </div>
</template>

<script>
import product from "../pages/product";

export default {
    name: "offer_ending",
    components : {
        product
    },
  data(){
      return {
        window: {
          width: 0,
          height: 0
        }
      }
  },
    props:['offer_ending_products','offer_ending_banner','offer_ending_banner_url'],
    mounted() {
    },
    computed : {
        products()
        {
            if (this.offer_ending_products && this.offer_ending_products.length > 0)
            {
                return this.offer_ending_products;
            }
            else{
                return [];
            }
        },
    },
      created() {
        window.addEventListener('resize', this.handleResize);
        this.handleResize();
      },
      destroyed() {
        window.removeEventListener('resize', this.handleResize);
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
                this.$router.push({ name : 'product.by.offer' });
            }
        },
      handleResize() {
        this.window.width = window.innerWidth;
        this.window.height = window.innerHeight;
      }
    }
}
</script>
