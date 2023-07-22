"use strict";(self.webpackChunk=self.webpackChunk||[]).push([[989],{3989:(t,e,s)=>{s.r(e),s.d(e,{default:()=>n});var a=s(3424),r=s(7982);const i={name:"gift-voucher",data:function(){return{current:"gift_voucher",testingCode:"",is_shimmer:!1}},components:{user_sidebar:a.Z,shimmer:r.Z},mounted:function(){1!=this.settings.coupon_system&&this.$router.push({name:"home"}),0==this.lengthCounter(this.coupons.data)&&1==this.settings.coupon_system&&this.userCoupon(),this.lengthCounter(this.coupons.data)>0&&(this.is_shimmer=!0)},computed:{coupons:function(){return this.$store.getters.getUserCoupons},shimmer:function(){return this.$store.state.module.shimmer}},methods:{copyTestingCode:function(t){var e=document.getElementById("testing-code_"+t);e&&(e.setAttribute("type","text"),e.select(),e.setSelectionRange(0,99999),document.execCommand("copy"),alert("Copied the text: "+e.value),e.setAttribute("type","hidden"),window.getSelection().removeAllRanges())},userCoupon:function(){var t=this,e=this.getUrl("user/coupons/");axios.get(e).then((function(e){t.is_shimmer=!0,t.$store.commit("getUserCoupons",e.data.coupons)}))}}};const n=(0,s(1900).Z)(i,(function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("div",{staticClass:"sg-page-content"},[s("section",{staticClass:"sg-global-content"},[s("div",{staticClass:"container"},[s("div",{staticClass:"row"},[s("user_sidebar",{attrs:{current:t.current}}),t._v(" "),s("div",{staticClass:"col-lg-9"},[s("div",{staticClass:"coupon-content"},[s("div",{staticClass:"title justify-content-between"},[t.coupons.data?s("h1",[t._v(t._s(t.lang.coupons_code))]):t.shimmer?s("h1",[t._v(t._s(t.lang.loading))]):s("h1",[t._v(t._s(t.lang.no_founds_coupons_code))])]),t._v(" "),t.is_shimmer?s("div",{staticClass:"row"},t._l(t.coupons.data,(function(e,a){return s("div",{key:a,staticClass:"col-md-6"},[s("div",{staticClass:"coupon-text-thumb"},[s("div",{staticClass:"coupon-text"},[s("h3",[s("a",{attrs:{href:"javascript:void(0)"}},[t._v(t._s("flat"==e.discount_type?t.priceFormat(e.discount):e.discount+"% OFF"))])]),t._v(" "),s("p",[t._v(t._s(e.title))]),t._v(" "),s("a",{staticClass:"btn",attrs:{href:"javaScript:void(0)"},on:{click:function(s){return t.copyTestingCode(e.code)}}},[t._v(t._s(t.lang.get_code))]),t._v(" "),s("div",{staticClass:"form-group"},[s("input",{staticClass:"form-control",attrs:{type:"hidden",id:"testing-code_"+e.code},domProps:{value:e.code}})])]),t._v(" "),s("div",{staticClass:"coupon-thumb"},[s("img",{directives:[{name:"lazy",rawName:"v-lazy",value:e.image_145x110,expression:"coupon.image_145x110"}],staticClass:"img-fluid",attrs:{alt:"Image"}})])])])})),0):t.shimmer?s("div",{staticClass:"row"},t._l(4,(function(t,e){return s("div",{key:e,staticClass:"col-md-6 pb-2"},[s("a",{attrs:{href:"javascript:void(0)"}},[s("shimmer",{attrs:{height:160}})],1)])})),0):t._e()])])],1),t._v(" "),s("div",{staticClass:"row"})])])])}),[],!1,null,null,null).exports},7982:(t,e,s)=>{s.d(e,{Z:()=>r});const a={name:"shimmer.vue",props:["height"],data:function(){return{style:{height:this.height+"px"}}}};const r=(0,s(1900).Z)(a,(function(){var t=this,e=t.$createElement;return(t._self._c||e)("img",{staticClass:"shimmer",style:[t.height?t.style:null],attrs:{src:t.defaultAssets.shimmer,alt:"shimmer"}})}),[],!1,null,null,null).exports},3424:(t,e,s)=>{s.d(e,{Z:()=>r});const a={name:"user_sidebar",props:["current","addresses"],data:function(){return{loading:!1,download_url:!1}},mounted:function(){this.checkAuth()},computed:{totalReward:function(){return this.$store.getters.getTotalReward},modalType:function(){return this.$store.getters.getModalType}},components:{shimmer:s(7982).Z},methods:{checkAuth:function(){var t=this,e=this.getUrl("home/check-auth");axios.get(e).then((function(e){t.$store.dispatch("user",e.data.user),t.$store.commit("getOrderUrl",e.data.order_urls),t.authUser?"admin"==t.authUser.user_type&&t.$router.push({name:"home"}):t.$router.push({name:"login"}),e.data.reward&&t.$store.commit("setTotalReward",e.data.reward),e.data.download_urls&&(t.download_url=!0)}))},convertReward:function(){var t=this,e=this.getUrl("user/convert-reward"),s={amount:this.converted_reward/this.settings.reward_convert_rate,reward:this.converted_reward};s.amount>0&&this.totalReward.rewards>=this.converted_reward&&confirm("Are You Sure! You want to Convert ?")&&(this.loading=!0,axios.post(e,s).then((function(e){t.loading=!1,e.data.error?toastr.error(e.data.error,t.lang.Error+" !!"):(toastr.success(e.data.success,t.lang.Success+"!!"),$("#convert_reward").modal("hide"),t.converted_reward="",t.$store.dispatch("user",e.data.user),t.$store.commit("setTotalReward",e.data.reward))})).catch((function(e){t.loading=!1})))}}};const r=(0,s(1900).Z)(a,(function(){var t=this,e=t.$createElement,s=t._self._c||e;return t.authUser?s("div",{staticClass:"col-lg-3"},[s("div",{staticClass:"profile-details position-relative"},[s("div",{staticClass:"profile-thumb"},[s("img",{staticClass:"img-fluid",attrs:{src:t.authUser.profile_image,alt:t.authUser.full_name}})]),t._v(" "),s("h2",[t._v(t._s(t.authUser.full_name)+" "),s("router-link",{staticClass:"d-inline",attrs:{to:{name:"edit.profile"}}},[s("span",{staticClass:"mdi mdi-name mdi-pencil"})])],1),t._v(" "),s("a",{attrs:{href:"javascript:void(0)"}},[t._v(t._s(t.authUser.email))]),t._v(" "),1==t.settings.seller_system?s("router-link",{staticClass:"be_seller base",attrs:{to:{name:"migrate.seller"}}},[t._v("\n            "+t._s(t.lang.be_a_seller)+" "),s("span",{staticClass:"mdi mdi-name mdi-store-outline"})]):t._e()],1),t._v(" "),s("div",{staticClass:"sidebar-menu"},[s("ul",{staticClass:"global-list"},[s("li",{class:{active:"dashboard"===t.current}},[s("router-link",{attrs:{to:{name:"dashboard"}}},[s("span",{staticClass:"mdi mdi-name mdi-view-dashboard-outline"}),t._v(" "+t._s(t.lang.dashboard)+"\n                ")])],1),t._v(" "),s("li",{class:{active:"addresses"===t.current}},[s("router-link",{attrs:{to:{name:"addresses"}}},[s("span",{staticClass:"mdi mdi-name mdi-map-marker-outline"}),t._v("\n                    "+t._s(t.lang.addresses)+"\n                ")])],1),t._v(" "),s("li",{class:{active:"notification"===t.current}},[s("router-link",{attrs:{to:{name:"notification"}}},[s("span",{staticClass:"mdi mdi-name mdi-bell-outline"}),t._v("\n                    "+t._s(t.lang.notification)+"\n                ")])],1),t._v(" "),s("li",{class:{active:"order_history"===t.current}},[s("router-link",{attrs:{to:{name:"order.history"}}},[s("span",{staticClass:"mdi mdi-name mdi-cart-outline"}),t._v("\n                    "+t._s(t.lang.order_history)+"\n                ")])],1),t._v(" "),t.download_url?s("li",{class:{active:"digital_product_order_history"===t.current}},[s("router-link",{attrs:{to:{name:"orders.digital.product"}}},[s("span",{staticClass:"mdi mdi-name mdi-cart-arrow-down"}),t._v(" "+t._s(t.lang.digital_product_order)+"\n                ")])],1):t._e(),t._v(" "),1==t.settings.coupon_system?s("li",{class:{active:"gift_voucher"===t.current}},[s("router-link",{attrs:{to:{name:"gift.voucher"}}},[s("span",{staticClass:"mdi mdi-name mdi-wallet-giftcard"}),t._v("\n                    "+t._s(t.lang.gift_voucher)+"\n                ")])],1):t._e(),t._v(" "),s("li",{class:{active:"change_password"===t.current}},[s("router-link",{attrs:{to:{name:"change.password"}}},[s("span",{staticClass:"mdi mdi-name mdi-lock-outline"}),t._v("\n                    "+t._s(t.lang.change_password)+"\n                ")])],1),t._v(" "),1==t.settings.wallet_system?s("li",{class:{active:"wallet_history"===t.current}},[s("router-link",{attrs:{to:{name:"wallet.history"}}},[s("span",{staticClass:"mdi mdi-wallet-outline"}),t._v("\n                    "+t._s(t.lang.my_wallet)+"\n                ")])],1):t._e(),t._v(" "),t.addons.includes("reward")?s("li",{class:{active:"reward_history"===t.current}},[s("router-link",{attrs:{to:{name:"reward.history"}}},[s("span",{staticClass:"mdi mdi-vector-point"}),t._v(t._s(t.lang.my_rewards)+"\n                ")])],1):t._e(),t._v(" "),1==t.settings.seller_system?s("li",{class:{active:"followed_shop"===t.current}},[s("router-link",{attrs:{to:{name:"shop.followed"}}},[s("span",{staticClass:"mdi mdi-home-heart"}),t._v(t._s(t.lang.shop)+"\n                ")])],1):t._e()])]),t._v(" "),s("div",{staticClass:"modal fade reward",attrs:{id:"convert_reward",tabindex:"-1","aria-labelledby":"exampleModalLabel","aria-hidden":"true"}},[s("div",{staticClass:"modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable"},[s("div",{staticClass:"modal-content"},[s("div",{staticClass:"modal-header"},[s("h5",{staticClass:"modal-title"},[t._v(t._s(t.lang.reward_point))]),t._v(" "),t._m(0)]),t._v(" "),s("div",{staticClass:"modal-body reward_modal"},[s("form",{on:{submit:function(e){return e.preventDefault(),t.convertReward.apply(null,arguments)}}},[s("div",{staticClass:"row"},[s("div",{staticClass:"col-lg-12 text-center"},[s("div",{staticClass:"form-group"},[s("label",{attrs:{for:"reward"}},[t._v(t._s(t.lang.reward_point)+" ")]),t._v(" "),s("input",{directives:[{name:"model",rawName:"v-model",value:t.converted_reward,expression:"converted_reward"}],staticClass:"form-control",attrs:{type:"text",id:"reward",placeholder:t.lang.enter_point_you_want_convert},domProps:{value:t.converted_reward},on:{input:function(e){e.target.composing||(t.converted_reward=e.target.value)}}})]),t._v(" "),null!=t.totalReward?s("div",{staticClass:"text-start"},[s("p",[t._v("Available Points to Convert : "+t._s(t.totalReward.rewards))]),t._v(" "),s("p",[t._v(t._s(t.settings.reward_convert_rate)+t._s(t.lang.reward_points)+t._s(t.priceFormat(1)))]),t._v(" "),t.totalReward.rewards>0?s("p",[t._v(t._s(t.lang.total_amount_you_will_get)+"\n                                        "+t._s(t.priceFormat(t.converted_reward/t.settings.reward_convert_rate)))]):t._e()]):t._e(),t._v(" "),t.loading?s("loading_button",{attrs:{class_name:"btn btn-primary mt-3"}}):s("button",{staticClass:"btn btn-primary mt-3",class:{disable_btn:t.converted_reward<t.settings.reward_convert_rate||t.totalReward.rewards<t.converted_reward},attrs:{type:"submit"}},[t._v("\n                                    "+t._s(t.lang.covert_rewards)+"\n                                ")])],1)])])])])])])]):t._e()}),[function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("button",{staticClass:"close modal_close",attrs:{type:"button","data-bs-dismiss":"modal","aria-label":"Close"}},[s("span",{attrs:{"aria-hidden":"true"}},[t._v("×")])])}],!1,null,null,null).exports}}]);