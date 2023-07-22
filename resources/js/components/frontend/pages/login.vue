<template>
    <div class="sg-page-content">
        <section class="ragister-account text-center">
            <div class="container">
                <div class="account-content">
                    <div class="thumb">
                        <img v-lazy="settings.login_banner" alt="login_banner" class="img-fluid">
                    </div>
                    <div class="form-content">
                        <h1>{{lang.sign_in}}</h1>
                        <p>{{lang.sign_continue_shopping}}</p>
                        <form class="ragister-form" name="ragister-form" @submit.prevent="login">
                            <div class="form-group" v-if="optionTo=='phone'">
                                <span class="mdi mdi-name mdi-email-outline"></span>
                                <input type="email" v-model="form.email" class="form-control mb-1" :class="{ 'error_border' : errors.email }"
                                       :placeholder="lang.email">
                            </div>
                            <span class="validation_error" v-if="errors.email">{{ errors.email[0] }}</span>

                            <div v-if="optionTo=='email'">
                                <telePhone  @phone_no="getNumber"></telePhone>

                            </div>
                            <span class="validation_error" v-if="errors.phone">{{ errors.phone[0] }}</span>

                            <div class="form-group text-end mb-3" v-if="addons.includes('otp_system') && !otp_field">
                                <a href="javaScript:void(0)" class="btn sign-in-option" @click="loginOptions(optionTo)">{{
                                        optionTo == 'email' ? lang.use_email_instead : lang.use_phone_instead
                                    }}</a>
                            </div>
                            <div class="form-group mt-4" v-if="otp_field">
                                <span class="mdi mdi-name mdi-lock-outline"></span>
                                <input type="text" v-model="phoneForm.otp" class="form-control"
                                       :placeholder="lang.enter_your_otp">
                            </div>
                            <div class="form-group" :class="{ 'mt-4' : !addons.includes('otp_system') }" v-if="optionTo=='phone'">
                                <span class="mdi mdi-name mdi-lock-outline"></span>
                                <input type="password" v-model="form.password" class="form-control" :class="{ 'error_border' : errors.password }"
                                       :placeholder="lang.Password">
                            </div>
                            <span class="validation_error" v-if="errors.password">{{ errors.password[0] }}</span>

                            <div class="middle-content d-flex" v-if="optionTo == 'phone'">
                                <div class="form-group remember">
                                    <input type="checkbox" name="remember" v-model="form.remember" value="1"
                                           id="remember">
                                    <label for="remember">{{lang.remember_me}}</label>
                                </div>
                                <router-link :to="{name:'reset.password'}">
                                    <a href="javaScript:void(0)">{{lang.forgot_your_password}}</a>
                                </router-link>
                            </div>
                            <div v-if="settings.is_recaptcha_activated == 1" class="g-recaptcha mb-2"
                                 :class="optionTo == 'email' ? 'd-none': ''" data-callback="myCallback"
                                 :data-sitekey="settings.recaptcha_Site_key"></div>

                            <loading_button v-if="loading" :class_name="'btn'"></loading_button>
                            <button type="submit" v-else class="btn">{{ buttonText }}</button>

                            <div v-if="settings.demo_mode && !loading" class="d-flex justify-content-between mb-3">
                                <button type="button" href="javascript:void(0)" @click="copyLoginInfo('admin@spagreen.net')" class="btn copy_btn">Admin</button>
                                <button v-if="settings.seller_system == 1" type="button" href="javascript:void(0)" @click="copyLoginInfo('seller@spagreen.net')" class="btn copy_btn">Seller</button>
                                <button type="button" href="javascript:void(0)" @click="copyLoginInfo('customer@spagreen.net')" class="btn copy_btn">Customer</button>
                                <button type="button" href="javascript:void(0)" @click="copyLoginInfo('staff@spagreen.net')" class="btn copy_btn">Staff</button>
                            </div>

                            <p>{{lang.don_have_an_account}}
                                <router-link :to="{ name : 'register' }">{{lang.sign_up}}</router-link>
                            </p>
                            <div class="continue-with"
                                 v-if="settings.is_facebook_login_activated == 1 || settings.is_google_login_activated == 1 || settings.is_twitter_login_activated == 1">
                                <p>{{lang.or_continue_with}}</p>
                            </div>
                            <ul class="global-list">
                                <li v-if="settings.is_facebook_login_activated == 1"><a class="facebook"
                                                                                        href="javascript:void(0)"
                                                                                        @click="socialLogin('facebook')"><span
                                    class="mdi mdi-name mdi-facebook"></span>{{lang.facebook}}</a></li>
                                <li v-if="settings.is_twitter_login_activated == 1"><a class="twitter"
                                                                                       href="javascript:void(0)"
                                                                                       @click="socialLogin('twitter')"><span
                                    class="mdi mdi-name mdi-twitter"></span>{{lang.twitter}}</a></li>
                                <li v-if="settings.is_google_login_activated == 1"><a class="google"
                                                                                      href="javascript:void(0)"
                                                                                      @click="socialLogin('google')"><span
                                    class="mdi mdi-name mdi-google"></span>{{lang.google}}</a></li>
                            </ul>
                        </form>
                    </div>

                </div><!-- /.account-content -->
            </div><!-- /.container -->
        </section><!-- /.ragister-account -->

    </div>
