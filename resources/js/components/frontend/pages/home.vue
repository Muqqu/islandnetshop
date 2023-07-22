<template>

  <div>
    <slider></slider>
    <services v-if="settings.show_service_info_section == 1 "></services>
    <div v-for="(componentName, index) in supportedComponents" :key="index">

      <component :is="componentName"
                 :popular_categories="componentName == 'popular_categories' && results[index] ? results[index].data : []"
                 :top_categories="componentName == 'top_categories' && results[index] ? results[index].data : []"
                 :today_deals="componentName == 'today_deals' && results[index] ? results[index].data : []"
                 :banners="componentName == 'banner' && results[index] ? results[index].data : []"
                 :flash_products="componentName == 'flash_products' && results[index] ? results[index].data :[] "
                 :gadget_product="componentName == 'gadget_product' && results[index] ? results[index].data :[] "
                 :best_selling_product="componentName == 'best_selling_product' && results[index] ? results[index].data : []"
                 :offer_ending_products="componentName == 'offer_ending' && results[index] ? results[index].data : []"
                 :blogs="componentName == 'blog' && results[index] ? results[index].data : []"
                 :brands="componentName == 'brands' && results[index] ? results[index].data :[] "
                 :sellers="componentName == 'top_shop' && results[index] ? results[index].data : [] "
                 :best_shop="componentName == 'best_shop' && results[index] ? results[index].data : [] "
                 :featured_shop="componentName == 'featured_shop' && results[index] ? results[index].data : [] "
                 :express_shop="componentName == 'express_shop' && results[index] ? results[index].data : [] "
                 :download_section="componentName == 'download_section' && results[index] ? results[index].data : [] "
                 :category_sec_banner="componentName == 'gadget_product' && results[index] ? results[index].banner : null "
                 :category_sec_banner_url="componentName == 'gadget_product' && results[index] ? results[index].banner_url : null"
                 :offer_ending_banner="componentName == 'offer_ending' && results[index]  ? results[index].banner : null "
                 :offer_ending_banner_url="componentName == 'offer_ending' && results[index] ? results[index].banner_url : null "
                 :campaigns="componentName == 'campaign' && results[index] ? results[index].data : []"
                 :latest_products="componentName == 'latest_products' && results[index] ? results[index].data : []"
                 :video_shopping="addons.includes('video_shopping') && componentName == 'video_shopping' && results[index] ? results[index].data : []"
      >
      </component>
    </div>
    <viewed_product v-if="settings.recent_viewed == 1 "></viewed_product>
    <subscribe></subscribe>
    <deal></deal>
  </div>
</template>

<script>

import slider from "../homepage/slider";
import services from "../homepage/services";
import campaign from "../homepage/campaign";
import popular_categories from "../homepage/popular_categories";
import top_categories from "../homepage/top_categories";
import today_deals from "../homepage/today_deals";
import addBanner from "../homepage/add-banner";
import flash_products from "../homepage/flash_products";
import gadget_product from "../homepage/gadget_product";
import best_selling_product from "../homepage/best_selling_product";
import offer_ending from "../homepage/offer_ending";
import blog from "../homepage/blog";
import viewed_product from "../homepage/viewed_product";
import subscribe from "../homepage/subscribe";
import deal from "../homepage/article";
import brand from "../homepage/brand";
import top_shop from "../homepage/top_shop";
import best_shop from "../homepage/best_shop";
import featured_shop from "../homepage/featured-shop";
import express_shop from "../homepage/express-shop";
import download_section from "../homepage/download_section";
import latest_products from "../homepage/latest_product";
import video_shop from "../homepage/video_shop";

