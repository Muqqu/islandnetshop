<template>
    <section class="sg-video-section" v-if="video_shopping.length > 0">
        <div class="container">
            <div class="title justify-content-between">
                <h1>{{ lang.video_shopping }}</h1>
                <router-link :to="{ name: 'video.shopping' }">{{ lang.browse_all_videos }}<span
                    class="icon mdi mdi-name mdi-arrow-right"></span></router-link>
            </div>
            <div class="video-content">
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-3 mb-3" v-for="(video,index) in video_shopping"
                        :key="index">
                        <div class="video--thumb">
                            <div class="video-play--thumb">
                                <router-link :to="{ name : 'video.shopping.details',params : { slug : video.slug } }">
                                    <img :src="video.thumbnail" :alt="video.title" class="img-fluid"></router-link>
                            </div>
                            <div class="video--details">
                                <span class="base" v-if="video.is_live">{{ lang.live }}</span>
                                <p class="video-title">{{ video.title }}</p>
                                <router-link :to="{ name : 'video.shopping.details',params : { slug : video.slug } }"
                                            class="playIcon"><span class="mdi mdi-name mdi-play"></span></router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>


export default {
    name: "viewed_product",
    props: ['video_shopping'],
    data: () => ({}),
    computed: {}
}
</script>