</template>

<script>
import telePhone from "../partials/telephone";

export default {
    name: "sign_in",
    components : {
        telePhone
    },
    data() {
        return {
            form: {
                email: '',
                password: '',
                _token: this.token,
                remember: 0,
                captcha: '',
            },
            phoneForm: {
                phone: '',
                otp: '',
            },
            otp_field: false,
            loading: false,
            optionTo: 'phone',
            buttonText: 'Sign In',
        }
    },
    mounted() {
        if (this.authUser) {
            this.$router.go(-1);
        }
        if (this.settings.is_recaptcha_activated == 1) {
            this.captcha();
        }
        this.loginOptions();
    },
    watch : {
        lang(){
            this.loginOptions();
        }
    },
    computed: {
        loginRedirect()
        {
            return this.$store.getters.getLoginRedirection;
        }
    },

    methods: {
        login(direct_login) {
            let form =this.form; let url = this.getUrl('login');
            if (direct_login == 'direct_login')
            {
                this.form.captcha = '1313132';
            }
            else{
                if (this.settings.is_recaptcha_activated == 1 && this.optionTo == 'phone') {
                    let captcha = window.captcha;

                    if (captcha) {
                        this.form.captcha = captcha;
                    } else {
                        return toastr.warning(this.lang.verify_google_recaptcha, this.lang.Warning +' !!' );
                    }
                }
            }

            const axiosWithCredentials = axios.create({
                withCredentials: true
            });
            this.$store.commit('getCountCompare', true);

            if (direct_login != 'direct_login')
            {
                if (this.optionTo == 'phone') {
                    form = this.form;
                } else if (this.optionTo == 'email' && !this.otp_field) {
                    url = this.getUrl('get-otp');
                    form = this.phoneForm;
                } else if (this.otp_field) {
                    url = this.getUrl('submit-otp');
                    form = this.phoneForm;
                }
            }

            this.loading = true;

            axiosWithCredentials.post(url, form).then((response) => {
                this.loading = false;
                if (response.data.error) {
                    toastr.error(response.data.error, this.lang.Error +' !!' );
                }

                if (response.data.success) {

                    window.captcha = '';
                    this.errors = [];

                    if (this.optionTo == 'email' && !this.otp_field && direct_login != 'direct_login') {
                        this.otp_field = true;
                        this.buttonText = this.lang.sign_in;
                    } else {
                        if (this.loginRedirect)
                        {
                            this.$router.push({ name: this.loginRedirect });
                        }
                        else{
                            let user = response.data.user;
                            if (user.user_type == 'customer') {
                                this.$router.push({name: 'dashboard'});
                            } else if (user.user_type == 'admin' || user.user_type == 'staff') {
                                this.loading = true;
                                document.location.href = this.getUrl('admin/dashboard');
                            } else if (user.user_type == 'seller') {
                                this.loading = true;
                                document.location.href = this.getUrl('seller/dashboard');
                            }
                        }


                        this.$store.dispatch('carts', response.data.carts);
                        this.$store.dispatch('user', response.data.user);
                        this.$store.dispatch('compareList', response.data.compare_list);
                        this.$store.dispatch('wishlists', response.data.wishlists);
                    }
                }
            }).catch((error) =>{
                this.loading = false;
                if (error.response && error.response.status == 422)
                {
                    this.errors = error.response.data.errors;
                }
            });
        },
        socialLogin(provider) {
            let url = this.getUrl('login/' + provider);
            axios.get(url).then((response) => {
                window.location.href = response.data;
            })
        },
        loginOptions(optionTo) {
            this.errors = [];
            if (optionTo)
            {
                if (optionTo == 'phone') {
                    this.buttonText = this.lang.get_oTP;
                    this.optionTo = 'email';
                } else {
                    this.buttonText = this.lang.sign_in;
                    this.optionTo = 'phone';
                }
            }
            else{
                if (this.addons.includes('otp_system'))
                {

                    this.optionTo = 'email';
                    this.buttonText = this.lang.get_oTP;
                }
                else {
                    this.buttonText = this.lang.sign_in;
                    this.optionTo = 'phone';
                }
            }

        },
        captcha() {
            const script = document.createElement("script")
            script.src = "https://www.google.com/recaptcha/api.js";
            document.body.appendChild(script);
        },
        copyLoginInfo(email)
        {
            this.form.email = email;
            this.form.password = '123456';
            this.login('direct_login');
        },
        getNumber(number){
            this.phoneForm.phone = number;
        }
    },
}
</script>