export default {
  name: "home.vue",
  data() {
    return {
      scroll_continue: true,
      scroller_key: 0,
      supportedComponents: [],
      results: [],
      video_shops: []
    }
  },
  components: {
    slider,
    services,
    campaign: campaign,
    popular_categories: popular_categories,
    top_categories: top_categories,
    today_deals: today_deals,
    banner: addBanner,
    flash_products: flash_products,
    latest_products: latest_products,
    gadget_product: gadget_product,
    best_selling_product: best_selling_product,
    offer_ending: offer_ending,
    blog: blog,
    viewed_product: viewed_product,
    subscribe: subscribe,
    deal: deal,
    brands: brand,
    top_shop: top_shop,
    best_shop: best_shop,
    express_shop: express_shop,
    featured_shop: featured_shop,
    download_section: download_section,
    video_shopping: video_shop,
  },
  mounted() {
    this.loadComponents();

    if (this.homeResponse) {
      this.$store.commit('setShimmer', 0);
    }

  },
  computed: {
    homeComponents() {
      return this.$store.getters.getHomeComponents;
    },
    homeResults() {
      return this.$store.getters.getHomeResults;
    },
  },
  watch: {
    homeComponents() {
      this.loadComponents();
    }
  },
  created() {
  },
  methods: {
    submit() {
      let url = this.getUrl('home/subscribers');
      axios.post(url, this.form)
          .then((response) => {
            if (response.data.success) {
              $('#pop_up').modal('hide');
              toastr.success(response.data.success, this.lang.Success + ' !!');
              this.form.email = '';
            } else {
              if (response.data.error) {
                toastr.error(response.data.error, this.lang.Error + ' !!');
              }
            }
          }).catch((error) => {
        if (error.response.status == 422) {
          let errors = Object.keys(error.response.data.errors);
          for (let i = 0; i <= errors.length; i++) {
            toastr.error(error.response.data.errors[errors[i]][0], this.lang.Error + ' !!');
          }

        }
      })
    },
    loadComponents() {
      let keys = this.homeComponents;
      if (keys.length > 0) {
        for (let i = 0; i < keys.length; i++) {
          if (keys[i] == "popular_category") {
            this.results.push({key: 'popular_categories', data: {}});
            this.supportedComponents.push('popular_categories');
          }
          if (keys[i] == "top_category") {
            this.results.push({key: 'top_categories', data: {}});
            this.supportedComponents.push('top_categories');
          }
          if (keys[i] == 'todays_deal') {
            this.results.push({key: 'today_deals', data: {}});
            this.supportedComponents.push('today_deals');
          }
          if (keys[i] == "banner") {
            this.results.push({key: "banner", data: {}});
            this.supportedComponents.push('banner');
          }
          if (keys[i] == 'flash_deal') {
            this.results.push({key: "flash_products", data: {}});
            this.supportedComponents.push('flash_products');
          }
          if (keys[i] == 'latest_product') {
            this.results.push({key: "latest_products", data: {}});
            this.supportedComponents.push('latest_products');
          }
          if (keys[i] == 'category_section') {
            this.results.push({key: "gadget_product", data: {}, banner: null, banner_url: null});
            this.supportedComponents.push('gadget_product');
          }
          if (keys[i] == 'best_selling_products') {
            this.results.push({key: "best_selling_product", data: {}});
            this.supportedComponents.push('best_selling_product');
          }
          if (keys[i] == "offer_ending_soon") {
            this.results.push({key: 'offer_ending', data: {}, banner: null, banner_url: null});
            this.supportedComponents.push('offer_ending');
          }

          if (keys[i] == "latest_news") {
            this.results.push({key: 'blog', data: {}});
            this.supportedComponents.push('blog');
          }
          if (keys[i] == "popular_brands") {
            this.results.push({key: 'brands', data: {}});
            this.supportedComponents.push('brands');
          }
          if (keys[i] == "top_sellers") {

            this.results.push({key: 'top_sellers', data: {}});
            this.supportedComponents.push('top_shop');
          }
          if (keys[i] == "best_sellers") {
            this.results.push({key: 'best_sellers', data: {}});
            this.supportedComponents.push('best_shop');
          }
          if (keys[i] == "featured_sellers") {
            this.results.push({key: 'featured_sellers', data: {}});
            this.supportedComponents.push('featured_shop');
          }
          if (keys[i] == "express_sellers") {
            this.results.push({key: 'express_sellers', data: {}});
            this.supportedComponents.push('express_shop');
          }
          if (keys[i] == 'download_section') {
            this.results.push({key: 'download_section', data: {}});
            this.supportedComponents.push('download_section');
          }
          if (keys[i] == "campaign") {
            this.results.push({key: 'campaign', data: {}});
            this.supportedComponents.push('campaign');
          }
          if (keys[i] == "video_shopping") {
            this.results.push({key: 'video_shopping', data: {}});
            this.supportedComponents.push('video_shopping');
          }
        }
        this.scrollData();
      }
    },
    scrollData() {
      let url = this.getUrl('home/page');
      this.scroll_continue = false;


      if (this.homeResults.length == 0) {

        axios.get(url).then((response) => {
          if (response.data.error) {
            toastr.error(response.data.error, this.lang.Error + ' !!');
            this.$Progress.fail();
          } else {
            this.$store.commit('setResponseDone', 1);
            this.componentAppend(response);
          }
        });
      } else {
        this.results = this.homeResults;
      }

    },

    componentAppend(response) {
      let components = Object.keys(response.data);


      for (let i = 0; i < components.length; i++) {
        let component = components[i].split('-');

        if (component[0] == 'banners') {
          this.dataReplace('banner', response.data[components[i]]);
          this.scroll_continue = true;
          this.scroller_key++;
        }
        if (component[0] == 'campaigns') {
          this.dataReplace('campaign', response.data[components[i]]);
          this.scroll_continue = true;
          this.scroller_key++;
        }
        if (component[0] == 'popular_categories') {
          this.dataReplace('popular_categories', response.data[components[i]]);
          this.scroll_continue = true;
          this.scroller_key++;
        }
        if (component[0] == 'best_selling_product') {
          this.dataReplace('best_selling_product', response.data[components[i]]);
          this.scroll_continue = true;
          this.scroller_key++;
        }
        if (component[0] == 'top_categories') {
          this.dataReplace('top_categories', response.data[components[i]]);
          this.scroll_continue = true;
          this.scroller_key++;
        }
        if (component[0] == 'today_deals') {
          this.dataReplace('today_deals', response.data[components[i]]);
          this.scroll_continue = true;
          this.scroller_key++;
        }
        if (component[0] == 'flash_products') {
          this.dataReplace('flash_products', response.data[components[i]]);
          this.scroll_continue = true;
          this.scroller_key++;
        }
        if (component[0] == 'latest_products') {
          this.dataReplace('latest_products', response.data[components[i]]);
          this.scroll_continue = true;
          this.scroller_key++;
        }
        if (component[0] == 'gadget_product') {
          this.dataReplace('gadget_product', response.data);
          this.scroll_continue = true;
          this.scroller_key++;
        }
        if (component[0] == 'offer_ending') {
          this.dataReplace('offer_ending', response.data);
          this.scroll_continue = true;
          this.scroller_key++;
        }
        if (component[0] == 'blog') {
          this.dataReplace('blog', response.data[components[i]]);
          this.scroll_continue = true;
          this.scroller_key++;
        }
        if (component[0] == 'brands') {
          this.dataReplace('brands', response.data[components[i]]);
          this.scroll_continue = true;
          this.scroller_key++;
        }
        if (component[0] == 'sellers') {
          this.dataReplace('sellers', response.data[components[i]]);
          this.scroll_continue = true;
          this.scroller_key++;
        }
        if (component[0] == 'best_sellers') {
          this.dataReplace('best_sellers', response.data[components[i]]);
          this.scroll_continue = true;
          this.scroller_key++;
        }
        if (component[0] == 'featured_sellers') {
          this.dataReplace('featured_sellers', response.data[components[i]]);
          this.scroll_continue = true;
          this.scroller_key++;
        }
        if (component[0] == 'express_sellers') {
          this.dataReplace('express_sellers', response.data[components[i]]);
          this.scroll_continue = true;
          this.scroller_key++;
        }
        if (component[0] == 'download_section') {
          this.dataReplace('download_section', response.data[components[i]]);
          this.scroll_continue = true;
          this.scroller_key++;
        }
        if (component[0] == 'video_shopping') {
          this.dataReplace('video_shopping', response.data[components[i]]);
          this.scroll_continue = true;
          this.scroller_key++;
        }
      }
      this.$store.commit('getHomeResults', this.results);
    },
    dataReplace(key, data) {

      if (key == 'offer_ending') {
        this.results[this.scroller_key] = {
          key: 'offer_ending',
          data: data['offer_ending-' + this.scroller_key],
          banner: data['offer_ending_banner-' + this.scroller_key],
          banner_url: data['offer_ending_banner_url' + this.scroller_key]
        };
      } else if (key == 'gadget_product') {
        this.results[this.scroller_key] = {
          key: 'gadget_product',
          data: data['gadget_product-' + this.scroller_key],
          banner: data['category_sec_banner-' + this.scroller_key],
          banner_url: data['category_sec_banner_url-' + this.scroller_key]
        };
      } else {
        this.results[this.scroller_key].data = data;
      }
    },
  }

}
</script>
