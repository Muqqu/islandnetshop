<template>
  <div class="sg-page-content" v-if="authUser && authUser.user_type == 'customer'">

    <section class="sg-global-content">
      <div class="container">
        <div class="row">
          <user_sidebar :current="current"></user_sidebar>
          <div class="col-lg-9">
            <div class="profile-banner-image">
              <a href="#"><img v-lazy="settings.user_dashboard_banner" alt="profile-banner"></a>
            </div>
            <div class="title justify-between">
              <h1>{{ lang.dashboard }}</h1>
            </div>
            <div class="row">
              <div class="col-md-4">
                <router-link :to="{ name : 'order.history' }">
                  <div class="card text-center profile-card d-flex justify-center profile-card-red">
                    <div class="profile-card-title mb-3">{{ lang.total_order }}</div>
                    <h3 class="text-white ">{{ profileOrders ? profileOrders.total : 0 }}</h3>
                  </div>
                </router-link>
              </div>
              <div class="col-md-4">
                <router-link :to="{name : 'cart'}">
                  <div class="card text-center profile-card d-flex justify-center profile-card-gray">
                    <div class="profile-card-title mb-3">{{ lang.product_cart }}</div>
                    <h3 class="text-white"> {{ carts ? carts.length : 0 }}</h3>
                  </div>
                </router-link>
              </div>
              <div class="col-md-4">
                <router-link :to="{ name : 'wishlist' }">
                  <div class="card text-center profile-card d-flex justify-center profile-card-green">
                    <div class="profile-card-title mb-3">{{ lang.product_you_love }}</div>
                    <h3 class="text-white">{{ wishlists }}</h3>
                  </div>
                </router-link>
              </div>
              <div class="col-md-4" v-if="settings.wallet_system == 1">
                <router-link :to="{ name : 'wallet.history' }">
                  <div class="card text-center profile-card d-flex justify-center profile-card-white">
                    <div class="profile-card-title text-black mb-3">{{ lang.wallet_balance }}</div>
                    <h3 class="text-black">{{ priceFormat(authUser.balance) }}</h3>
                  </div>
                </router-link>
              </div>
              <div class="col-md-4" v-if="settings.wallet_system == 1">
                <div class="card text-center profile-card d-flex justify-center profile-card-white">
                  <div class="profile-card-title mb-3 text-black">{{ lang.last_recharge }}</div>
                  <h3 class="text-black">{{ priceFormat(authUser.last_recharge) }}</h3>
                </div>
              </div>
              <div class="col-md-4" v-if="settings.wallet_system == 1">
                <a href="#" data-bs-target="#recharge_wallet" data-bs-toggle="modal">
                  <div
                      class="card text-center profile-card d-flex justify-center profile-card-white-outline-dashed">
                    <div class="profile-card-title mb-3">{{ lang.recharge_wallet }}</div>
                    <h3><i class="mdi mdi-plus"></i></h3>
                  </div>
                </a>
              </div>

              <div class="col-md-4" v-if="addons.includes('reward')">
                <router-link :to="{name : 'reward.history'}">
                  <div class="card text-center profile-card d-flex justify-center profile-card-white">
                    <div class="profile-card-title mb-3 text-black">{{ lang.total_rewards }}</div>
                    <h3 class="text-black"> {{ totalReward ? totalReward.reward_sum : 0 }}</h3>
                  </div>
                </router-link>
              </div>
              <div class="col-md-4" v-if="addons.includes('reward')">
                <router-link :to="{name : 'reward.history'}">
                  <div class="card text-center profile-card d-flex justify-center profile-card-white">
                    <div class="profile-card-title mb-3 text-black">{{ lang.current_rewards }}</div>
                    <h3 class="text-black"> {{ totalReward ? totalReward.rewards : 0 }}</h3>
                  </div>
                </router-link>
              </div>
              <div class="col-md-4"
                   v-if="settings.reward_convert_rate > 0 && totalReward && totalReward.rewards > 0 && addons.includes('reward') && settings.wallet_system == 1">
                <a href="#" data-bs-target="#convert_reward" data-bs-toggle="modal">
                  <div class="card text-center profile-card d-flex justify-center profile-card-white">
                    <div class="profile-card-title mb-3">{{ lang.convert_reward_wallet }}</div>
                    <h3><i class="mdi mdi-transfer"></i></h3>
                  </div>
                </a>
              </div>
            </div>

            <div class="sg-shipping" v-if="authUser">
              <div class="row">
                <div class="col-md-6">
                  <div class="title mt-3 mb-0 b-0">
                    <h1>{{ lang.default_shipping }}</h1>
                  </div>
                  <div class="sg-card address" v-if="default_shipping">
                    <div class="justify-content-between d-flex">
                      <div class="text">
                        <ul class="global-list">
                          <li>{{ lang.name }}: {{ default_shipping.name }}</li>
                          <li>{{ lang.email }}: {{ default_shipping.email }}</li>
                          <li>{{ lang.phone }}: {{ default_shipping.phone_no }}</li>
                          <li>{{ lang.street_address }}: {{ default_shipping.default_shipping }}</li>
                          <li>{{ lang.city }}: {{ default_shipping.city }}</li>
                          <li>{{ lang.country }}: {{ default_shipping.country }}</li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="title mt-3 mb-0 b-0">
                    <h1>{{ lang.default_billing }}</h1>
                  </div>
                  <div class="sg-card address" v-if="default_billing">
                    <div class="justify-content-between d-flex">
                      <div class="text">
                        <ul class="global-list">
                          <li>{{ lang.name }}: {{ default_billing.name }}</li>
                          <li>{{ lang.email }}: {{ default_billing.email }}</li>
                          <li>{{ lang.phone }}: {{ default_billing.phone_no }}</li>
                          <li>{{ lang.street_address }}: {{ default_billing.default_shipping }}</li>
                          <li>{{ lang.city }}: {{ default_billing.city }}</li>
                          <li>{{ lang.country }}: {{ default_billing.country }}</li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-12" v-if="profileOrders && profileOrders.total > 0">
              <div class="sg-table">
                <div class="title justify-content-between">
                  <h1>{{ lang.order_history }}</h1>
                </div>
                <orders :orders="profileOrders.data" :user_dashboard="false"></orders>
              </div>
            </div>
          </div>
        </div>
      </div><!-- /.container -->
    </section>
    <div class="modal fade" id="recharge_wallet" tabindex="-1" aria-labelledby="recharge_wallet"
         aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ lang.wallet_recharge }}</h5>
            <button type="button" class="close modal_close" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-lg-12">
                <div class="sg-shipping">
                  <div class="sg-card">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group">
                          <label for="amount">{{ lang.amount }}</label>
                          <input type="text" class="form-control" id="amount" @input="removeData"
                                 v-model="form.total" :placeholder="lang.enter_amount">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card-list" :class="{ 'disable_section' : form.total == 0 }">
                    <ul class="global-list grid-3">
                      <li v-if="settings.is_paypal_activated == 1">
                        <div class="input-checkbox">
                          <input type="radio" value="paypal" @change="razorPayRemove"
                                 v-model="payment_form.payment_type"
                                 id="paypal" name="radio">
                          <label for="paypal">
                            <img :src="getUrl('public/images/payment-method/paypal.svg')" :alt="payment_form.payment_type"
                                 class="img-fluid">
                            {{ lang.pay_with_payPal }}
                          </label>
                        </div>
                      </li>
                      <li v-if="settings.is_stripe_activated == 1">
                        <div class="input-checkbox">
                          <input type="radio" id="stripe" @change="razorPayRemove" value="stripe"
                                 v-model="payment_form.payment_type" name="radio">
                          <label for="stripe">
                            <img :src="getUrl('public/images/payment-method/stripe.svg')" :alt="payment_form.payment_type"
                                 class="img-fluid">
                            {{ lang.pay_with_stripe }}
                          </label>
                        </div>
                      </li>
                      <li v-if="settings.is_sslcommerz_activated == 1">
                        <div class="input-checkbox">
                          <input type="radio" name="radio" @change="razorPayRemove"
                                 v-model="payment_form.payment_type"
                                 id="ssl_commerze" value="ssl_commerze">
                          <label for="ssl_commerze">
                            <img :src="getUrl('public/images/payment-method/sslcommerze.svg')"
                                 :alt="payment_form.payment_type" width="90">
                            {{ lang.pay_with_sSLCOMMERZE }}
                          </label>
                        </div>
                      </li>
                      <li v-if="settings.is_paytm_activated == 1">
                        <div class="input-checkbox">
                          <input type="radio" id="paytm" value="paytm" @change="razorPayRemove"
                                 v-model="payment_form.payment_type" name="radio">
                          <label for="paytm">
                            <img :src="getUrl('public/images/payment-method/paytm.svg')" :alt="payment_form.payment_type"
                                 class="img-fluid">
                            {{ lang.pay_with_paytm }}
                          </label>
                        </div>
                      </li>
                      <li v-if="settings.is_razorpay_activated == 1">
                        <div class="input-checkbox">
                          <input type="radio" id="razor_pay" value="razor_pay"
                                 v-model="payment_form.payment_type" @change="integrateRazorPay"
                                 name="radio">
                          <label for="razor_pay">
                            <img :src="getUrl('public/images/payment-method/razorpay.svg')" :alt="payment_form.payment_type"
                                 width="90"
                                 class="img-fluid">
                            {{ lang.pay_with_razorpay }}
                          </label>
                        </div>
                      </li>
                      <li v-if="settings.is_jazz_cash_activated == 1">
                        <div class="input-checkbox">
                          <input type="radio" id="jazzCash" @change="razorPayRemove" value="jazz_cash"
                                 v-model="payment_form.payment_type" name="radio">
                          <label for="jazzCash">
                            <img :src="getUrl('public/images/payment-method/jazzCash.svg')" :alt="payment_form.payment_type"
                                 width="90"
                                 class="img-fluid">
                            {{ lang.pay_with_jazzCash }}
                          </label>
                        </div>
                      </li>
                      <li v-if="settings.is_paystack_activated == 1">
                        <div class="input-checkbox">
                          <input type="radio" id="paystack" @change="razorPayRemove" value="paystack"
                                 v-model="payment_form.payment_type" name="radio">
                          <label for="paystack">
                            <img :src="getUrl('public/images/payment-method/paystack.svg')" :alt="payment_form.payment_type"
                                 width="90"
                                 class="img-fluid">
                            {{ lang.pay_with_paystack }}
                          </label>
                        </div>
                      </li>
                      <li v-if="settings.is_flutterwave_activated == 1">
                        <div class="input-checkbox">
                          <input type="radio" id="flutter_wave" @change="razorPayRemove" value="flutter_wave"
                                 v-model="payment_form.payment_type" name="radio">
                          <label for="flutter_wave">
                            <img :src="getUrl('public/images/payment-method/fw.svg')" :alt="payment_form.payment_type"
                                 width="90"
                                 class="img-fluid">
                            {{ lang.pay_with_flutter }}
                          </label>
                        </div>
                      </li>
                      <li v-if="settings.is_mollie_activated == 1">
                        <div class="input-checkbox">
                          <input type="radio" id="mollie" @change="razorPayRemove" value="mollie"
                                 v-model="payment_form.payment_type" name="radio">
                          <label for="mollie">
                            <img :src="getUrl('public/images/payment-method/mollie.svg')" :alt="payment_form.payment_type"
                                 width="90"
                                 class="img-fluid">
                            {{ lang.pay_with_mollie }}
                          </label>
                        </div>
                      </li>
                      <li v-if="addons.includes('offline_payment')" v-for="(offline,index) in offline_methods"
                          :key="index">
                        <div class="input-checkbox">
                          <input type="radio" :id="'offline'+offline.id"
                                 @change="offlineCheck(offline)"
                                 value="offline_method"
                                 name="radio" v-model="payment_form.payment_type">
                          <label :for="'offline'+offline.id">
                            <img v-lazy="offline.image" :alt="offline.name"
                                 class="img-fluid">
                            {{ offline.name }}
                          </label>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
                <div class="row justify-content-center text-end mt-3" :class="{ 'disable_section' : !form.total }">
                  <div class="col-lg-5" id="payment_buttons">
                    <div class="mx-auto" v-show="payment_form.payment_type == 'paypal'"
                         id="paypal-button-container" ref="paypal"></div>

                    <a href="javascript:void(0)" class="btn btn-primary w-100 disable_btn"
                       v-if="!payment_form.payment_type">{{ lang.pay_now }}</a>

                    <a href="javascript:void(0)" @click="payment" class="btn btn-primary w-100"
                       v-if="payment_form.payment_type == 'stripe' ">{{ lang.pay_now }}</a>

                    <a href="javascript:void(0)" @click="payment" class="btn btn-primary w-100"
                       v-if="payment_form.payment_type == 'ssl_commerze' ">{{ lang.pay_now }}</a>

                    <a href="javascript:void(0)" @click="payment" class="btn btn-primary w-100"
                       v-if="payment_form.payment_type == 'paytm' ">{{ lang.pay_now }}</a>

                    <a href="javascript:void(0)" class="btn btn-primary w-100" data-bs-toggle="modal"
                       data-bs-target="#offline"
                       v-if="offline_method.name">{{ lang.pay_now }}</a>
                    <a href="#" class="btn btn-primary w-100" data-bs-toggle="modal"
                       data-bs-target="#paystack_modal" @click="payment_component_load = true"
                       v-if="payment_form.payment_type == 'paystack' ">
                      {{ lang.pay_now }}</a>

                    <a href="#" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#fw_modal"
                       v-if="payment_form.payment_type == 'flutter_wave' ">
                      {{ lang.pay_now }}</a>

                    <a :href="getUrl('mollie/recharge-payment/?amount=' + form.total)" class="btn btn-primary w-100"
                       v-if="payment_form.payment_type == 'mollie'">{{ lang.pay_now }}</a>

                    <form name="jsform" :action="jazz_url" method="get">
                      <input v-for="(value,name) in jazz_data" :key="name" type="hidden" :name="name"
                             :value="value">
                      <button type="submit" class="btn btn-primary w-100"
                              v-show="!loading"
                              v-if="payment_form.payment_type == 'jazz_cash'">{{ lang.pay_now }}
                      </button>
                    </form>

                    <form :action="getUrl('user/recharge-wallet')" method="post"
                          v-show="payment_form.payment_type == 'razor_pay'">
                      <input type="hidden" name="_token" :value="token">
                      <input type="hidden" name="payment_type" :value="payment_form.payment_type">
                      <input type="hidden" name="amount" :value="form.total">
                      <div ref="razor_pay">

                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- /.modal-body -->
        </div>
      </div>
    </div>
    <div class="modal fade" id="offline" tabindex="-1" aria-labelledby="offline_modal"
         aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ lang.pay_with }}{{ offline_method.name }}</h5>
            <button type="button" class="close modal_close" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-lg-12">
                <div class="form-group">
                  <label>{{ lang.upload_file }}</label>
                  <div class="input-group">
                    <div class="custom-file d-flex">
                      <label class="upload-image form-control" for="upload-1">
                        <input type="file" id="upload-1" @change="imageUp($event)">
                        <i id="upload-image">{{ lang.upload_file }}</i>
                      </label>
                      <label class="upload-image upload-text" for="upload-2">
                        <input type="file" id="upload-2" @change="imageUp($event)">
                        <img v-lazy="defaultAssets.review_image" :alt="defaultAssets.preloader"
                             class="img-fluid">
                        {{ lang.upload }}
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-12" v-if="offline_method.instructions">
                <label>{{ lang.instructions }}</label>
                <div class="instruction" v-html="offline_method.instructions"></div>
              </div>
              <div class="col-lg-12 text-center mt-3">
                <button @click="payment" class="btn btn-primary" v-show="!loading">{{ lang.proceed }}</button>
                <loading_button v-show="loading" :class_name="'btn btn-primary'"></loading_button>
              </div>
            </div>
          </div><!-- /.modal-body -->
        </div>
      </div>
    </div>
    <div class="modal fade" id="paystack_modal" tabindex="-1" aria-labelledby="paystack_modal"
         aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ lang.pay_with_paystack }}</h5>
            <button type="button" class="close modal_close" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <paystack v-if="payment_component_load" :trx_id="trx_id" :transaction_type="'wallet_recharge'"
                    :paystack_key="settings.paystack_pk" :ngn_exchange_rate="settings.ngn_exchange_rate" :code="code"
                    :amount="form.total" :type="payment_form.payment_type"></paystack>
        </div>
      </div>
    </div>
    <flutter_wave :trx_id="trx_id" :code="code" :transaction_type="'wallet_recharge'" :amount="form.total"
                  :type="payment_form.payment_type" ref="flutter_wave"></flutter_wave>
  </div>
