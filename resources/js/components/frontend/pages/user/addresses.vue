<template>
    <div class="sg-page-content">
        <section class="sg-global-content">
            <div class="container">
                <div class="row">
                    <user_sidebar :current="current"></user_sidebar>
                    <div class="col-lg-9">
                        <div class="sg-shipping">
                            <div class="title">
                                <h1>{{ lang.addresses }}</h1>
                            </div>
                            <div class="accordion add-new" ref="update">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <button class="accordion-button" :class="{ 'collapsed' : !address_area }"
                                                type="button" data-bs-toggle="collapse"
                                                @click="address_area = !address_area"
                                                data-bs-target="#shipping_accordion" aria-expanded="false"
                                                aria-controls="collapse1">{{ lang.address_area_title }}
                                        </button>
                                    </div>
                                    <div id="shipping_accordion" class="accordion-collapse collapse"
                                         :class="{ 'show' : address_area }" aria-labelledby="address1"
                                         data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <form @submit.prevent="saveAddress()">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>{{ lang.name }}</label>
                                                            <input type="text" class="form-control"
                                                                   :class="{ 'error_border' : errors.name }"
                                                                   :placeholder="lang.name"
                                                                   v-model="form.name">
                                                        </div>
                                                        <span class="validation_error"
                                                              v-if="errors.name">{{ errors.name[0] }}</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>{{ lang.email }}</label>
                                                            <input type="email" class="form-control"
                                                                   :placeholder="lang.email"
                                                                   :class="{ 'error_border' : errors.email }"
                                                                   v-model="form.email">
                                                        </div>
                                                        <span class="validation_error"
                                                              v-if="errors.email">{{ errors.email[0] }}</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>{{ lang.phone }}</label>
                                                        <telePhone @phone_no="getNumber"></telePhone>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>{{ lang.country }}</label>
                                                            <select class="form-control"
                                                                    :class="{ 'error_border' : errors.country_id }"
                                                                    v-model="form.country_id"
                                                                    @change="getStates()">
                                                                <option value="">{{ lang.select_country }}</option>
                                                                <option v-for="(country,index) in countries"
                                                                        :key="'country'+index" :value="country.id">
                                                                    {{ country.name }}
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <span class="validation_error"
                                                              v-if="errors.country_id">{{ errors.country_id[0] }}</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>{{ lang.state }}</label>
                                                            <select class="form-control"
                                                                    :class="{ 'error_border' : errors.state_id }"
                                                                    v-model="form.state_id"
                                                                    @change="getCities()">
                                                                <option value="">{{ lang.select_state }}</option>
                                                                <option v-for="(state,index) in states"
                                                                        :key="'state'+index" :value="state.id">
                                                                    {{ state.name }}
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <span class="validation_error"
                                                              v-if="errors.state_id">{{ errors.state_id[0] }}</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>{{ lang.city }}</label>
                                                            <select class="form-control"
                                                                    :class="{ 'error_border' : errors.city_id }"
                                                                    v-model="form.city_id">
                                                                <option value="">{{ lang.select_city }}</option>
                                                                <option v-for="(city,index) in cities"
                                                                        :key="'city'+index" :value="city.id">
                                                                    {{ city.name }}
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <span class="validation_error"
                                                              v-if="errors.city_id">{{ errors.city_id[0] }}</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>{{ lang.postal_code }}</label>
                                                            <input type="text" class="form-control"
                                                                   :class="{ 'error_border' : errors.postal_code }"
                                                                   :placeholder="lang.postal_code"
                                                                   v-model="form.postal_code">
                                                        </div>
                                                        <span class="validation_error" v-if="errors.postal_code">{{
                                                                errors.postal_code[0]
                                                            }}</span>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                        <div class="form-group">
                                                            <label>{{ lang.address }}</label>
                                                            <textarea v-model="form.address"
                                                                      class="form-control"
                                                                      :class="{ 'error_border' : errors.address }"
                                                                      :placeholder="lang.street_address"></textarea>
                                                        </div>
                                                        <span class="validation_error"
                                                              v-if="errors.address">{{ errors.address[0] }}</span>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group text-end">
                                                            <button type="submit" class="btn btn-primary">
                                                                {{ lang.address_submit_button }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.accordion -->
                            <div class="row" v-if="lengthCounter(addresses) > 0">
                                <div class="col-lg-6"
                                     v-for="(address, index) in addresses" :key="index">
                                    <div class="sg-card address">
                                        <div class="justify-content-between d-flex">
                                            <div class="text">
                                                <ul class="global-list">
                                                    <li>{{ lang.name }}: {{ address.name }}</li>
                                                    <li>{{ lang.email }}: {{ address.email }}</li>
                                                    <li>{{ lang.phone }}: {{ address.phone_no }}</li>
                                                    <li>{{ lang.street_address }}: {{ address.state }}</li>
                                                    <li>{{ lang.city }}: {{ address.city }}</li>
                                                    <li>{{ lang.country }}: {{ address.country }}</li>
                                                </ul>
                                            </div>
                                            <div class="dropdown float-right">
                                                <span class="mdi mdi-name mdi-dots-vertical dropbtn"></span>
                                                <div class="dropdown-content">
                                                    <a href="javascript:void(0)"
                                                       @click="edit(address)">{{ lang.edit }}</a>
                                                    <a href="javascript:void(0)" v-if="!address.default_shipping"
                                                       @click="makeDefault(address.id, 'shipping')">{{ lang.make_default_shipping }}</a>
                                                    <a href="javascript:void(0)" v-if="!address.default_billing"
                                                       @click="makeDefault(address.id, 'billing')">{{ lang.make_default_billing }}</a>
                                                    <a href="javascript:void(0)"
                                                       v-if="!address.default_shipping && !address.default_billing"
                                                       @click="deleteAddress(address.id)">{{ lang.delete }}</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="default-batch"
                                                 v-if="address.default_shipping && address.default_billing">
                                                {{ lang.default_shipping_billing }}
                                            </div>
                                            <div class="default-batch" v-else-if="address.default_shipping">
                                                {{ lang.default_shipping }}
                                            </div>
                                            <div class="default-batch" v-else-if="address.default_billing">
                                                {{ lang.default_billing }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" v-else-if="shimmer">
                                <div class="col-lg-6 mb-3" v-for="(address, index) in 4" :key="'address'+index">
                                    <shimmer :height="200"></shimmer>
                                </div>
                            </div>
                        </div><!-- /.sg-shipping -->
                    </div>
                </div><!-- /.row -->
            </div><!-- /.container -->
        </section><!-- /.profile-section -->

    </div>
</template>

<script>
import user_sidebar from "../../partials/user_sidebar";
import shimmer from "../../partials/shimmer";
import telePhone from "../../partials/telephone";


export default {
    name: "addresses",
    components: {
        user_sidebar, shimmer,telePhone
    },
    data() {
        return {
            current: 'addresses',
            length_check: "",
            // addresses: [],

            form: {
                name: '',
                email: '',
                phone_no: '',
                address: '',
                country_id: '',
                state_id: '',
                city_id: '',
                postal_code: '',
                id: '',
            },

            default_shipping: this.$store.getters.getUser.billing_address,
            default_billing: this.$store.getters.getUser.shipping_address,
            address_area: false,
            address_area_title: 'Add a new address',
            address_submit_button: 'Add',
            // countries: [],
            states: [],
            cities: [],
            countries: [],
            country_code : [],
           is_edit : false,
        }
    },
    mounted() {
        // if (Object.keys(this.addresses).length == 0) {
            this.getAddress();
        // }
        this.length_check = Object.keys(this.addresses).length

    },
    computed: {
        addresses() {
            return this.$store.getters.getUserAddresses
        },
        shimmer() {
            return this.$store.state.module.shimmer
        },
        flags(){
            return this.$store.getters.getFlags
        }
    },
    methods: {
        getAddress() {
            let url = this.getUrl('user/address');
            this.$Progress.start();
            axios.get(url).then((response) => {
                if (response.data.error) {
                    this.$Progress.fail();
                    toastr.error(response.data.error, this.lang.Error +' !!' );
                } else {
                    this.$store.commit('getUserAddresses', response.data.addresses);
                    this.$store.commit('setShimmer', false)
                    this.countries = response.data.countries;
                    this.country_code = response.data.countries;
                    if (this.addresses.length == 0) {
                        this.address_area = true;
                    }
                    this.$Progress.finish();
                }
            }).catch((error) => {
                this.$Progress.fail();
                if (error.response.status == 422) {
                    this.errors = error.response.data.errors;
                }
            })
        },
        getStates(address) {
            let country_id = this.form.country_id;

            let url = this.getUrl('state/by-country/' + country_id);
            axios.get(url).then((response) => {
                if (response.data.error) {
                    toastr.error(response.data.error, this.lang.Error +' !!' );
                } else {
                    this.states = response.data.states;
                    if (address && address.address_ids) {
                        this.form.state_id = address.address_ids.state_id;
                        this.getCities(address);
                    }
                }
            });
        },
        getCities(address) {
            let state_id = this.form.state_id;

            let url = this.getUrl('city/by-state/' + state_id);
            axios.get(url).then((response) => {
                if (response.data.error) {
                    toastr.error(response.data.error, this.lang.Error +' !!' );
                } else {
                    this.cities = response.data.cities;
                    if (address && address.address_ids) {
                        this.form.city_id = address.address_ids.city_id;
                    }
                }
            });
        },
        saveAddress() {
            let url = this.getUrl('store/user-address');

            axios.post(url, this.form).then((response) => {
                if (response.data.error) {
                    toastr.error(response.data.error, this.lang.Error +' !!' );
                } else {
                    toastr.success(response.data.success, this.lang.Success +' !!');
                    this.errors = [];
                    this.getAddress();
                    this.address_area = false;

                    this.form.name = '';
                    this.form.email = '';
                    this.form.phone_no = '';
                    this.form.address = '';
                    this.form.country_id = '';
                    this.form.state_id = '';
                    this.form.city_id = '';
                    this.form.postal_code = '';
                }
            })
                .catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                })
        },
        deleteAddress(id) {
            let url = this.getUrl('delete/user-address/' + id);
            axios.get(url).then((response) => {
                if (response.data.error) {
                    toastr.error(response.data.error, this.lang.Error +' !!' );;
                } else {
                    toastr.success(response.data.success, this.lang.Success +' !!');
                    this.getAddress();
                }
            })
        },
        makeDefault(id, type) {
            let url = this.getUrl('default/user-address/' + type + '/' + id);
            axios.post(url).then((response) => {
                if (response.data.error) {
                    toastr.error(response.data.error, this.lang.Error +' !!' );;
                } else {
                    toastr.success(response.data.success, this.lang.Success +' !!');
                    this.addresses = [];
                    this.$store.dispatch('user', response.data.user);
                    this.default_shipping = response.data.user.shipping_address;
                    this.default_billing = response.data.user.billing_address;
                    this.getAddress();
                }
            })
        },
        edit(address) {
            this.address_area = true;
            this.address_area_title = 'Update Address Info';
            this.address_submit_button = 'Update';

            this.form.name = address.name;
            this.form.email = address.email;
            this.form.phone_no = address.phone_no;
            this.form.address = address.address;
            this.form.country_id = address.address_ids ? address.address_ids.country_id : '';
            this.form.postal_code = address.postal_code;
            this.form.id = address.id;
            this.getStates(address);
            this.$store.commit('setMobileNo',this.form.phone_no);


            const el = this.$refs.update;

            if (el) {
                // Use el.scrollIntoView() to instantly scroll to the element
                el.scrollIntoView({behavior: 'smooth'});
            }
        },
        getNumber(number){
            this.form.phone_no = number;
        }

    }
}
</script>
