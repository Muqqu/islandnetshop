<template>
    <div>
        <section class="sg-convenience home-4 mb-0" id="services-tab" v-if="lengthCounter(services)>0">
            <div class="container">
                <ul class="global-list">
                    <li v-for="(service,index) in services" :key="index">
                        <div class="convenience">
                            <div class="icon">
                                <svg v-if="settings.demo_mode && (service.id === 1)" xmlns="http://www.w3.org/2000/svg"
                                     width="47.5" height="38" viewBox="0 0 47.5 38">
                                    <path id="truck-solid"
                                          d="M1,4V37.25H6a6.274,6.274,0,0,0,12.172,0H31.331a6.274,6.274,0,0,0,12.172,0h5V20.774l-.445-.495-9.5-9.5-.495-.445H29.5V4ZM4.167,7.167H26.333V34.083H18.169A6.274,6.274,0,0,0,6,34.083H4.167ZM29.5,13.5h7.224l8.609,8.609V34.083H43.5a6.274,6.274,0,0,0-12.172,0H29.5Zm-17.417,19a3.167,3.167,0,1,1-3.167,3.167A3.141,3.141,0,0,1,12.083,32.5Zm25.333,0a3.167,3.167,0,1,1-3.167,3.167A3.141,3.141,0,0,1,37.417,32.5Z"
                                          transform="translate(-1 -4)" class="st1"/>
                                </svg>

                                <svg v-else-if="settings.demo_mode && (service.id === 2)" id="return"
                                     xmlns="http://www.w3.org/2000/svg" width="36.533" height="36.533"
                                     viewBox="0 0 36.533 36.533">
                                    <path id="Path_3516" data-name="Path 3516"
                                          d="M1.522,19.758a1.523,1.523,0,0,0,1.HomeCOntr522-1.522A15.177,15.177,0,0,1,18.171,3.044a14.973,14.973,0,0,1,9.805,3.622h-1.4a1.522,1.522,0,1,0,0,3.044h5A1.523,1.523,0,0,0,33.1,8.189V3.172a1.522,1.522,0,1,0-3.044,0V4.44A17.985,17.985,0,0,0,18.171,0,18.224,18.224,0,0,0,0,18.236,1.523,1.523,0,0,0,1.522,19.758Z"
                                          class="st1"/>
                                    <path id="Path_3517" data-name="Path 3517"
                                          d="M69.193,176.32a1.523,1.523,0,0,0-1.522,1.522,15.176,15.176,0,0,1-15.127,15.192,14.972,14.972,0,0,1-9.711-3.539h1.462a1.522,1.522,0,0,0,0-3.044h-5c-.014,0-.026,0-.039,0a1.479,1.479,0,0,0-.173.018c-.043.006-.085.01-.127.02a1.533,1.533,0,0,0-.149.046c-.046.016-.092.03-.136.049s-.081.043-.122.066a1.64,1.64,0,0,0-.14.086c-.011.008-.024.013-.036.022s-.046.044-.07.065a1.194,1.194,0,0,0-.211.223c-.03.039-.059.078-.085.119s-.049.087-.072.131a1.4,1.4,0,0,0-.06.133c-.018.047-.031.094-.044.143s-.025.093-.033.141-.013.108-.017.163c0,.031-.009.062-.009.094v5.017a1.522,1.522,0,1,0,3.044,0v-1.21a17.992,17.992,0,0,0,11.723,4.3,18.224,18.224,0,0,0,18.171-18.236A1.523,1.523,0,0,0,69.193,176.32Z"
                                          transform="translate(-34.182 -159.545)" class="st1"/>
                                </svg>
                                <svg v-else-if="settings.demo_mode && (service.id === 3)" id="credit-card_1_"
                                     data-name="credit-card(1)" xmlns="http://www.w3.org/2000/svg" width="42.848"
                                     height="28.565" viewBox="0 0 42.848 28.565">
                                    <g id="Group_3768" data-name="Group 3768" transform="translate(0 0)">
                                        <g id="Group_3767" data-name="Group 3767" transform="translate(0 0)">
                                            <path id="Path_3521" data-name="Path 3521"
                                                  d="M38.384,85.333H4.463A4.469,4.469,0,0,0,0,89.8v19.638A4.469,4.469,0,0,0,4.463,113.9H38.384a4.469,4.469,0,0,0,4.463-4.463V89.8A4.469,4.469,0,0,0,38.384,85.333Zm2.678,24.1a2.681,2.681,0,0,1-2.678,2.678H4.463a2.681,2.681,0,0,1-2.678-2.678V89.8a2.681,2.681,0,0,1,2.678-2.678H38.384A2.681,2.681,0,0,1,41.062,89.8v19.638Z"
                                                  transform="translate(0 -85.333)" class="st1"/>
                                        </g>
                                    </g>
                                    <g id="Group_3770" data-name="Group 3770" transform="translate(0 5.356)">
                                        <g id="Group_3769" data-name="Group 3769" transform="translate(0 0)">
                                            <path id="Path_3522" data-name="Path 3522"
                                                  d="M41.955,149.333H.893a.893.893,0,0,0-.893.893v5.356a.893.893,0,0,0,.893.893H41.955a.893.893,0,0,0,.893-.893v-5.356A.893.893,0,0,0,41.955,149.333Zm-.893,5.356H1.785v-3.571H41.062v3.571Z"
                                                  transform="translate(0 -149.333)" class="st1"/>
                                        </g>
                                    </g>
                                    <g id="Group_3772" data-name="Group 3772" transform="translate(5.356 17.853)">
                                        <g id="Group_3771" data-name="Group 3771" transform="translate(0 0)">
                                            <path id="Path_3523" data-name="Path 3523"
                                                  d="M75.6,298.667H64.893a.893.893,0,1,0,0,1.785H75.6a.893.893,0,1,0,0-1.785Z"
                                                  transform="translate(-64 -298.667)" class="st1"/>
                                        </g>
                                    </g>
                                    <g id="Group_3774" data-name="Group 3774" transform="translate(5.356 21.424)">
                                        <g id="Group_3773" data-name="Group 3773">
                                            <path id="Path_3524" data-name="Path 3524"
                                                  d="M75.6,341.333H64.893a.893.893,0,1,0,0,1.785H75.6a.893.893,0,0,0,0-1.785Z"
                                                  transform="translate(-64 -341.333)" class="st1"/>
                                        </g>
                                    </g>
                                    <g id="Group_3776" data-name="Group 3776" transform="translate(30.35 16.068)">
                                        <g id="Group_3775" data-name="Group 3775">
                                            <path id="Path_3525" data-name="Path 3525"
                                                  d="M367.13,277.333h-1.785a2.681,2.681,0,0,0-2.678,2.678V281.8a2.681,2.681,0,0,0,2.678,2.678h1.785a2.681,2.681,0,0,0,2.678-2.678v-1.785A2.681,2.681,0,0,0,367.13,277.333Zm.893,4.463a.893.893,0,0,1-.893.893h-1.785a.893.893,0,0,1-.893-.893v-1.785a.893.893,0,0,1,.893-.893h1.785a.893.893,0,0,1,.893.893Z"
                                                  transform="translate(-362.667 -277.333)" class="st1"/>
                                        </g>
                                    </g>
                                </svg>
                                <svg v-else-if="settings.demo_mode && (service.id === 4)" id="chat"
                                     xmlns="http://www.w3.org/2000/svg" width="34.279" height="34.279"
                                     viewBox="0 0 34.279 34.279">
                                    <g id="Group_3764" data-name="Group 3764" transform="translate(0 0)">
                                        <g id="Group_3763" data-name="Group 3763" transform="translate(0 0)">
                                            <path id="Path_3518" data-name="Path 3518"
                                                  d="M31.369,20.526a1.339,1.339,0,0,0-2.483,1l1.577,3.9-4.684-2.065a1.339,1.339,0,0,0-1.066-.006,12.911,12.911,0,0,1-5.1,1.029c-7.169,0-11.984-5.613-11.984-10.855,0-5.986,5.376-10.855,11.984-10.855S31.6,7.548,31.6,13.533a10.92,10.92,0,0,1-.119,1.534,1.339,1.339,0,1,0,2.649.4,13.512,13.512,0,0,0,.149-1.929,12.942,12.942,0,0,0-4.337-9.61A15.218,15.218,0,0,0,19.617,0,15.218,15.218,0,0,0,9.292,3.923a13.014,13.014,0,0,0-4.284,8.458A10.825,10.825,0,0,0,0,21.393,10.389,10.389,0,0,0,2.054,27.6L.1,32.438A1.339,1.339,0,0,0,1.88,34.165l5.639-2.486a12.744,12.744,0,0,0,4.466.8c.033,0,.066,0,.1-.005a12.674,12.674,0,0,0,6.3-1.708,11.527,11.527,0,0,0,4.051-3.945,15.408,15.408,0,0,0,2.782-.773L32.4,29.211a1.339,1.339,0,0,0,1.782-1.727ZM11.985,29.793l-.054,0A10.042,10.042,0,0,1,8.018,29,1.338,1.338,0,0,0,6.953,29L3.817,30.385l1.012-2.5a1.339,1.339,0,0,0-.222-1.37,7.816,7.816,0,0,1-1.928-5.119,7.967,7.967,0,0,1,2.475-5.7,13.517,13.517,0,0,0,3.961,7.239,14.967,14.967,0,0,0,9.742,4.122A9.841,9.841,0,0,1,11.985,29.793Z"
                                                  transform="translate(0 0)" class="st1"/>
                                            <circle id="Ellipse_107" data-name="Ellipse 107" cx="1.5" cy="1.5" r="1.5"
                                                    transform="translate(17.551 12.143)" class="st1"/>
                                            <circle id="Ellipse_108" data-name="Ellipse 108" cx="1.5" cy="1.5" r="1.5"
                                                    transform="translate(23.551 12.143)" class="st1"/>
                                            <circle id="Ellipse_109" data-name="Ellipse 109" cx="1.5" cy="1.5" r="1.5"
                                                    transform="translate(12.551 12.143)" class="st1"/>
                                        </g>
                                    </g>
                                </svg>
                                <img v-else v-lazy="service.service_image" :alt="service.title" class="img-fluid">

                            </div>
                            <div class="text">
                                <span>{{ service.title }}</span>
                                <p>{{ service.sub_tile }}</p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </section>

    </div>
</template>

<script>
import VueSlickCarousel from 'vue-slick-carousel'

export default {
    name: "services",
    components: {VueSlickCarousel},
    computed: {
        services() {
            return this.$store.getters.getServices;
        },
    },
    mounted() {

    },
    methods: {},

    data() {
        return {
        }
    }
}
</script>