</template>

<script>
import orders from "./../../partials/orders";
import user_sidebar from "../../partials/user_sidebar";
import shimmer from "../../partials/shimmer";
import paystack from "../../payment_partials/paystack";
import Flutter_wave from "../../payment_partials/flutter_wave";

export default {
  name: "dashboard",
  data() {
    return {
      current: 'dashboard',
      amount: 0,
      offline_methods: [],
      indian_currency: {},
      form: {
        total: ''
      },
      ssl: {
        name: null,
        email: null,
        phone: null,
      },

      razor_form: {
        name: null,
        email: null,
        phone: null,
        description: null,
      },
      loading: false,
      jazz_data: [],
      jazz_url: '',
      offline_method: {
        id: '',
        name: '',
        image: '',
        instructions: '',
      },
      trx_id: "",
      code: "",
      payment_component_load: false,
    }
  },
  components: {
    user_sidebar, orders, shimmer, paystack, Flutter_wave
  },
  mounted() {
    this.getProfileOrders();
    this.takeData();
    if (this.settings.is_paypal_activated == 1) {
      this.loadPaypal();
    }
  },

  computed: {
    authUser() {
      return this.$store.getters.getUser;
    },
    profileOrders() {
      return this.$store.getters.getProfileOrders
    },
    carts() {
      return this.$store.getters.getCarts;
    },
    wishlists() {
      return this.$store.getters.getTotalWishlists;
    },
    activeCurrency() {
      return this.$store.getters.getActiveCurrency;
    },
    totalReward() {
      return this.$store.getters.getTotalReward === '' ? null : this.$store.getters.getTotalReward;
    },
    shimmer() {
      return this.$store.state.module.shimmer
    },
    default_shipping() {
      return this.authUser ? this.$store.getters.getUser.shipping_address : null
    },
    default_billing() {
      return this.authUser ? this.$store.getters.getUser.billing_address : null

    }

  },
  methods: {
    getProfileOrders() {
      let url = this.getUrl('user/profile-orders')
      axios.get(url).then((response) => {
        this.$store.commit('setShimmer', false)
        this.$store.commit("getProfileOrders", response.data.orders);
      })
    },
    payment() {
      let payment_type = this.payment_form.payment_type;

      if (!payment_type) {
        toastr.warning(this.lang.please_choose_a_payment_method, this.lang.Warning + ' !!');

        return false;
      }
      let form = {
        id: this.offline_method.id,
        file: this.product_form.image,
        payment_type: payment_type,
        amount: this.form.total,
      };

      let url = this.getUrl('user/recharge-wallet');

      if (this.offline_method.id) {
        this.loading = true;

        axios.post(url, form, {

          transformRequest: [function (data, headers) {
            return objectToFormData(data)
          }]
        }).then((response) => {
          this.loading = false;
          if (response.data.error) {
            toastr.error(response.data.error, this.lang.Error + ' !!');
          } else {
            $('#offline').modal('hide');
            let unshift = 1;
            this.$store.commit("getWalletRecharges", {unshift: unshift, data: response.data.recharges.data});
            let image_selector = document.getElementById('upload-image');
            if (image_selector) {
              image_selector.innerHTML = '';
            }
            this.$router.push({name: 'wallet.history'});
          }
        }).catch((error) => {
          this.loading = false;
        })
        ;
      } else if (payment_type == 'stripe') {
        return window.location.href = this.getUrl("stripe/recharge/?amount="+ this.form.total)
      } else if (payment_type == 'paytm') {
        return window.location.href = this.getUrl("user/payment/paytmRedirect?amount=" + this.form.total);
      } else if (payment_type == 'jazz_cash') {
        return window.location.href = this.getUrl('jazz/redirect');
      } else if (payment_type == 'ssl_commerze') {
        window.location.href = this.getUrl('get/ssl-response?payment_type=ssl_commerze&type=wallet&amount='+this.form.total);
      }
    },
    takeData() {
      this.$Progress.start();
      let url = this.getUrl('user/wallet-data');
      axios.get(url).then((response) => {
        if (response.data.error) {
          this.$Progress.fail();
          toastr.error(response.data.error, this.lang.Error + ' !!');
          ;
        } else {
          this.$Progress.finish();
          this.indian_currency = response.data.indian_currency;
          this.offline_methods = response.data.offline_methods;
          this.jazz_data = response.data.jazz_data;
          this.jazz_url = response.data.jazz_url;
        }
      })
    },
    integrateRazorPay() {
      this.razorPayRemove();
      if (this.payment_form.payment_type == 'razor_pay' && this.indian_currency) {
        var razorKeys = document.querySelectorAll('.razorpay-payment-button');

        for (let i = 0; i < razorKeys.length; i++) {
          razorKeys[i].style.display = "none";
        }

        let myScript = document.createElement('script');

        myScript.setAttribute('type', 'text/javascript');
        myScript.setAttribute('language', 'javascript');
        myScript.setAttribute('data-key', this.settings.razor_key);
        myScript.setAttribute('data-amount', parseInt(this.form.total * 100 * this.indian_currency.exchange_rate));
        myScript.setAttribute('data-name', 'Yoori');
        myScript.setAttribute('data-description', 'Rozerpay');
        myScript.setAttribute('data-image', this.settings.dark_logo);
        myScript.setAttribute('data-prefill.name', 'name');
        myScript.setAttribute('data-prefill.email', 'email');
        myScript.setAttribute('data-prefill.address', 'address');
        myScript.setAttribute('data-theme.color', '#ff7529');
        myScript.setAttribute('src', this.defaultAssets.razor_pay_js);

        // Append script
        this.$refs.razor_pay.insertAdjacentElement('afterend', myScript);
      }
      if (this.payment_form.payment_type == 'ssl_commerze') {
        var ssl_keys = document.getElementById('sslczPayBtn');
        if (ssl_keys) {
          ssl_keys.setAttribute("endpoint", this.getUrl('user/recharge-wallet?payment_type=ssl_commerze&amount=' + this.form.total));
        }
      }

    },
    razorPayRemove() {
      var razorKeys = document.querySelectorAll('.razorpay-payment-button');

      for (let i = 0; i < razorKeys.length; i++) {
        razorKeys[i].style.display = "none";
      }

      this.offline_method.name = '';
      this.offline_method.image = '';
      this.offline_method.instructions = '';
    },
    loadPaypal() {
      const script = document.createElement("script")
      script.src = "https://www.paypal.com/sdk/js?client-id=" + this.settings.paypal_key;
      script.setAttribute('data-namespace', 'paypal_sdk');
      script.addEventListener("load", this.setLoaded);
      document.body.appendChild(script);
    },
    setLoaded: function () {
      window.paypal_sdk.Buttons({
        createOrder: (data, actions) => {
          return actions.order.create({
            purchase_units: [
              {
                description: "Product Purchase",
                amount: {
                  currency_code: "USD",
                  value: this.round(this.form.total / this.activeCurrency.exchange_rate, 2)
                }
              }
            ]
          });
        },
        onApprove: async (data, actions) => {
          const order = await actions.order.capture();
          // this.data;
          this.loading = true;
          let url = this.getUrl('user/recharge-wallet');
          data.amount = this.form.total;
          data.payment_type = this.payment_form.payment_type;
          data.order = order;

          this.axios.post(url, data)
              .then((response) => {
                if (response.data.error) {
                  toastr.error(response.data.error, this.lang.Error + ' !!');
                } else {
                  this.loading = false;
                  $('#recharge_wallet').modal('hide');
                  let unshift = 1;
                  this.$store.commit("getWalletRecharges", {unshift: unshift, data: response.data.recharges.data});
                  this.$router.push({name: 'wallet.history'});
                  toastr.success(response.data.success, this.lang.Success + ' !!');
                }
              })
        },
        onError: err => {
          alert('Error')
        }
      }).render(this.$refs.paypal);
    },
    removeData() {
      this.payment_form.payment_type = '';
      this.razorPayRemove();
    },
    offlineCheck(offline) {
      this.razorPayRemove();
      this.offline_method.id = offline.id;
      this.offline_method.name = offline.name;
      this.offline_method.image = offline.image;
      this.offline_method.instructions = offline.instructions;
    },
  }
}
</script>
