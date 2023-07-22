<template>
    <section class="products-section category-products bg-white ajax_scroll" v-if="lengthCounter(products)>0 && gadget_product">
        <div class="container">
            <div class="title justify-content-between">
                <h1>{{ category }}</h1>
                <router-link :to="{ name : 'product.by.gadget',params:{slug:gadget_product.slug} }">{{lang.more_products}} <span class="icon mdi mdi-name mdi-arrow-right"></span></router-link>
            </div>
            <div class="row">
                <div class="col-md-4" v-if="category_sec_banner_url && window.width >= 1200">
                    <div class="products">
                        <div class="category-product">
                            <div class="thumb" v-if="category_sec_banner_url">
                                <a v-if="urlCheck(category_sec_banner_url)" :href="category_sec_banner_url"><img v-lazy="category_sec_banner" :alt="category_sec_banner" class="img-fluid"></a>
                                <router-link v-else :to="category_sec_banner_url"><img v-lazy="category_sec_banner" :alt="category_sec_banner_url" class="img-fluid"></router-link>
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
</template>

<script>
import product from "../pages/product";

export default {
    name: "gadget_product",
    data(){
        return{
          window: {
            width: 0,
            height: 0
          }
        }
    },
    components : {
        product
    },

    props : ['gadget_product','category_sec_banner','category_sec_banner_url'],
    computed : {
        category()
        {
            if(this.gadget_product)
               return this.gadget_product.name;
        },
        products()
        {
            if (this.gadget_product && this.gadget_product.products && this.gadget_product.products.length > 0)
            {
                return this.gadget_product.products;
            }
            else{
                return [];
            }
        }
    },
    created() {
      window.addEventListener('resize', this.handleResize);
      this.handleResize();
    },
    destroyed() {
      window.removeEventListener('resize', this.handleResize);
    },
  methods:{
    handleResize() {
      this.window.width = window.innerWidth;
      this.window.height = window.innerHeight;
    }
  }
}
</script>
